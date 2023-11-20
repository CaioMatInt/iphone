<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getAllMockedAchievements() as $achievement) {
            Achievement::factory()->create($achievement);
        }
    }

    private function getAllMockedAchievements(): array
    {
        return array_merge(
            $this->getLessonAchievements(),
            $this->getCommentAchievements()
        );
    }

    private function getLessonAchievements(): array
    {
        return [
            [
                'name' => 'First lesson watched',
                'type' => Achievement::LESSON,
                'threshold' => 1,
                'order' => 1,
            ],
            [
                'name' => '5 lessons watched',
                'type' => Achievement::LESSON,
                'threshold' => 5,
                'order' => 2,
            ],
            [
                'name' => '10 lessons watched',
                'type' => Achievement::LESSON,
                'threshold' => 10,
                'order' => 3,
            ],
            [
                'name' => '25 lessons watched',
                'type' => Achievement::LESSON,
                'threshold' => 25,
                'order' => 4,
            ],
            [
                'name' => '50 lessons watched',
                'type' => Achievement::LESSON,
                'threshold' => 50,
                'order' => 5,
            ]
        ];
    }

    private function getCommentAchievements(): array
    {
        return [
            [
                'name' => 'First comment written',
                'type' => Achievement::COMMENT,
                'threshold' => 1,
                'order' => 1,
            ],
            [
                'name' => '5 comments written',
                'type' => Achievement::COMMENT,
                'threshold' => 5,
                'order' => 2,
            ],
            [
                'name' => '10 comments written',
                'type' => Achievement::COMMENT,
                'threshold' => 10,
                'order' => 3,
            ],
            [
                'name' => '25 comments written',
                'type' => Achievement::COMMENT,
                'threshold' => 25,
                'order' => 4,
            ],
            [
                'name' => '50 comments written',
                'type' => Achievement::COMMENT,
                'threshold' => 50,
                'order' => 5,
            ]
        ];
    }
}
