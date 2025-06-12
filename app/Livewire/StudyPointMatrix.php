<?php

namespace App\Livewire;

use App\Http\Controllers\StudentController;
use App\Models\StudentScore;
use App\Models\Group;
use App\Traits\SendsNotifications;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Curio\SdClient\Facades\SdApi;
use Livewire\Attributes\Url;

class StudyPointMatrix extends Component
{
    use SendsNotifications;

    public $blok;
    public $groups;
    public $blokken = [];
    public $students;

    #[Url(as: 'group', history: true)]
    public $selectedGroupId = -1;

    public $selectedBlokId = -1;
    public $isSpecialisatieBlok = false;

    #[Url(as: 'specialisatie')]
    public $specialisatieFilter = null;

    public $fbmsActive;
    public $vakkenActiveB;

    public $floodFillValue = -1;
    public $floodFillCount;
    public $floodFillSubject;

    private $selectedCohortId;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount()
    {
        // List available groups
        $groupsFromApi = collect(SdApi::get('/groups'))->map(fn($g) => (object) $g);

        $groups = Group::all()
            ->filter(fn($group) => $groupsFromApi->firstWhere('id', $group->group_id) !== null)
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
        $group = SdApi::get('groups/' . $this->selectedGroupId);

        // List available blokken for this group
        $this->blokken = json_decode(file_get_contents(config('app.currapp.api_url') . '/cohorts/' . $selectedCohortId . '/uitvoeren', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));

        list($this->blok, $this->fbmsActive, $this->students, $this->vakkenActiveB) =
            StudentController::getStudentScoresForBlok(
                $group,
                $selectedCohortId,
                blokId: $this->selectedBlokId,
                vakkenToHide: StudentController::getSpecialisatieVakkenToHide($this->specialisatieFilter)
            );

        $this->selectedBlokId = $this->blok->id;

        // Specialisatie filter hack
        if ($this->selectedBlokId == -1) {
            $this->isSpecialisatieBlok = false;
            return;
        }

        $blokName = $this->blok->blok;

        // Starting from blok E we have specialisatie blokken
        $this->isSpecialisatieBlok = preg_match('/^Blok [E-Z]/', $blokName);
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

    public function updatedSpecialisatieFilter()
    {
        $this->updateStudentScores();
    }

    public function save()
    {
        $this->updateStudentScores();
        $this->dispatch('study-point-matrix-changed');
    }

    public function startFloodFill($feedbackmomentId)
    {
        $studentCount = count($this->students);
        $feedbackmoment = $this->blok->vakken->pluck('feedbackmomenten')->flatten()->firstWhere('id', $feedbackmomentId);

        // Count the students that have a score for this feedbackmoment and wont be affected by the floodfill
        $studentsWithScore = 0;

        foreach($this->students as $student)
        {
            if(isset($student->feedbackmomenten[$feedbackmomentId]))
                $studentsWithScore++;
        }

        $count = $studentCount - $studentsWithScore;

        // TODO: Nice message that tells the user nobody will be affected by the floodfill
        if ($count == 0)
            return;

        $this->floodFillCount = $count;
        $this->floodFillSubject = $feedbackmoment;
        $this->floodFillValue = $feedbackmoment->points;
    }

    public function doFloodFill()
    {
        foreach($this->students as $key => $student)
        {
            if(!isset($student->feedbackmomenten[$this->floodFillSubject->id]))
            {
                $student->feedbackmomenten[$this->floodFillSubject->id] = $this->floodFillValue;
                $this->updatedStudents($this->floodFillValue, $key . '.feedbackmomenten.' . $this->floodFillSubject->id);
            }
        }

        $this->cancelFloodFill();
    }

    public function cancelFloodFill()
    {
        $this->floodFillValue = -1;
        $this->floodFillSubject = null;
        $this->floodFillCount = null;
    }

    public function updatedStudents($value, $key)
    {
        $parts = explode('.', $key);
        $studentKey = $parts[0];
        $student = $this->students[$studentKey];
        $feedbackmomentId = $parts[count($parts) - 1];

        if($value == null)
        {
            $score = StudentScore::where('student_id', $student->id)->where('feedbackmoment_id', $feedbackmomentId)->first();
            $score?->delete();
        }
        else
        {
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
