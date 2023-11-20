<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Badge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $currentMaxOrder = Badge::max('order');

        return [
            'name' => $this->faker->unique()->word,
            'achievement_threshold' => $this->faker->unique()->numberBetween(0, 20),
            'order' => $currentMaxOrder ? $currentMaxOrder + 1 : 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
