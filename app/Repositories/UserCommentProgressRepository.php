<?php

namespace App\Repositories;

use App\Models\UserCommentProgress;

class UserCommentProgressRepository
{
    protected $model;

    public function __construct(UserCommentProgress $model)
    {
        $this->model = $model;
    }

    public function create(int $userId, int $count): UserCommentProgress
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
