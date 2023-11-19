<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StudioKaa\Amoclient\Facades\AmoAPI;

class StudentController extends Controller
{
    public function show($id = null)
    {
    }

    public static function getStudentScoresForBlok($group, $cohortId, $onlyForUser = null)
    {
        $blok = json_decode(file_get_contents(config('app.currapp.api_url') . '/cohorts/' . $cohortId . '/active-uitvoer', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));
        
        $blok->vakken = collect($blok->vakken)->filter(function($vak) {
            return count($vak->feedbackmomenten); //remove vakken without feedbackmoment
        });
        $feedbackmomenten = $blok->vakken->pluck('feedbackmomenten')->flatten();
        $blok->totalFeedbackmomenten = $feedbackmomenten->count();

        $students = collect($group['users']);
        if(isset($group['users'][0]['name']))
        {
            $students = $students->sortBy(function ($student){
                $parts = explode(" ", $student['name']);
                return $parts[count($parts) - 1];
            });
        }
        $scores = StudentScore::queryFeedbackForStudents($students->pluck('id')->toArray(), $feedbackmomenten->pluck('id')->toArray())->get();
        $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber();

        // Find the fbm's that have results for this group;
        $studentIds = collect($group['users'])->pluck('id');
        $fbmIds = StudentScore::whereIn('student_id', $studentIds)->distinct()->get()->pluck('feedbackmoment_id')->unique();        
        $fbmsActive = $feedbackmomenten
            ->filter(fn($fm) => $fm->week <= $currentWeek)
            ->filter(fn($fm) => $fbmIds->contains($fm->id));

        $totalPointsToGainUntilNow = $fbmsActive->sum('points');

        // Only looking op for one user, so now replace list with this one user;
        if($onlyForUser) $students = collect([$onlyForUser]);

        $students = $students->map(function($user) use ($group, $scores, $currentWeek, $feedbackmomenten, $totalPointsToGainUntilNow) {
            
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
                'totalPointsToGainUntilNow' => $totalPointsToGainUntilNow,
                'feedbackmomenten' => $scores->where('student_id', $user['id'])->mapWithKeys(function($score) {
                    return [
                        $score['feedbackmoment_id'] => $score['score']
                    ];
                })->toArray()
            ];
        });

        if($onlyForUser) $students = $students[0];
        return [$blok, $fbmsActive, $students];
    }
}
