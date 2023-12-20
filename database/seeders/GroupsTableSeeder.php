<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->truncate();

        \App\Models\Group::create(['group_id' =>  98, 'cohort_id' =>  1]);
        \App\Models\Group::create(['group_id' => 102, 'cohort_id' => 10]);
        \App\Models\Group::create(['group_id' => 104, 'cohort_id' =>  5]);
    }
}
