<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserLessonProgress;

class UserLessonProgressRepository
{
    protected $model;

    public function __construct(UserLessonProgress $model)
    {
        $this->model = $model;
    }

    public function create(int $userId, int $count): UserLessonProgress
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
