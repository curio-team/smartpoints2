<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\StudentScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Curio\SdClient\Facades\SdApi;

class StudentController extends Controller
{
    public function show($id = null)
    {
        $studentFromApi = $this->getStudent($id);
        $group = collect($studentFromApi['groups'])->firstWhere('type', 'class');

        if (!$group) {
            return 'Fout! Je bent niet ingedeeld in een klas. Vraag dat jouw mentor dit voor je regelt.';
        }

        $groupId = $group['id'];
        $groupFromApi = SdApi::get('/groups/' . $groupId);
        $cohortId = Group::firstWhere('group_id', $groupId)->cohort_id;

        list($blok, $fbmsActive, $student, $vakkenActiveB) = self::getStudentScoresForBlok($groupFromApi, $cohortId, onlyForUser: $studentFromApi);
        $bPoints = DB::table('student_scores_b')->where('student_id', $studentFromApi['id'])->get();

        return view('student')
            ->with('blok', $blok)
            ->with('bPoints', $bPoints)
            ->with('fbmsActive', $fbmsActive)
            ->with('vakkenActiveB', $vakkenActiveB)
            ->with('student', $student);
    }

    private function getStudent($id = null)
    {
        // For students always return self
        if ($id == null || Auth::user()->type == 'student') return collect(SdApi::get('/me'));

        // Teachers is allowed to look up by id
        return collect(SdApi::get('/users/' . $id));
    }

