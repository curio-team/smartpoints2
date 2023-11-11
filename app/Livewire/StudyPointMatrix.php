<?php

namespace App\Livewire;

use App\Models\StudentScore;
use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;

class StudyPointMatrix extends Component
{
    public $groups;
    public $matrix;
    public $matrixes;
    public $students;
    public $selectedBlokKey;
    public $selectedGroupId = -1;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount()
    {
        $matrixes = json_decode(file_get_contents(config('app.currapp.api_url') . '/feedbackmomenten/active-sorted-by-module', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));
        $matrixes = collect($matrixes);

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
            // ->filter(function($group) {
            //     return strtotime($group['date_end']) > time();
            // });
            ->map(fn($g) => (object)$g);

        // Pick the first block for testing now
        $this->selectedBlokKey = 0;
        $this->updatedSelectedBlokKey($this->selectedBlokKey);

        $this->getStudentScoresForBlok();
    }

    public function updatedSelectedBlokKey($value)
    {
        $this->matrix = $this->matrixes[$value];
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
            ->pluck('modules');
        $group = AmoAPI::get('groups/'.$this->selectedGroupId);

        $students = collect($group['users']);
        $scores = StudentScore::getFeedbackForStudents($students->pluck('id')->toArray(), $modules->flatten()->pluck('version_id')->toArray());

        $students = $students->map(function($user) use ($group, $scores) {
                return (object) [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'group' => $group['name'],
                    'feedbackmomenten' => $scores->where('student_id', $user['id'])->mapWithKeys(function($score) {
                        return [
                            $score['module_version_id'] . '-' . $score['feedbackmoment_id'] => $score['score']
                        ];
                    })->toArray()
                ];
            });

        $this->students = $students;
    }

    public function save()
    {
        $this->dispatch('study-point-matrixes-changed');
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
                'score' => $value
            ],
        ];

        StudentScore::updateFeedbackForStudents($updatedScores);
    }
}
