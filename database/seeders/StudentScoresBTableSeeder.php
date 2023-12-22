<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentScoresBTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_scores_b')->truncate();
        $student_scores = [
            ['student_id' => 'i273050', 'subject_id' => '31', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i273050', 'subject_id' => '32', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i274594', 'subject_id' => '31', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i274594', 'subject_id' => '32', 'score' => null, 'teacher_id' => 'br10'],
        ];
        DB::table('student_scores_b')->insert($student_scores);

    }
}
