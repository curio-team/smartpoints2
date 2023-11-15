<?php

namespace App\Livewire;

use App\Models\StudentScore;
use App\Traits\SendsNotifications;
use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;
use Livewire\Attributes\On;

class StudyPointMatrix extends Component
{
    use SendsNotifications;

    public $groups;
    public $matrix;
    public $matrixes;
    public $students;
    public $selectedBlokKey;
    public $selectedGroupId = -1;
    public $fbmsActive;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount($group = null)
    {
        // $matrixes = json_decode(file_get_contents(config('app.currapp.api_url') . '/feedbackmomenten/active-sorted-by-module', false, stream_context_create([
        //     'http' => [
        //         'method' => 'GET',
        //         'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
        //     ]
        // ])));
        $matrixes = json_decode(file_get_contents(resource_path('test-data/debug-api-result.json')));
        $matrixes = collect($matrixes);

        // Filter the feedbackmomenten to those that happen within the bounds of the weeks of their module
        $matrixes = $matrixes->map(function($blokMatrix) {
            $blokMatrix->vakken = collect($blokMatrix->vakken)
                ->map(function($vak) {
                    $vak->modules = collect($vak->modules)
                        ->map(function($module) {
                            $module->feedbackmomenten = collect($module->feedbackmomenten)
                                ->filter(function($fm) use ($module) {
                                    return $fm->week >= $module->week_start && $fm->week <= $module->week_eind;
                                })
                                ->toArray();
                            return $module;
                        })
                        ->toArray();
                    return $vak;
                })
                ->toArray();
            return $blokMatrix;
        });

        // TODO: clean up spaghetti code
        $matrixes->each(function($blokMatrix) {
            $modules = collect($blokMatrix->vakken)
                ->pluck('modules');
            $blokMatrix->totalFeedbackmomenten = $modules
                ->map(fn($v) => collect($v)
                    ->pluck('feedbackmomenten')
                    ->map(fn($v) => collect($v)
                        ->flatten()
                        ->toArray()
                    )
                    ->flatten()
                    ->toArray()
                )
                ->flatten()
                ->count();
        });
        $this->matrixes = $matrixes;

        // List available groups
        $this->groups = collect(AmoAPI::get('groups'))
            ->filter(function($group) {
                return ($group['type'] == "class");
            })
            ->map(fn($g) => (object)$g);

        // Pick the first block for testing now
        $this->selectedBlokKey = 0;
        $this->updatedSelectedBlokKey($this->selectedBlokKey);

        if($group && is_numeric($group))
        {
            $this->selectedGroupId = $group;
        }
        elseif($group)
        {
            $result = AmoAPI::get('/groups/find/' . $group);
            $this->selectedGroupId = $result['id'];
        }

        $this->getStudentScoresForBlok();
    }

    public function updatedSelectedBlokKey($value)
    {
        $this->matrix = $this->matrixes[$value];
        $this->getStudentScoresForBlok();
    }

    #[On('new-group-id-from-state')] 
    public function updateGroup($id)
    {
        $this->selectedGroupId = $id;
        $this->getStudentScoresForBlok();
    }

    public function updatedSelectedGroupId($value)
    {
        $this->getStudentScoresForBlok();
    }

    public function getStudentScoresForBlok()
    {
        if ($this->selectedGroupId < 0) {
            $this->students = collect();
            return;
        }

        $blokMatrix = $this->matrix;
        $modules = collect($blokMatrix->vakken)
            ->pluck('modules')
            ->flatten();
        $group = AmoAPI::get('groups/'.$this->selectedGroupId);

        $students = collect($group['users']);
        $scores = StudentScore::queryFeedbackForStudents($students->pluck('id')->toArray(), $modules->pluck('version_id')->toArray())->get();
        $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber();

        // Find the fbm's that have results for this group;
        $moments = $modules->map(fn($m) => collect($m->feedbackmomenten))->flatten();
        $studentIds = collect($group['users'])->pluck('id');
        $fbmIds = StudentScore::whereIn('student_id', $studentIds)->distinct()->get()->pluck('feedbackmoment_id')->unique();        
        $fbmsActive = $moments
            ->filter(fn($fm) => $fm->week <= $currentWeek)
            ->filter(fn($fm) => $fbmIds->contains($fm->id));

        $students = $students->map(function($user) use ($group, $scores, $modules, $currentWeek, $moments, $fbmsActive) {
           
            $totalPointsToGain = $moments->sum('points');
            $totalPointsToGainUntilNow = $fbmsActive->sum('points');

            // The sum of all highest scores per feedbackmoment
            $totalPoints = $modules->map(
                fn($m) => collect($m->feedbackmomenten)
                    ->where('week', '<=', $currentWeek)
                    ->flatten()
                    ->map(fn($fm) => $scores->where('student_id', $user['id'])
                        ->where('module_version_id', $m->version_id)
                        ->where('feedbackmoment_id', $fm->id)
                        ->max('score')
                    )
                    ->sum()
            )->sum();

            return (object) [
                'id' => $user['id'],
                'name' => $user['name'],
                'group' => $group['name'],
                'totalPoints' => $totalPoints,
                'totalPointsToGain' => $totalPointsToGain,
                'totalPointsToGainUntilNow' => $totalPointsToGainUntilNow,
                'feedbackmomenten' => $scores->where('student_id', $user['id'])->mapWithKeys(function($score) {
                    return [
                        $score['module_version_id'] . '-' . $score['feedbackmoment_id'] => $score['score']
                    ];
                })->toArray()
            ];
        });

        $this->fbmsActive = $fbmsActive;
        $this->students = $students;
    }

    public function save()
    {
        $this->getStudentScoresForBlok();
        $this->dispatch('study-point-matrix-changed');
    }

    public function updatedStudents($value, $key)
    {
        $parts = explode('.', $key);
        $studentKey = $parts[0];
        $moduleVersionWithFeedbackId = $parts[count($parts) - 1];
        list($moduleVersionId, $feedbackmomentId) = explode('-', $moduleVersionWithFeedbackId);
        $student = $this->students[$studentKey];

        // Write the StudentScore to the database
        $updatedScores = [
            [
                'student_id' => $student->id,
                'module_version_id' => $moduleVersionId,
                'feedbackmoment_id' => $feedbackmomentId,
                'teacher_id' => auth()->user()->id,
                'score' => $value ?: 0
            ],
        ];

        StudentScore::updateFeedbackForStudents($updatedScores);
    }
}
