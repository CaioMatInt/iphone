<?php

namespace Database\Seeders;

use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const SHOULD_ADD_TESTING_DATA = true;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BadgeSeeder::class,
            AchievementSeeder::class,
        ]);

        if (self::SHOULD_ADD_TESTING_DATA) {
            $this->setUpLocalTestingData();
        }
    }

    private function setUpLocalTestingData()
    {
        $users = User::factory()->count(5)->create();
        $fakeLessons = Lesson::factory()->count(50)->create();

        foreach ($users as $user) {
            $totalRandomLessonsOptions = [1, 5, 10, 15, 25, 35, 50];
            $randomNumberOfWatchedLessons = array_rand($totalRandomLessonsOptions);
            $randomNumberOfWatchedLessons = $totalRandomLessonsOptions[$randomNumberOfWatchedLessons];

            $randomLessons = $fakeLessons->random($randomNumberOfWatchedLessons);
            $user->lessons()->attach($randomLessons, ['watched' => true]);

            // Manually forcing the event to fire instead of defining to call it automatically in the model, since
            // when a new lesson with watched === 0 is created, I shouldn't fire it.
            foreach($user->lessons as $lesson) {
                LessonWatched::dispatch($lesson, $user);
            }

            $totalRandomCommentsOptions = [1, 3, 5, 10, 15, 20, 25];
            $randomNumberOfComments = array_rand($totalRandomCommentsOptions);
            $randomNumberOfComments = $totalRandomCommentsOptions[$randomNumberOfComments];

            Comment::factory()->count($randomNumberOfComments)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
