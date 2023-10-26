<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'achievements' => 0
            ],
            [
                'name' => 'Intermediate',
                'achievements' => 4,
            ],
            [
                'name' => 'Advanced',
                'achievements' => 8
            ],
            [
                'name' => 'Master',
                'achievements' => 10
            ]
        ]);
    }
}