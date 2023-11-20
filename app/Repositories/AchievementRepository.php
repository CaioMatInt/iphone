<?php

namespace App\Repositories;

use App\Models\Achievement;
use App\Models\User;

class AchievementRepository
{
    protected $model;

    public function __construct(Achievement $model)
    {
        $this->model = $model;
    }

    public function findByTypeAndThreshold(string $type, int $threshold): ?Achievement
    {
        return $this->model->where('type', $type)->where('threshold', $threshold)->first();
    }
}
