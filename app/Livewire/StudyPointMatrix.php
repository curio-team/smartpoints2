<?php

namespace App\Livewire;

use App\Http\Controllers\StudentController;
use App\Models\Group;
use App\Models\StudentScore;
use App\Traits\SendsNotifications;
use Curio\SdClient\Facades\SdApi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class StudyPointMatrix extends Component
{
    use SendsNotifications;

    #[Locked]
    public $blok;

    #[Locked]
    public $groups;

    #[Locked]
    public $blokken = [];

    // Not locked so scores can be edited, see updatingStudents for what the client may change.
    public $students;

    #[Url(as: 'group', history: true)]
    public $selectedGroupId = -1;

    public $selectedBlokId = -1;

    #[Locked]
    public $isSpecialisatieBlok = false;

    #[Url(as: 'specialisatie')]
    public $specialisatieFilter = null;

    #[Locked]
    public $fbmsActive;

    #[Locked]
    public $vakkenActiveB;

    #[Locked]
    public $floodFillValue = -1;

    #[Locked]
    public $floodFillCount;

    #[Locked]
    public $floodFillSubject;

    public $isAverageGradeView = false;

    public function boot()
    {
        // Runs on every request, including Livewire updates which skip non-persistent route middleware.
        abort_unless(Auth::user()?->isTeacher(), 403);
    }

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount()
    {
        // List available groups
        $groupsFromApi = collect(SdApi::get('/groups'))
            ->map(fn ($g) => (object) $g)
            ->filter(fn ($g) => empty($g->date_end) || ! \Illuminate\Support\Carbon::parse($g->date_end)->endOfDay()->isPast());

        $groups = Group::all()
            ->filter(fn ($group) => $groupsFromApi->firstWhere('id', $group->group_id) !== null)
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
        if ($this->selectedGroupId == -1) {
            return;
        }

        $selectedGroup = Group::firstWhere('group_id', (int) $this->selectedGroupId);
        abort_if($selectedGroup === null, 404);

        $selectedCohortId = $selectedGroup->cohort_id;
        $group = SdApi::get('groups/'.$selectedGroup->group_id);

        $apiUrl = implode('/', [
            rtrim(config('app.currapp.api_url'), '/'),
            'cohorts',
            $selectedCohortId,
            'uitvoeren',
        ]);

        // List available blokken for this group
        $this->blokken = json_decode(
            file_get_contents(
                $apiUrl,
                false,
                stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => 'Authorization: Bearer '.config('app.currapp.api_token'),
                    ],
                ])
            )
        );

        [$this->blok, $this->fbmsActive, $this->students, $this->vakkenActiveB] =
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
        if ($this->selectedGroupId == -1) {
            return redirect()->route('home');
        }
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
        $feedbackmoment = $this->getFeedbackmomenten()->firstWhere('id', $feedbackmomentId);

        if (! $feedbackmoment) {
            return;
        }

        // Count the students that have a score for this feedbackmoment and wont be affected by the floodfill
        $studentsWithScore = 0;

        foreach ($this->students as $student) {
            if (isset($student->feedbackmomenten[$feedbackmomentId])) {
                $studentsWithScore++;
            }
        }

        $count = $studentCount - $studentsWithScore;

        // TODO: Nice message that tells the user nobody will be affected by the floodfill
        if ($count == 0) {
            return;
        }

        $this->floodFillCount = $count;
        $this->floodFillSubject = $feedbackmoment;

        // Convert study points to grade and set the flood fill value
        $this->floodFillValue = $feedbackmoment->points / 10;
    }

    public function doFloodFill()
    {
        if (! $this->floodFillSubject) {
            return;
        }

        foreach ($this->students as $key => $student) {
            if (! isset($student->feedbackmomenten[$this->floodFillSubject->id])) {
                $student->feedbackmomenten[$this->floodFillSubject->id] = $this->floodFillValue;
                $this->updatedStudents($this->floodFillValue, $key.'.feedbackmomenten.'.$this->floodFillSubject->id);
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

    public function updatingStudents($value, $key)
    {
        // Only a single score of a listed student, for a feedbackmoment in this blok, may be changed.
        // Otherwise the client could change student ids (or the whole list) and write arbitrary scores.
        if (
            ! preg_match('/^([^.]+)\.feedbackmomenten\.(\d+)$/', (string) $key, $matches)
            || ! isset($this->students[$matches[1]])
            || ! $this->getFeedbackmomenten()->contains('id', (int) $matches[2])
        ) {
            abort(403);
        }
    }

    public function updatedStudents($value, $key)
    {
        $parts = explode('.', $key);
        $studentKey = $parts[0];
        $student = $this->students[$studentKey];
        $feedbackmomentId = $parts[count($parts) - 1];

        if ($value !== null && $value !== '' && (! is_numeric($value) || $value < 0 || $value > 10)) {
            // Restore the stored score, so the invalid value isn't used when rendering
            $score = StudentScore::where('student_id', $student->id)->where('feedbackmoment_id', $feedbackmomentId)->first();
            $student->feedbackmomenten[$feedbackmomentId] = $score ? $score->score / 10 : null;
            $this->addError('students.'.$key, 'Een cijfer moet een getal tussen 0 en 10 zijn.');

            return;
        }

        if ($value == null) {
            // Livewire doesn't convert empty strings to null, which would break the average calculations
            $student->feedbackmomenten[$feedbackmomentId] = null;

            $score = StudentScore::where('student_id', $student->id)->where('feedbackmoment_id', $feedbackmomentId)->first();
            $score?->delete();
        } else {
            // Convert grade to study points
            $value = $value * 10;

            $updatedScores = [
                [
                    'student_id' => $student->id,
                    'feedbackmoment_id' => $feedbackmomentId,
                    'teacher_id' => Auth::user()->id,
                    'score' => $value ?: 0,
                ],
            ];
            StudentScore::updateFeedbackForStudents($updatedScores);
        }
    }

    private function getFeedbackmomenten()
    {
        return collect($this->blok?->vakken)->pluck('feedbackmomenten')->flatten();
    }

    public function changed()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
    }
}
