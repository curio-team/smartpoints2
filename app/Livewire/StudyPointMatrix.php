<?php

namespace App\Livewire;

use App\Http\Controllers\StudentController;
use App\Livewire\Concerns\CanFloodFill;
use App\Livewire\Concerns\CanManageAttempts;
use App\Models\StudentScore;
use App\Models\Group;
use App\Traits\SendsNotifications;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;
use Livewire\Attributes\Url;

class StudyPointMatrix extends Component
{
    use SendsNotifications;
    use CanFloodFill;
    use CanManageAttempts;

    public $blok;
    public $groups;
    public $blokken = [];
    public $students;
    public $changedStudents;

    #[Url(as: 'points', history: true)]
    public $showPoints = 'a';

    #[Url(as: 'group', history: true)]
    public $selectedGroupId = -1;

    public $selectedBlokId = -1;

    public $fbmsActive;

    private $selectedCohortId;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount()
    {
        // List available groups
        $groupsFromApi = collect(AmoAPI::get('/groups'))->map(fn($g) => (object) $g);

        $groups = Group::all()
            ->map(function ($group) use ($groupsFromApi) {
                $group->name = $groupsFromApi->firstWhere('id', $group->group_id)->name;
                return $group;
            })
            ->sortByDesc('name');

        $this->groups = $groups->toArray();
        $this->updateStudentScores();
    }

    private function updateStudentScores()
    {
        if ($this->selectedGroupId == -1) return;

        $selectedCohortId = Group::firstWhere('group_id', $this->selectedGroupId)->cohort_id;
        $group = AmoAPI::get('groups/' . $this->selectedGroupId);

        // List available blokken for this group
        $this->blokken = json_decode(file_get_contents(config('app.currapp.api_url') . '/cohorts/' . $selectedCohortId . '/uitvoeren', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));

        list($this->blok, $this->fbmsActive, $this->students) = StudentController::getStudentScoresForBlok($group, $selectedCohortId, blokId: $this->selectedBlokId);
        $this->selectedBlokId = $this->blok->id;
        $this->changedStudents = [];
    }

    public function updatedSelectedGroupId()
    {
        if ($this->selectedGroupId == -1) return redirect()->route('home');
        $this->selectedBlokId = -1;
        $this->updateStudentScores();
    }

    public function updatedSelectedBlokId()
    {
        $this->updateStudentScores();
    }

    public function updatedStudents($value, $key)
    {
        $parts = explode('.', $key);
        $studentId = $parts[0];
        $type = $parts[1];

        if ($type === 'bPointsOverview') {
            $subjectId = $parts[count($parts) - 1];
        } elseif ($type === 'feedbackmomenten') {
            $subjectId = $parts[count($parts) - 2];
        }

        $this->changedStudents[$studentId] = [
            'type' => $type,
            'subjectId' => $subjectId,
            'score' => $value,
        ];
    }

    public function save()
    {
        foreach($this->changedStudents as $studentKey => $change) {
            $student = $this->students[$studentKey];
            $subjectId = $change['subjectId'];
            $value = $change['score'];

            if($change['type'] == 'bPointsOverview') {
                $this->savePointsB($student, $subjectId, $value);
            } else {
                $this->savePointsA($student, $subjectId, $value);
            }
        }

        $this->changedStudents = [];
    }

    private function savePointsA($student, $feedbackmomentId, $value)
    {
        $attempt = $student->feedbackmomenten[$feedbackmomentId]['attempt'];

        if($value == null)
        {
            $score = StudentScore::where('student_id', $student->id)
                ->where('feedbackmoment_id', $feedbackmomentId)
                ->where('attempt', $attempt)
                ->first();
            $score?->delete();
        }
        else
        {
            $updatedScores = [
                [
                    'student_id' => $student->id,
                    'feedbackmoment_id' => $feedbackmomentId,
                    'teacher_id' => auth()->user()->id,
                    'score' => $value ?: 0,
                    'attempt' => $attempt ?? 1,
                ],
            ];

            StudentScore::updateFeedbackForStudents($updatedScores);
        }
    }

    private function savePointsB($student, $subjectId, $value)
    {
        // TODO: Make a model out of B points...
        $row = DB::table('student_scores_b')
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$row) {
            DB::table('student_scores_b')->insert([
                'student_id' => $student->id,
                'subject_id' => $subjectId,
                'score' => $value ?? 0,
                'teacher_id' => auth()->user()->id,
                'created_at' =>  \Carbon\Carbon::now(), // Not using Eloquent, so need to handle this manually..
                'updated_at' => \Carbon\Carbon::now(),  // Not using Eloquent, so need to handle this manually..
            ]);

            return;
        }

        if ($value === null) {
            // If $value is null, delete the row
            DB::table('student_scores_b')
                ->where('student_id', $student->id)
                ->where('subject_id', $subjectId)
                ->delete();
        } else {
            // If $value is not null, update the score
            DB::table('student_scores_b')
                ->where('student_id', $student->id)
                ->where('subject_id', $subjectId)
                ->update([
                    'score' => $value,
                    'updated_at' => \Carbon\Carbon::now(),  // Not using Eloquent, so need to handle this manually..
                ]);
        }
    }
}
