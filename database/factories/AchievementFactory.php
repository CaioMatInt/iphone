<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement([Achievement::LESSON_TYPE, Achievement::COMMENT_TYPE]);
        $maxOrderByType = Achievement::where('type', $type)->max('order');

        return [
            'name' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement([Achievement::LESSON_TYPE, Achievement::COMMENT_TYPE]),
            'threshold' => $this->faker->unique()->numberBetween(0, 20),
            'order' => $maxOrderByType ? $maxOrderByType + 1 : 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
