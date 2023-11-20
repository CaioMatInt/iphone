<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Badge;
use App\Services\UserService;

class AwardBeginnerBadgeToCreatedUser
{

    private UserService $userService;

    /**
     * Create the event listener.
     */
    public function __construct(
        UserService $userService
    )
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $beginnerBadge = Badge::where('name', Badge::BEGINNER)->first();
        $this->userService->addNewBadgeAndNotify($event->user, $beginnerBadge);
    }
}
