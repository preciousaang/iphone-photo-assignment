<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;

class UnlockAchievement
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        //
        $event->user->unlockAchievment($event->achievement_name);
    }
}
