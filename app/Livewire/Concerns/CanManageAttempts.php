<?php

namespace App\Livewire\Concerns;

use App\Models\StudentScore;

trait CanManageAttempts
{
    public $manageAttempts = [];
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

    public function doManageAttempts()
    {
        // Go through $this->manageAttempts and update the scores
        foreach ($this->manageAttempts as $id => $attempt) {
            StudentScore::find($id)->update([
                'attempt' => $attempt['attempt'],
                'score' => $attempt['score'],
            ]);
        }

        $this->cancelManageAttempts();
        $this->updateStudentScores();
        $this->dispatch('study-point-matrix-changed');
    }

    public function cancelManageAttempts()
    {
        $this->manageAttempts = [];
        $this->manageAttemptsStudent = null;
        $this->manageAttemptsFeedbackmoment = null;
    }
}
