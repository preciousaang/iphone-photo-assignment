<?php

namespace Database\Seeders;

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
                'number_to_unlock' => 1,
            ],
            [
                'name' => '5 Lessons Watched',
                'type' => 'lesson',
                'number_to_unlock' => 5,
            ],
            [
                'name' => '10 Lessons Watched',
                'type' => 'lesson',
                'number_to_unlock' => 10,
            ],
            [
                'name' => '25 Lessons Watched',
                'type' => 'lesson',
                'number_to_unlock' => 25,
            ],
            [
                'name' => '50 Lessons Watched',
                'type' => 'lesson',
                'number_to_unlock' => 50,
            ],
            [
                'name' => 'First Comment Written',
                'type' => 'comment',
                'number_to_unlock' => 1,
            ],
            [
                'name' => '3 Comments Written',
                'type' => 'comment',
                'number_to_unlock' => 3,
            ],
            [
                'name' => '5 Comments Written',
                'type' => 'comment',
                'number_to_unlock' => 5,
            ],
            [
                'name' => '10 Comments Written',
                'type' => 'comment',
                'number_to_unlock' => 10,
            ],
            [
                'name' => '20 Comments Written',
                'type' => 'comment',
                'number_to_unlock' => 20,
            ],
        ]);
    }
}