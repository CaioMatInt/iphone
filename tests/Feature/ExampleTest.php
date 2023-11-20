<?php

namespace Tests\Feature;

use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Events\UserCreated;
use App\Listeners\AwardBeginnerBadgeToCreatedUser;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Repositories\AchievementRepository;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    private AchievementRepository $achievementRepository;
    /*use RefreshDatabase;*/
    /*run db:seed*/
    /*nome da tabela*/
    /*trtansaction*/

    //setUp function
    protected function setUp(): void
    {
        parent::setUp();
        $this->achievementRepository = app(AchievementRepository::class);
    }



    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        /*$user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);*/
    }

    /**
     * @test
     */
    public function should_award_a_created_user_with_beginner_badge(): void
    {
        $user = User::factory()->create();
        $badge = Badge::where('name', Badge::BEGINNER)->first();

        $this->assertDatabaseHas('user_badge', [
            'user_id' => $user->id,
            'badge_id' => $badge->id,
        ]);
    }

    /**
     * @test
     */
    public function expect_a_created_user_to_have_just_one_badge(): void
    {
        $user = User::factory()->create();
        $userBadges = $user->badges()->get();
        $this->assertCount(1, $userBadges);
    }

    /**
     * @test
     */
    public function should_trigger_badge_unlocked_event_when_a_new_user_is_created(): void
    {
        Event::fake([
            BadgeUnlocked::class
        ]);
        User::factory()->create();
        Event::assertDispatched(BadgeUnlocked::class);
    }

    /**
     * @test
     */
    public function expect_award_beginner_badge_to_created_user_listener_to_be_listening_user_created_event(): void
    {
        Event::fake([
            BadgeUnlocked::class
        ]);
        User::factory()->create();
        Event::assertListening(UserCreated::class, AwardBeginnerBadgeToCreatedUser::class);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_one_written_comment_achievement(): void
    {
        $comment = Comment::factory()->create();
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        $oneCommentAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, 1);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $comment->user->id,
            'achievement_id' => $oneCommentAchievement->id,
        ]);

        $userAchievementsCount = $comment->user->achievements()->count();
        $this->assertEquals($oneCommentAchievement->order, $userAchievementsCount);
    }

    /*
     * @test
     */
    public function should_award_a_user_with_three_written_comment_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        $threeCommentsAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, 3);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $threeCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($threeCommentsAchievement->order, $userAchievementsCount);
    }


    /**
     * @test
     */
    public function should_award_a_user_with_five_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        $fiveCommentsAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, 5);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $fiveCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($fiveCommentsAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_ten_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(10)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        $tenCommentsAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, 10);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $tenCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($tenCommentsAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_twenty_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(20)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        $twentyCommentsAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, 20);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $twentyCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($twentyCommentsAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_one_lesson_watched_achievement(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson->id, ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        LessonWatched::dispatch($lesson, $user);

        $oneLessonWatchedAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, 1);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $oneLessonWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($oneLessonWatchedAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_five_lessons_watched_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $fiveLessonsWatchedAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, 5);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $fiveLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($fiveLessonsWatchedAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_ten_lessons_watched_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $tenLessonsWatchedAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, 10);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $tenLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($tenLessonsWatchedAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_twenty_five_lessons_watched_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $twentyFiveLessonsWatchedAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, 25);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $twentyFiveLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($twentyFiveLessonsWatchedAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_fifty_lessons_watched_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $fiftyLessonsWatchedAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, 50);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $fiftyLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($fiftyLessonsWatchedAchievement->order, $userAchievementsCount);
    }
}
