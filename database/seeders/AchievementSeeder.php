<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('achievements')->insert([
            [
                'name' => 'First Lesson Watched',
                'type' => 'lesson',
                'number_to_achieve' => 1
            ],
            [
                'name' => '5 Lessons Watched',
                'type' => 'lesson',
                'number_to_achieve' => 5
            ],
            [
                'name' => '10 Lessons Watched',
                'type' => 'lesson',
                'number_to_achieve' => 10
            ],
            [
                'name' => '25 Lessons Watched',
                'type' => 'lesson',
                'number_to_achieve' => 25
            ],
            [
                'name' => '50 Lessons Watched',
                'type' => 'lesson',
                'number_to_achieve' => 50
            ],
            [
                'name' => 'First Comment Written',
                'type' => 'comment',
                'number_to_achieve' => 1
            ],
            [
                'name' => '5 Comments Watched',
                'type' => 'comment',
                'number_to_achieve' => 3
            ],
            [
                'name' => '10 Comments Watched',
                'type' => 'comment',
                'number_to_achieve' => 5
            ],
            [
                'name' => '25 Comments Watched',
                'type' => 'comment',
                'number_to_achieve' => 10
            ],
            [
                'name' => '50 Comments Watched',
                'type' => 'lesson',
                'number_to_achieve' => 20
            ],
        ]);
    }
}
