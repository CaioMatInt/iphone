<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Badge;
use App\Repositories\BadgeRepository;
use App\Services\UserService;

class AwardBeginnerBadgeToCreatedUser
{

    private UserService $userService;
    private BadgeRepository $badgeRepository;

    /**
     * Create the event listener.
     */
    public function __construct(
        UserService $userService,
        BadgeRepository $badgeRepository,
    )
    {
        $this->userService = $userService;
        $this->badgeRepository = $badgeRepository;
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $beginnerBadge = $this->badgeRepository->findBadgeByName(Badge::BEGINNER);

        if ($beginnerBadge) {
            $this->userService->addNewBadgeAndNotify($event->user, $beginnerBadge);
        }
    }
}
