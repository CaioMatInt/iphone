<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use App\Repositories\BadgeRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService {

    private UserRepository $userRepository;
    private UserAchievementProgressService $userAchievementProgressService;
    private BadgeRepository $badgeRepository;

    public function __construct(
        UserRepository $userRepository,
        UserAchievementProgressService $userAchievementProgressService,
        BadgeRepository $badgeRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->userAchievementProgressService = $userAchievementProgressService;
        $this->badgeRepository = $badgeRepository;
    }

    public function handleUserNewAchievement(Achievement $userNewAchievement, User $user): void
    {
        $this->addNewAchievementAndNotify($user, $userNewAchievement);
        $userAchievementProgress = $this->userAchievementProgressService->createOrUpdateUserAchievementProgress($user->id);

        $userNewBadge = $this->badgeRepository->findBadgeByAchievementThreshold($userAchievementProgress->count);

        if ($userNewBadge) {
            $this->addNewBadgeAndNotify($user, $userNewBadge);
        }
    }

    public function addNewAchievementAndNotify(User $user, Achievement $achievement): void
    {
        $this->userRepository->attachAchievement($user, $achievement->id);
        AchievementUnlocked::dispatch($achievement->name, $user);
    }

    public function addNewBadgeAndNotify(User $user, Badge $badge): void
    {
        $this->userRepository->attachBadge($user, $badge->id);
        BadgeUnlocked::dispatch($badge->name, $user);
    }

    public function getUserAchievements(User $user): Collection
    {
        return $user->achievements()->get();
    }

    public function extractUserAchievementNames(Collection $userAchievements): Collection
    {
        return $userAchievements->pluck('name');
    }

    public function getUserNextAchievements(Collection $userAchievements): array
    {
        $userAchievementsGroupedByType = $userAchievements->groupBy('type');
        $nextAchievements = [];

        foreach ($userAchievementsGroupedByType as $type => $userAchievements) {
            $maxOrderAchievement = $userAchievements->max('order');
            //mover pra repo
            $nextAvailableAchievement = Achievement::select('name')->where('type', $type)->where('order', $maxOrderAchievement + 1)->first();

            if ($nextAvailableAchievement) {
                $nextAchievements[] = $nextAvailableAchievement->name;
            }
        }

        return $nextAchievements;
    }

    public function getBadgeAchievementOverview(User $user, int $userAchievementsCount): array
    {
        $currentBadge = $user->badges()->orderBy('order', 'desc')->first(['name', 'order']);
        $nextBadgeToUnlock = $this->badgeRepository->findNextBadgeByOrder($currentBadge->order);

        return [
            'current_badge' => $currentBadge->name,
            'next_badge' => $nextBadgeToUnlock->name,
            'remaining_to_unlock_next_badge' => $nextBadgeToUnlock->achievement_threshold - $userAchievementsCount
        ];
    }

    public function getUserAchievementsOverview(User $user) {
        $userUnlockedAchievements = $this->getUserAchievements($user);
        $userUnlockedAchievedNames = $this->extractUserAchievementNames($userUnlockedAchievements);

        $userNextAvailableAchievements = $this->getUserNextAchievements($userUnlockedAchievements);
        $badgeAchievementOverview = $this->getBadgeAchievementOverview($user, $userUnlockedAchievements->count());

        return [
            'unlocked_achievements' => $userUnlockedAchievedNames,
            'next_available_achievements' => $userNextAvailableAchievements,
            'current_badge' => $badgeAchievementOverview['current_badge'],
            'next_badge' => $badgeAchievementOverview['next_badge'],
            'remaining_to_unlock_next_badge' => $badgeAchievementOverview['remaining_to_unlock_next_badge'],
        ];
    }
}
