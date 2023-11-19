<?php

namespace App\Livewire;

use App\Models\StudentScore;
use App\Models\Group;
use App\Traits\SendsNotifications;
use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;
use Livewire\Attributes\On;

class StudyPointMatrix extends Component
{
    use SendsNotifications;

    public $blok;
    public $groups;
    public $students;
    public $selectedGroupId = -1;
    public $fbmsActive;
    private $selectedCohortId;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount($group = null)
    {
        // List available groups
        $groupsFromApi = collect(AmoAPI::get('/groups'))->map(fn($g) => (object) $g);
        $this->groups = Group::all()->map(function ($group) use($groupsFromApi) {
            $group->name = $groupsFromApi->firstWhere('id', $group->group_id)->name;
            return $group;
        })->toArray();

        if($group && is_numeric($group))
        {
            $this->selectedGroupId = $group;
            $this->updatedSelectedGroupId($group);
        }
        elseif($group)
        {
            $result = AmoAPI::get('/groups/find/' . $group);
            $this->selectedGroupId = $result['id'];
            $this->updatedSelectedGroupId($result['id']);
        }
    }

    #[On('new-group-id-from-state')] 
    public function updateGroup($id)
    {
        $this->selectedGroupId = $id;
        $this->updatedSelectedGroupId($id);
    }

    public function updatedSelectedGroupId($value)
    {
        $this->selectedCohortId = Group::firstWhere('group_id', $this->selectedGroupId)->cohort_id;
        $group = AmoAPI::get('groups/' . $value);
        $this->getStudentScoresForBlok($group);
    }

    public function getStudentScoresForBlok($group)
    {
        if ($this->selectedGroupId < 0) return;

        $this->blok = json_decode(file_get_contents(config('app.currapp.api_url') . '/cohorts/' . $this->selectedCohortId . '/active-uitvoer', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));
        
        $this->blok->vakken = collect($this->blok->vakken)->filter(function($vak) {
            return count($vak->feedbackmomenten); //remove vakken without feedbackmoment
        });
        $feedbackmomenten = $this->blok->vakken->pluck('feedbackmomenten')->flatten();
        $this->blok->totalFeedbackmomenten = $feedbackmomenten->count();

        $students = collect($group['users'])->sortBy(function ($student){
            $parts = explode(" ", $student['name']);
            return $parts[count($parts) - 1];
        });
        $scores = StudentScore::queryFeedbackForStudents($students->pluck('id')->toArray(), $feedbackmomenten->pluck('id')->toArray())->get();
        $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber();

        // Find the fbm's that have results for this group;
        $studentIds = collect($group['users'])->pluck('id');
        $fbmIds = StudentScore::whereIn('student_id', $studentIds)->distinct()->get()->pluck('feedbackmoment_id')->unique();        
        $fbmsActive = $feedbackmomenten
            ->filter(fn($fm) => $fm->week <= $currentWeek)
            ->filter(fn($fm) => $fbmIds->contains($fm->id));

        $students = $students->map(function($user) use ($group, $scores, $currentWeek, $feedbackmomenten, $fbmsActive) {
           
            $totalPointsToGain = $feedbackmomenten->sum('points');
            $totalPointsToGainUntilNow = $fbmsActive->sum('points');

            // The sum of all highest scores per feedbackmoment
            $totalPoints = $feedbackmomenten
                    ->where('week', '<=', $currentWeek)
                    ->map(fn($fm) => $scores->where('student_id', $user['id'])
                                            ->where('feedbackmoment_id', $fm->id)
                                            ->max('score'))
                    ->sum();

            return (object) [
                'id' => $user['id'],
                'name' => $user['name'],
                'group' => $group['name'],
                'totalPoints' => $totalPoints,
                'totalPointsToGain' => $totalPointsToGain,
                'totalPointsToGainUntilNow' => $totalPointsToGainUntilNow,
                'feedbackmomenten' => $scores->where('student_id', $user['id'])->mapWithKeys(function($score) {
                    return [
                        $score['feedbackmoment_id'] => $score['score']
                    ];
                })->toArray()
            ];
        });

        $this->fbmsActive = $fbmsActive;
        $this->students = $students;
    }

    public function save()
    {
        $this->updatedSelectedGroupId($this->selectedGroupId);
        $this->dispatch('study-point-matrix-changed');
    }

    public function updatedStudents($value, $key)
    {
        $parts = explode('.', $key);
        $studentKey = $parts[0];
        $feedbackmomentId = $parts[count($parts) - 1];
        $student = $this->students[$studentKey];

        if($value == null)
        {
            $score = StudentScore::where('student_id', $student->id)->where('feedbackmoment_id', $feedbackmomentId)->first();
            $score->delete();
        }
        else
        {
            // Write the StudentScore to the database
            $updatedScores = [
                [
                    'student_id' => $student->id,
                    'feedbackmoment_id' => $feedbackmomentId,
                    'teacher_id' => auth()->user()->id,
                    'score' => $value ?: 0
                ],
            ];
            StudentScore::updateFeedbackForStudents($updatedScores);
        }
    }
}
