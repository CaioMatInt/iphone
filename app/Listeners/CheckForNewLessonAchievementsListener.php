<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Repositories\AchievementRepository;
use App\Repositories\UserRepository;
use App\Services\UserLessonProgressService;
use App\Services\UserService;

class CheckForNewLessonAchievementsListener
{

    private UserRepository $userRepository;
    private UserLessonProgressService $userLessonProgressService;
    private AchievementRepository $achievementRepository;
    private UserService $userService;

    /**
     * Create the event listener.
     */
    public function __construct(
        UserRepository $userRepository,
        UserLessonProgressService $userLessonProgressService,
        AchievementRepository $achievementRepository,
        UserService $userService,
    )
    {
        $this->userRepository = $userRepository;
        $this->userLessonProgressService = $userLessonProgressService;
        $this->achievementRepository = $achievementRepository;
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $user = $event->user;
        $lesson = $event->lesson;

        $this->userRepository->attachWatchedLesson($user, $lesson->id, true);

        $userLessonProgress = $this->userLessonProgressService->createOrUpdateUserLessonProgress($user->id);

        $userNewAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::LESSON_TYPE, $userLessonProgress->count);

        if ($userNewAchievement) {
            $this->userService->handleUserNewAchievement($userNewAchievement, $user);
        }
    }
}
