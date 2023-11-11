<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'feedbackmoment_id',
        'module_version_id',
        'score',
        'feedback',
        'teacher_id',
        'attempt',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public static function getFeedbackForStudents(array $ids, array $moduleVersionIds, ?array $feedbackMomentIds = null)
    {
        $query = StudentScore::whereIn('module_version_id', $moduleVersionIds)
            ->whereIn('student_id', $ids);

        if ($feedbackMomentIds) {
            $query->whereIn('feedbackmoment_id', $feedbackMomentIds);
        }

        return $query->get();
    }

    public static function updateFeedbackForStudents(array $updatedScores)
    {
        StudentScore::upsert(
            $updatedScores,
            ['student_id', 'module_version_id', 'feedbackmoment_id', 'attempt'],
            ['score', 'feedback', 'teacher_id']
        );
    }
}
