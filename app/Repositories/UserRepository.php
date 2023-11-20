<?php

namespace App\Repositories;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function attachWatchedLesson(User $user, int $lessonId, bool $watched): void
    {
        $user->lessons()->attach($lessonId, ['watched' => $watched]);
    }

    public function attachAchievement(User $user, int $achievementId): void
    {
        $user->achievements()->attach($achievementId);
    }

    public function attachBadge(User $user, int $badgeId): void
    {
        $user->badges()->attach($badgeId);
    }
}
