<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('badges')->insert([
            [
                'name' => 'Beginner',
                'achievements_unlocked' => 0,
            ],
            [
                'name' => 'Intermediate',
                'achievements_unlocked' => 4,
            ],
            [
                'name' => 'Advanced',
                'achievements_unlocked' => 8,
            ],
            [
                'name' => 'Master',
                'achievements_unlocked' => 10,
            ],
        ]);
    }
}
