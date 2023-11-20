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

    public function findBadgeByName(string $name): ?Badge
    {
        return $this->model->where('name', $name)->first();
    }

    public function findNextBadgeByOrder(int $order): ?Badge
    {
        return $this->model->where('order', $order + 1)->first();
    }
}
