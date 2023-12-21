<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class B_PointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('b_points')->truncate();
        $student_scores = [
            ['student_id' => 'i273050', 'subject_id' => '31', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i273050', 'subject_id' => '32', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i274594', 'subject_id' => '31', 'score' => '2', 'teacher_id' => 'br10'],
            ['student_id' => 'i274594', 'subject_id' => '32', 'score' => null, 'teacher_id' => 'br10'],
        ];
        DB::table('b_points')->insert($student_scores);

    }
}
