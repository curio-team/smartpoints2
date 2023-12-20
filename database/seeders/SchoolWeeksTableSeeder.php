<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolWeeksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('school_weeks')->truncate();

        $weeks = [

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 1,
                'date_of_monday' => '2023-09-04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 2,
                'date_of_monday' => '2023-09-11',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 3,
                'date_of_monday' => '2023-09-18',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 4,
                'date_of_monday' => '2023-09-25',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 5,
                'date_of_monday' => '2023-10-02',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 6,
                'date_of_monday' => '2023-10-09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 6,
            //     'date_of_monday' => '2023-10-16',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 7,
                'date_of_monday' => '2023-10-23',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 8,
                'date_of_monday' => '2023-10-30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 9,
                'date_of_monday' => '2023-11-06',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 10,
                'date_of_monday' => '2023-11-13',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 10,
            //     'date_of_monday' => '2023-11-20',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 11,
                'date_of_monday' => '2023-11-27',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 12,
                'date_of_monday' => '2023-12-04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 13,
                'date_of_monday' => '2023-12-11',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 14,
                'date_of_monday' => '2023-12-18',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 14,
            //     'date_of_monday' => '2023-12-25',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 14,
            //     'date_of_monday' => '2024-01-01',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 15,
                'date_of_monday' => '2024-01-08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'year_start' => 2023,
                'year_end' => 2024,
                'week_number' => 16,
                'date_of_monday' => '2024-01-15',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 16,
            //     'date_of_monday' => '2024-01-22',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            // [
            //     'year_start' => 2023,
            //     'year_end' => 2024,
            //     'week_number' => 16,
            //     'date_of_monday' => '2024-01-29',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

        ];

        DB::table('school_weeks')->insert($weeks);
    }
}
