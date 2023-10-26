<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'achievements_unlocked'];

    public function nextBadge()
    {
        return Badge::where('achievements_unlocked', '>', $this->achievements_unlocked)->first();
    }

    public function remainingAchievementsToUnlockNextBadge(): int
    {
        $nextBadge = $this->nextBadge();
        if (!$nextBadge) {
            return false;
        }

        return $nextBadge->achievements_unlocked - $this->achievements_unlocked;
    }
}
