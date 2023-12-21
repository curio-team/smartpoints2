<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SchoolWeeksTableSeeder::class,
            GroupsTableSeeder::class,
            StudentScoresTableSeeder::class,
            B_PointsTableSeeder::class,
        ]);
    }
}
