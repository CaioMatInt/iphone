<?php

namespace Tests\Feature;

use App\Events\BadgeUnlocked;
use App\Events\LessonWatched;
use App\Events\UserCreated;
use App\Listeners\AwardBeginnerBadgeToCreatedUser;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Repositories\AchievementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserAchievementAndBadgeTest extends TestCase
{
    use RefreshDatabase;

    private AchievementRepository $achievementRepository;
    private Badge $beginnerBadge;
    private Badge $intermediateBadge;
    private Badge $advancedBadge;
    private Badge $masterBadge;
    private array $allBadges;

    private Achievement $oneCommentAchievement;
    private Achievement $threeCommentsAchievement;
    private Achievement $fiveCommentsAchievement;
    private Achievement $tenCommentsAchievement;
    private Achievement $twentyCommentsAchievement;
    private array $allCommentAchievements;

    private Achievement $oneLessonWatchedAchievement;
    private Achievement $fiveLessonsWatchedAchievement;
    private Achievement $tenLessonsWatchedAchievement;
    private Achievement $twentyFiveLessonsWatchedAchievement;
    private Achievement $fiftyLessonsWatchedAchievement;
    private array $allLessonAchievements;

    protected function setUp(): void
    {
        parent::setUp();
        $this->achievementRepository = app(AchievementRepository::class);

        $this->setUpBadges();
        $this->setUpCommentAchievements();
        $this->setUpLessonAchievements();
    }

    private function setUpBadges(): void
    {
        $this->beginnerBadge = Badge::factory()->create(['name' => Badge::BEGINNER, 'achievement_threshold' => 0, 'order' => 1]);
        $this->intermediateBadge = Badge::factory()->create(['name' => Badge::INTERMEDIATE, 'achievement_threshold' => 4, 'order' => 2]);
        $this->advancedBadge = Badge::factory()->create(['name' => Badge::ADVANCED, 'achievement_threshold' => 8, 'order' => 3]);
        $this->masterBadge = Badge::factory()->create(['name' => Badge::MASTER, 'achievement_threshold' => 10, 'order' => 4]);
        $this->allBadges = [$this->beginnerBadge, $this->intermediateBadge, $this->advancedBadge, $this->masterBadge];
    }

    // Would move it to a Trait if I had more time
    private function setUpCommentAchievements(): void
    {
        $achievements = [
            'oneCommentAchievement' => ['threshold' => 1, 'order' => 1, 'name' => 'First Comment Written'],
            'threeCommentsAchievement' => ['threshold' => 3, 'order' => 2, 'name' => '3 Comments Written'],
            'fiveCommentsAchievement' => ['threshold' => 5, 'order' => 3, 'name' => '5 Comments Written'],
            'tenCommentsAchievement' => ['threshold' => 10, 'order' => 4, 'name' => '10 Comments Written'],
            'twentyCommentsAchievement' => ['threshold' => 20, 'order' => 5, 'name' => '20 Comments Written']
        ];

        foreach ($achievements as $varName => $details) {
            $this->$varName = Achievement::factory()->create([
                'type' => Achievement::COMMENT_TYPE,
                'threshold' => $details['threshold'],
                'order' => $details['order'],
                'name' => $details['name'],
            ]);
        }

        $this->allCommentAchievements = [
            $this->oneCommentAchievement,
            $this->threeCommentsAchievement,
            $this->fiveCommentsAchievement,
            $this->tenCommentsAchievement,
            $this->twentyCommentsAchievement
        ];
    }

    // Would move it to a Trait if I had more time
    private function setUpLessonAchievements(): void
    {
        $achievements = [
            'oneLessonWatchedAchievement' => ['threshold' => 1, 'order' => 1, 'name' => 'First Lesson Watched'],
            'fiveLessonsWatchedAchievement' => ['threshold' => 5, 'order' => 2, 'name' => '5 Lessons Watched'],
            'tenLessonsWatchedAchievement' => ['threshold' => 10, 'order' => 3, 'name' => '10 Lessons Watched'],
            'twentyFiveLessonsWatchedAchievement' => ['threshold' => 25, 'order' => 4, 'name' => '25 Lessons Watched'],
            'fiftyLessonsWatchedAchievement' => ['threshold' => 50, 'order' => 5, 'name' => '50 Lessons Watched']
        ];

        foreach ($achievements as $varName => $details) {
            $this->$varName = Achievement::factory()->create([
                'type' => Achievement::LESSON_TYPE,
                'threshold' => $details['threshold'],
                'order' => $details['order'],
                'name' => $details['name'],
            ]);
        }

        $this->allLessonAchievements = [
            $this->oneLessonWatchedAchievement,
            $this->fiveLessonsWatchedAchievement,
            $this->tenLessonsWatchedAchievement,
            $this->twentyFiveLessonsWatchedAchievement,
            $this->fiftyLessonsWatchedAchievement
        ];
    }

    /**
     * @test
     */
    public function should_award_a_created_user_with_beginner_badge(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('user_badge', [
            'user_id' => $user->id,
            'badge_id' => $this->beginnerBadge->id,
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

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $comment->user->id,
            'achievement_id' => $this->oneCommentAchievement->id,
        ]);

        $userAchievementsCount = $comment->user->achievements()->count();
        $this->assertEquals($this->oneCommentAchievement->order, $userAchievementsCount);
    }

    /*
     * @test
     */
    public function should_award_a_user_with_three_written_comment_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->threeCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->threeCommentsAchievement->order, $userAchievementsCount);
    }


    /**
     * @test
     */
    public function should_award_a_user_with_five_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->fiveCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->fiveCommentsAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_ten_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(10)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->tenCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->tenCommentsAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function should_award_a_user_with_twenty_written_comments_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(20)->create(['user_id' => $user->id]);
        // CommentWritten is being dispatched by Eloquent just for testing purposes

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->twentyCommentsAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->twentyCommentsAchievement->order, $userAchievementsCount);
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


        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->oneLessonWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->oneLessonWatchedAchievement->order, $userAchievementsCount);
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

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->fiveLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->fiveLessonsWatchedAchievement->order, $userAchievementsCount);
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

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->tenLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->tenLessonsWatchedAchievement->order, $userAchievementsCount);
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

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->twentyFiveLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->twentyFiveLessonsWatchedAchievement->order, $userAchievementsCount);
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

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $this->fiftyLessonsWatchedAchievement->id,
        ]);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals($this->fiftyLessonsWatchedAchievement->order, $userAchievementsCount);
    }

    /**
     * @test
     */
    public function expects_a_user_that_has_made_one_comment_and_watched_one_lesson_to_have_two_achievements_and_beginner_badge()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        Comment::factory()->create(['user_id' => $user->id]);

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        LessonWatched::dispatch($lesson, $user);

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals(2, $userAchievementsCount);

        $expectedCommentAchievement = $this->oneCommentAchievement;
        $expectedLessonAchievement = $this->oneLessonWatchedAchievement;

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $expectedCommentAchievement->id,
        ]);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $expectedLessonAchievement->id,
        ]);

        $userBadgesCount = $user->badges()->count();
        $this->assertEquals($this->beginnerBadge->order, $userBadgesCount);

        $expectedBadge = $this->beginnerBadge;

        $this->assertDatabaseHas('user_badge', [
            'user_id' => $user->id,
            'badge_id' => $expectedBadge->id,
        ]);
    }

    /**
     * @test
     */
    public function expects_a_user_that_has_made_one_comment_and_watched_10_lessons_to_have_four_achievements_and_intermediate_badge()
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        Comment::factory()->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals(4, $userAchievementsCount);

        $expectedCommentAchievement = $this->oneCommentAchievement;

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $expectedCommentAchievement->id,
        ]);

        $expectedLessonAchievements = [
            $this->oneLessonWatchedAchievement,
            $this->fiveLessonsWatchedAchievement,
            $this->tenLessonsWatchedAchievement,
        ];

        foreach ($expectedLessonAchievements as $achievement) {
            $this->assertDatabaseHas('user_achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
        }

        $userBadgesCount = $user->badges()->count();
        $this->assertEquals($this->intermediateBadge->order, $userBadgesCount);

        $expectedBadges = [
            $this->beginnerBadge,
            $this->intermediateBadge
        ];

        foreach ($expectedBadges as $badge) {
            $this->assertDatabaseHas('user_badge', [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ]);
        }
    }

    /**
     * @test
     */
    public function expects_a_user_that_has_made_20_comments_and_watched_10_lessons_to_have_8_achievements_and_advanced_badge()
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        Comment::factory()->count(20)->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals(8, $userAchievementsCount);

        $expectedCommentAchievements = $this->allCommentAchievements;
        foreach ($expectedCommentAchievements as $achievement) {
            $this->assertDatabaseHas('user_achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
        }

        $expectedLessonAchievements = [
            $this->oneLessonWatchedAchievement,
            $this->fiveLessonsWatchedAchievement,
            $this->tenLessonsWatchedAchievement,
        ];

        foreach ($expectedLessonAchievements as $achievement) {
            $this->assertDatabaseHas('user_achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
        }

        $userBadgesCount = $user->badges()->count();

        $this->assertEquals($this->advancedBadge->order, $userBadgesCount);

        $expectedBadges = [
            $this->beginnerBadge,
            $this->intermediateBadge,
            $this->advancedBadge
        ];

        foreach ($expectedBadges as $badge) {
            $this->assertDatabaseHas('user_badge', [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ]);
        }
    }

    /**
     * @test
     */
    public function expects_a_user_that_has_made_20_comments_and_watched_fifty_lessons_to_have_10_achievements_and_master_badge()
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        Comment::factory()->count(20)->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $userAchievementsCount = $user->achievements()->count();
        $this->assertEquals(10, $userAchievementsCount);

        $expectedCommentAchievements = $this->allCommentAchievements;
        foreach ($expectedCommentAchievements as $achievement) {
            $this->assertDatabaseHas('user_achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
        }

        $expectedLessonAchievements = $this->allLessonAchievements;

        foreach ($expectedLessonAchievements as $achievement) {
            $this->assertDatabaseHas('user_achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
        }

        $userBadgesCount = $user->badges()->count();

        $this->assertEquals($this->masterBadge->order, $userBadgesCount);

        $expectedBadges = [
            $this->beginnerBadge,
            $this->intermediateBadge,
            $this->advancedBadge,
            $this->masterBadge
        ];

        foreach ($expectedBadges as $badge) {
            $this->assertDatabaseHas('user_badge', [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ]);
        }
    }

    /**
     * @test
     */
    public function should_return_200_when_requesting_user_achievements_and_user_id_is_valid(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function should_return_404_when_requesting_user_achievements_and_user_id_is_invalid(): void
    {
        $invalidUserId = User::max('id') + 1;
        $response = $this->get("/users/{$invalidUserId}/achievements");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_structure(): void
    {
        $user = User::factory()->create();
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaining_to_unlock_next_badge'
        ]);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_when_user_has_no_achievements(): void
    {
        $user = User::factory()->create();
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [],
            'current_badge' => $this->beginnerBadge->name,
            'next_badge' => $this->intermediateBadge->name,
            'remaining_to_unlock_next_badge' => 4
        ]);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_when_user_has_one_lesson_and_one_comment()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        Comment::factory()->create(['user_id' => $user->id]);

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        LessonWatched::dispatch($lesson, $user);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJson([
            'unlocked_achievements' => [
                $this->oneCommentAchievement->name,
                $this->oneLessonWatchedAchievement->name,
            ],
            'next_available_achievements' => [
                $this->threeCommentsAchievement->name,
                $this->fiveLessonsWatchedAchievement->name,
            ],
            'current_badge' => $this->beginnerBadge->name,
            'next_badge' => $this->intermediateBadge->name,
            'remaining_to_unlock_next_badge' => 2
        ]);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_when_user_has_10_comments_and_5_lessons()
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        Comment::factory()->count(10)->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJson([
            'unlocked_achievements' => [
                $this->oneCommentAchievement->name,
                $this->threeCommentsAchievement->name,
                $this->fiveCommentsAchievement->name,
                $this->tenCommentsAchievement->name,
                $this->oneLessonWatchedAchievement->name,
                $this->fiveLessonsWatchedAchievement->name,
            ],
            'next_available_achievements' => [
                $this->twentyCommentsAchievement->name,
                $this->tenLessonsWatchedAchievement->name,
            ],
            'current_badge' => $this->intermediateBadge->name,
            'next_badge' => $this->advancedBadge->name,
            'remaining_to_unlock_next_badge' => 2
        ]);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_when_user_has_20_comments_and_10_lessons() {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        Comment::factory()->count(20)->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJson([
            'unlocked_achievements' => [
                $this->oneCommentAchievement->name,
                $this->threeCommentsAchievement->name,
                $this->fiveCommentsAchievement->name,
                $this->tenCommentsAchievement->name,
                $this->twentyCommentsAchievement->name,
                $this->oneLessonWatchedAchievement->name,
                $this->fiveLessonsWatchedAchievement->name,
                $this->tenLessonsWatchedAchievement->name,
            ],
            'next_available_achievements' => [
                $this->twentyFiveLessonsWatchedAchievement->name,
            ],
            'current_badge' => $this->advancedBadge->name,
            'next_badge' => $this->masterBadge->name,
            'remaining_to_unlock_next_badge' => 2
        ]);
    }

    /**
     * @test
     */
    public function validate_user_achievements_response_when_user_has_20_comments_and_50_lessons() {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        Comment::factory()->count(20)->create(['user_id' => $user->id]);

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        // Manually triggering LessonWatched here since I'm not supposed to implement it
        // CommentWritten is being dispatched by Eloquent just for testing purposes
        foreach ($lessons as $lesson) {
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertJson([
            'unlocked_achievements' => [
                $this->oneCommentAchievement->name,
                $this->threeCommentsAchievement->name,
                $this->fiveCommentsAchievement->name,
                $this->tenCommentsAchievement->name,
                $this->twentyCommentsAchievement->name,
                $this->oneLessonWatchedAchievement->name,
                $this->fiveLessonsWatchedAchievement->name,
                $this->tenLessonsWatchedAchievement->name,
                $this->twentyFiveLessonsWatchedAchievement->name,
                $this->fiftyLessonsWatchedAchievement->name,
            ],
            'next_available_achievements' => [],
            'current_badge' => $this->masterBadge->name,
            'next_badge' => null,
            'remaining_to_unlock_next_badge' => null
        ]);
    }
}
