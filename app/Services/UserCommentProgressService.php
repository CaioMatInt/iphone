<?php

namespace App\Services;

use App\Models\UserCommentProgress;
use App\Repositories\UserCommentProgressRepository;

class UserCommentProgressService {

    private UserCommentProgressRepository $userCommentProgressRepository;

    public function __construct(UserCommentProgressRepository $userCommentProgressRepository)
    {
        $this->userCommentProgressRepository = $userCommentProgressRepository;
    }

    public function createOrUpdateUserCommentProgress(int $userId): UserCommentProgress {
        $userCommentProgress = $this->userCommentProgressRepository->findByUserId($userId);
        if (!$userCommentProgress) {
            return $this->userCommentProgressRepository->create($userId, 1);
        }

        $userCommentProgress->count++;
        $userCommentProgress->save();
        return $userCommentProgress;
    }
}
