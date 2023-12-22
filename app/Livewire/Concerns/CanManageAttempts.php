<?php

namespace App\Livewire\Concerns;

use App\Models\StudentScore;

trait CanManageAttempts
{
    public $manageAttempts = [];
    public $manageAttemptsNew = -1;
    public $manageAttemptsStudent;
    public $manageAttemptsFeedbackmoment;

    public function startManageAttempts($studentId, $feedbackmomentId)
    {
        $student = $this->students->firstWhere('id', $studentId);
        $feedbackmoment = $this->blok->vakken->pluck('feedbackmomenten')->flatten()->firstWhere('id', $feedbackmomentId);

        $this->manageAttempts = StudentScore::where('student_id', $studentId)
            ->where('feedbackmoment_id', $feedbackmomentId)
            ->get()
            ->mapWithKeys(function ($score) {
                return [
                    $score->id => [
                        'attempt' => $score->attempt,
                        'score' => $score->score,
                    ]
                ];
            })
            ->toArray();
        $this->manageAttemptsStudent = $student;
        $this->manageAttemptsFeedbackmoment = $feedbackmoment;
    }

    public function removeAttempt($scoreId)
    {
        StudentScore::find($scoreId)->delete();
        unset($this->manageAttempts[$scoreId]);
    }

    public function doManageAttempts()
    {
        $highestAttempt = 1;
        foreach ($this->manageAttempts as $id => $attempt) {
            StudentScore::find($id)->update([
                'attempt' => $attempt['attempt'],
                'score' => $attempt['score'],
            ]);

            if ($highestAttempt < $attempt['attempt']) {
                $highestAttempt = $attempt['attempt'];
            }
        }

        if ($this->manageAttemptsNew > -1) {
            $updatedScores = [
                [
                    'student_id' => $this->manageAttemptsStudent->id,
                    'feedbackmoment_id' => $this->manageAttemptsFeedbackmoment->id,
                    'teacher_id' => auth()->user()->id,
                    'score' => $this->manageAttemptsNew ?: 0,
                    'attempt' => $highestAttempt + 1,
                ],
            ];

            StudentScore::updateFeedbackForStudents($updatedScores);
            $this->manageAttemptsNew = -1;
        }

        $this->cancelManageAttempts();
    }

    public function cancelManageAttempts()
    {
        $this->manageAttempts = [];
        $this->manageAttemptsStudent = null;
        $this->manageAttemptsFeedbackmoment = null;
        $this->manageAttemptsNew = -1;
    }

    public function addNewAttempt()
    {
        $this->manageAttemptsNew = 0;
    }

    public function removeNewAttempt()
    {
        $this->manageAttemptsNew = -1;
    }
}
