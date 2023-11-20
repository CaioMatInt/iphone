<?php

namespace App\Repositories;

use App\Models\Badge;

class BadgeRepository
{
    protected $model;

    public function __construct(Badge $model)
    {
        $this->model = $model;
    }

    public function findBadgeByAchievementThreshold(int $threshold): ?Badge
    {
        return $this->model->where('achievement_threshold', $threshold)->first();
    }
}
