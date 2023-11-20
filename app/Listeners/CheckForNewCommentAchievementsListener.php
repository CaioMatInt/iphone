<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Repositories\AchievementRepository;
use App\Repositories\UserRepository;
use App\Services\UserCommentProgressService;
use App\Services\UserService;

class CheckForNewCommentAchievementsListener
{

    private UserRepository $userRepository;
    private UserCommentProgressService $userCommentProgressService;
    private AchievementRepository $achievementRepository;
    private UserService $userService;

    /**
     * Create the event listener.
     */
    public function __construct(
        UserRepository $userRepository,
        UserCommentProgressService $userCommentProgressService,
        AchievementRepository $achievementRepository,
        UserService $userService,
    )
    {
        $this->userRepository = $userRepository;
        $this->userCommentProgressService = $userCommentProgressService;
        $this->achievementRepository = $achievementRepository;
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        $user = $event->comment->user;

        $userCommentProgress = $this->userCommentProgressService->createOrUpdateUserCommentProgress($user->id);

        $userNewAchievement = $this->achievementRepository->findByTypeAndThreshold(Achievement::COMMENT_TYPE, $userCommentProgress->count);

        if ($userNewAchievement) {
            $this->userService->handleUserNewAchievement($userNewAchievement, $user);
        }
    }
}
