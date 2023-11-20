<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Beginner',
                'achievement_threshold' => 0,
                'order' => 1,
            ],
            [
                'name' => 'Intermediate',
                'achievement_threshold' => 4,
                'order' => 2,
            ],
            [
                'name' => 'Advanced',
                'achievement_threshold' => 8,
                'order' => 3,
            ],
            [
                'name' => 'Master',
                'achievement_threshold' => 10,
                'order' => 4,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::factory()->create($badge);
        }
    }
}
