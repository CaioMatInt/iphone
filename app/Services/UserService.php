<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use App\Repositories\BadgeRepository;
use App\Repositories\UserRepository;

class UserService {

    private UserRepository $userRepository;
    private UserAchievementProgressService $userAchievementProgressService;
    private BadgeRepository $badgeRepository;

    public function __construct(
        UserRepository $userRepository,
        UserAchievementProgressService $userAchievementProgressService,
        BadgeRepository $badgeRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->userAchievementProgressService = $userAchievementProgressService;
        $this->badgeRepository = $badgeRepository;
    }

    public function handleUserNewAchievement(Achievement $userNewAchievement, User $user): void
    {
        $this->addNewAchievementAndNotify($user, $userNewAchievement);
        $userAchievementProgress = $this->userAchievementProgressService->createOrUpdateUserAchievementProgress($user->id);

        $userNewBadge = $this->badgeRepository->findBadgeByAchievementThreshold($userAchievementProgress->count);

        if ($userNewBadge) {
            $this->addNewBadgeAndNotify($user, $userNewBadge);
        }
    }

    public function addNewAchievementAndNotify(User $user, Achievement $achievement): void
    {
        $this->userRepository->attachAchievement($user, $achievement->id);
        AchievementUnlocked::dispatch($achievement->name, $user);
    }

    public function addNewBadgeAndNotify(User $user, Badge $badge): void
    {
        $this->userRepository->attachBadge($user, $badge->id);
        BadgeUnlocked::dispatch($badge->name, $user);
    }
}
