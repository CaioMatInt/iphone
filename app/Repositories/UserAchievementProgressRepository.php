<?php

namespace App\Repositories;

use App\Models\UserAchievementProgress;

class UserAchievementProgressRepository
{
    protected $model;

    public function __construct(UserAchievementProgress $model)
    {
        $this->model = $model;
    }

    public function create(int $userId, int $count): UserAchievementProgress
    {
        return $this->model::create([
            'user_id' => $userId,
            'count' => $count,
        ]);
    }

    public function findByUserId(int $userId)
    {
        return $this->model::where('user_id', $userId)->first();
    }
}
