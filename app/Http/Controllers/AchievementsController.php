<?php

namespace App\Http\Controllers;

use App\Models\User;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json([
            'unlocked_achievements' => $user->getAchievementNames(),
            'next_available_achievements' => $user->nextAvailableAchievements(),
            'current_badge' => $user->badge?->name ?? '',
            'next_badge' => $user->nextBadge(),
            'remaining_to_unlock_next_badge' => $user->remainingAchievementsToUnlockNextBadge(),
        ]);
    }
}