    public static function getStudentScoresForBlok($group, $cohortId, $onlyForUser = null, $blokId = -1)
    {
        try {
            $response = file_get_contents(config('app.currapp.api_url') . '/cohorts/' . $cohortId . '/uitvoer/' . $blokId, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
                ]
            ]));
        } catch (\ErrorException) {
            abort(404);
        }

        $blok = json_decode($response);

        $blok->vakken = collect($blok->vakken)->filter(function ($vak) {
            return count($vak->feedbackmomenten); //remove vakken without feedbackmoment
        });

        $feedbackmomenten = $blok->vakken->pluck('feedbackmomenten')->flatten();
        $blok->totalFeedbackmomenten = $feedbackmomenten->count();
        // b points are always 2 points per vak
        $blok->totalBpoints = $blok->vakken->count() * 2;


        $students = collect($group['users']);

        if (isset($group['users'][0]['name'])) {
            $students = $students->sortBy(function ($student) {

                $tussenvoegsels = [' S ', ' s ', ' T ', ' t ', ' A ', ' a ', ' Aan ', ' aan ', ' Aan \' t ', ' aan \' t ', ' Aan de ', ' aan de ', ' Aan den ', ' aan den ', ' Aan der ', ' aan der ', ' Aan het ', ' aan het ', ' Aan t ', ' aan t ', ' Af ', ' af ', ' Al ', ' al ', ' Am ', ' am ', ' Am de ', ' am de ', ' Auf ', ' auf ', ' Auf dem ', ' auf dem ', ' Auf den ', ' auf den ', ' Auf der ', ' auf der ', ' Auf ter ', ' auf ter ', ' Aus ', ' aus ', ' Aus ‘m ', ' aus ‘m ', ' Aus dem ', ' aus dem ', ' Aus den ', ' aus den ', ' Aus der ', ' aus der ', ' Aus m ', ' aus m ', ' Ben ', ' ben ', ' Bij ', ' bij ', ' Bij \' t ', ' bij \' t ', ' Bij de ', ' bij de ', ' Bij den ', ' bij den ', ' Bij het ', ' bij het ', ' Bij t ', ' bij t ', ' Bin ', ' bin ', ' Boven d ', ' boven d ', ' Boven d\'  ', ' boven d\'  ', ' D ', ' d ', ' D\'  ', ' d\'  ', ' Da ', ' da ', ' Dal ', ' dal ', ' Dal\'  ', ' dal\'  ', ' Dalla ', ' dalla ', ' Das ', ' das ', ' De ', ' de ', ' De die ', ' de die ', ' De die le ', ' de die le ', ' De l ', ' de l ', ' De l\'  ', ' de l\'  ', ' De la ', ' de la ', ' De las ', ' de las ', ' De le ', ' de le ', ' De van der ', ' de van der ', ' Deca ', ' deca ', ' Degli ', ' degli ', ' Dei ', ' dei ', ' Del ', ' del ', ' Della ', ' della ', ' Den ', ' den ', ' Der ', ' der ', ' Des ', ' des ', ' Di ', ' di ', ' Die le ', ' die le ', ' Do ', ' do ', ' Don ', ' don ', ' Dos ', ' dos ', ' Du ', ' du ', ' El ', ' el ', ' Het ', ' het ', ' I ', ' i ', ' Im ', ' im ', ' In ', ' in ', ' In \' t ', ' in \' t ', ' In de ', ' in de ', ' In den ', ' in den ', ' In der ', ' in der ', ' In het ', ' in het ', ' In t ', ' in t ', ' L ', ' l ', ' L\'  ', ' l\'  ', ' La ', ' la ', ' Las ', ' las ', ' Le ', ' le ', ' Les ', ' les ', ' Lo ', ' lo ', ' Los ', ' los ', ' Of ', ' of ', ' Onder ', ' onder ', ' Onder \' t ', ' onder \' t ', ' Onder de ', ' onder de ', ' Onder den ', ' onder den ', ' Onder het ', ' onder het ', ' Onder t ', ' onder t ', ' Op ', ' op ', ' Op \' t ', ' op \' t ', ' Op de ', ' op de ', ' Op den ', ' op den ', ' Op der ', ' op der ', ' Op gen ', ' op gen ', ' Op het ', ' op het ', ' Op t ', ' op t ', ' Op ten ', ' op ten ', ' Over ', ' over ', ' Over \' t ', ' over \' t ', ' Over de ', ' over de ', ' Over den ', ' over den ', ' Over het ', ' over het ', ' Over t ', ' over t ', ' S ', ' s ', ' S\'  ', ' s\'  ', ' T ', ' t ', ' Te ', ' te ', ' Ten ', ' ten ', ' Ter ', ' ter ', ' Tho ', ' tho ', ' Thoe ', ' thoe ', ' Thor ', ' thor ', ' To ', ' to ', ' Toe ', ' toe ', ' Tot ', ' tot ', ' Uijt ', ' uijt ', ' Uijt \' t ', ' uijt \' t ', ' Uijt de ', ' uijt de ', ' Uijt den ', ' uijt den ', ' Uijt te de ', ' uijt te de ', ' Uijt ten ', ' uijt ten ', ' Uit ', ' uit ', ' Uit \' t ', ' uit \' t ', ' Uit de ', ' uit de ', ' Uit den ', ' uit den ', ' Uit het ', ' uit het ', ' Uit t ', ' uit t ', ' Uit te de ', ' uit te de ', ' Uit ten ', ' uit ten ', ' Unter ', ' unter ', ' Van ', ' van ', ' Van \' t ', ' van \' t ', ' Van de ', ' van De ', ' van de ', ' Van de l ', ' van de l ', ' Van de l\'  ', ' van de l\'  ', ' Van Den ', ' Van den ', ' van den ', ' Van Der ', ' Van der ', ' van der ', ' Van gen ', ' van gen ', ' Van het ', ' van het ', ' Van la ', ' van la ', ' Van t ', ' van t ', ' Van ter ', ' van ter ', ' Van van de ', ' van van de ', ' Ver ', ' ver ', ' Vom ', ' vom ', ' Von ', ' von ', ' Von \' t ', ' von \' t ', ' Von dem ', ' von dem ', ' Von den ', ' von den ', ' Von der ', ' von der ', ' Von t ', ' von t ', ' Voor ', ' voor ', ' Voor \' t ', ' voor \' t ', ' Voor de ', ' voor de ', ' Voor den ', ' voor den ', ' Voor in \' t ', ' voor in \' t ', ' Voor in t ', ' voor in t ', ' Vor ', ' vor ', ' Vor der ', ' vor der ', ' Zu ', ' zu ', ' Zum ', ' zum ', ' Zur ', ' zur'];

                $name = str_replace($tussenvoegsels, " ", $student['name']);
                $parts = explode(" ", $name);
                if (!array_key_exists(1, $parts)) return $parts[0];
                return $parts[1];
            });
        }

        $scores = StudentScore::queryFeedbackForStudents($students->pluck('id')->toArray(), $feedbackmomenten->pluck('id')->toArray())->get();

        // Find the fbm's that have results for this group;
        $studentIds = collect($group['users'])->pluck('id');
        $fbmIds = StudentScore::whereIn('student_id', $studentIds)->distinct()->get()->pluck('feedbackmoment_id')->unique();
        $fbmsActive = $feedbackmomenten
            ->filter(fn ($fm) => $fm->week <= $blok->currentWeek)
            ->filter(fn ($fm) => $fbmIds->contains($fm->id));
        $totalPointsToGainUntilNow = $fbmsActive->sum('points');

        $vakkenActiveB = DB::table('student_scores_b')->whereIn('student_id', $studentIds)->whereIn('subject_id', $blok->vakken->pluck('uitvoer_id'))->select('subject_id')->distinct()->get()->pluck('subject_id');
        $totalBpointsToGainUntilNow = $vakkenActiveB->count() * 2;

        // Only looking op for one user, so now replace list with this one user;
        if ($onlyForUser)
            $students = collect([$onlyForUser]);

        // Pre-compute the scores for each feedback moment up to the current week.
        $feedbackScores = $feedbackmomenten->mapWithKeys(function ($fm) use ($scores) {
            return [$fm->id => $scores->where('feedbackmoment_id', $fm->id)->pluck('score', 'student_id')];
        });

        $students = $students->map(function ($user) use ($blok, $group, $feedbackScores, $totalPointsToGainUntilNow, $totalBpointsToGainUntilNow) {

            // Calculate total points by summing the highest scores for each feedback moment.
            $totalPoints = collect($feedbackScores)->map(function ($scores, $fmId) use ($user) {
                return $scores[$user['id']] ?? 0;
            })->sum();

            // Prepare the scores per feedback moment for the student.
            $feedbackmomentenScores = collect($feedbackScores)->mapWithKeys(function ($scores, $fmId) use ($user) {
                return [$fmId => $scores[$user['id']] ?? null];
            });

            $totalBpoints = DB::table('student_scores_b')->where('student_id', $user['id'])->whereIn('subject_id', $blok->vakken->pluck('uitvoer_id'))->sum('score');

            // new array from totalBPoints with key uitvoer_id from $blok->vakken and value
            // score from student_scores_b when uitvoer_id is equal to subject_id
            $totalBpointsOverview = $blok->vakken->mapWithKeys(function ($vak) use ($user) {
                return [
                    $vak->uitvoer_id => DB::table('student_scores_b')
                        ->where('student_id', $user['id'])
                        ->where('subject_id', $vak->uitvoer_id)
                        ->first()->score ?? ''
                ];
            });

            return (object) [
                'id' => $user['id'],
                'name' => $user['name'],
                'group' => $group['name'],
                'totalPoints' => $totalPoints,
                'totalBpoints' => $totalBpoints,
                'bPointsOverview' => $totalBpointsOverview,
                'totalPointsToGainUntilNow' => $totalPointsToGainUntilNow,
                'totalBpointsToGainUntilNow' => $totalBpointsToGainUntilNow,
                'feedbackmomenten' => $feedbackmomentenScores->toArray()
            ];
        });
        if ($onlyForUser) $students = $students[0];

        return [$blok, $fbmsActive, $students, $vakkenActiveB];
    }
}
