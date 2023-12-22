<?php

namespace App\Livewire\Concerns;

trait CanFloodFill
{
    public $floodFillValue = -1;
    public $floodFillCount;
    public $floodFillSubject;

    public function startFloodFill($feedbackmomentId)
    {
        $studentCount = count($this->students);
        $feedbackmoment = $this->blok->vakken->pluck('feedbackmomenten')->flatten()->firstWhere('id', $feedbackmomentId);

        // Count the students that have a score for this feedbackmoment and wont be affected by the floodfill
        $studentsWithScore = 0;

        foreach($this->students as $student)
        {
            if(isset($student->feedbackmomenten[$feedbackmomentId]['score']))
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
            if(!isset($student->feedbackmomenten[$this->floodFillSubject->id]['score']))
            {
                $student->feedbackmomenten[$this->floodFillSubject->id] = [
                    'attempt' => 1,
                    'score' => $this->floodFillValue,
                ];
                $this->updatedStudents($this->floodFillValue, $key . '.feedbackmomenten.' . $this->floodFillSubject->id . '.score');
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
}
