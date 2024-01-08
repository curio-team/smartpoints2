<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    use HasFactory;

    protected $table = 'student_scores_a';

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

    public static function queryFeedbackForStudents(array $ids, array $feedbackMomentIds)
    {
        return StudentScore::whereIn('feedbackmoment_id', $feedbackMomentIds)
            ->whereIn('student_id', $ids);
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
