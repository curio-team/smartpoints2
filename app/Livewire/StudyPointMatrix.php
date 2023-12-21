<?php

namespace App\Livewire;

use App\Http\Controllers\StudentController;
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
        })->sortByDesc('name')->toArray();

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

        list($this->blok, $this->fbmsActive, $this->students) = StudentController::getStudentScoresForBlok($group, $this->selectedCohortId);
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
            $score?->delete();
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
