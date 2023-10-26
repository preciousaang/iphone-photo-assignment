<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->count(10)->create();

        $lessons = Lesson::factory()
            ->count(20)
            ->create();

        Achievement::factory()->count(10)->state(function () {
            return [
                'next' => Achievement::inRandomOrder()->first()?->id,
            ];
        })->create();
    }
}
