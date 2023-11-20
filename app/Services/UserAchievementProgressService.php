<?php

namespace App\Services;

use App\Models\UserAchievementProgress;
use App\Repositories\UserAchievementProgressRepository;

class UserAchievementProgressService {

    private UserAchievementProgressRepository $userAchievementProgressRepository;

    public function __construct(UserAchievementProgressRepository $userAchievementProgressRepository)
    {
        $this->userAchievementProgressRepository = $userAchievementProgressRepository;
    }

    public function createOrUpdateUserAchievementProgress(int $userId): UserAchievementProgress
    {
        $userAchievementProgress = $this->userAchievementProgressRepository->findByUserId($userId);
        if (!$userAchievementProgress) {
            return $this->userAchievementProgressRepository->create($userId, 1);
        }

        $userAchievementProgress->count++;
        $userAchievementProgress->save();
        return $userAchievementProgress;
    }
}
