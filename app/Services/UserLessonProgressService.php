<?php

namespace App\Services;

use App\Models\UserLessonProgress;
use App\Repositories\UserLessonProgressRepository;

class UserLessonProgressService {

    private UserLessonProgressRepository $userLessonProgressRepository;

    public function __construct(UserLessonProgressRepository $userLessonProgressRepository)
    {
        $this->userLessonProgressRepository = $userLessonProgressRepository;
    }

    public function createOrUpdateUserLessonProgress(int $userId): UserLessonProgress
    {
        $userLessonProgress = $this->userLessonProgressRepository->findByUserId($userId);
        if (!$userLessonProgress) {
            return $this->userLessonProgressRepository->create($userId, 1);
        }

        $userLessonProgress->count++;
        $userLessonProgress->save();
        return $userLessonProgress;
    }
}
