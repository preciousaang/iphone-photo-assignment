<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'badge_id',
        'email',
        'password',
    ];

    protected $with = ['badge', 'achievements'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * User's current badge
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * The unlocked achievments
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class);
    }

    /*
    * Get the next available names
    */
    public function nextAvailableAchievements()
    {

        return Achievement::selectRaw('min(number_to_unlock) as number_to_number_to_unlock, name, type')
            ->whereNotIn('id', $this->achievements()->pluck('achievements.id')->all())
            ->groupBy(['type'])
            ->orderBy('number_to_unlock')
            ->limit(2)
            ->pluck('name')->all();
    }

    public function handleWatchedLesson(Lesson $lesson)
    {
        if ($this->hasWatchedLesson($lesson)) {
            return;
        }

        $this->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);

        if ($achievement = $this->hasEarnedAchievment('lesson', $this->lessons()->count())) {
            $this->unlockAchievement($achievement);
            $this->reviewBadgeEligibilty();
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }

    public function hasWatchedLesson(Lesson $lesson)
    {
        return $this->achievements()->where('achievements.id', $lesson->id)->exists();
    }

    /**
     * unlock an achievement for the user
     *
     * @return void
     */
    public function unlockAchievement(Achievement $achievement)
    {
        $this->achievements()->syncWithoutDetaching($achievement);
    }

    /**
     * check if there is an earned lessen achievement
     *
     *  @param  string  $type
     *  @param  int  $count
     *  @return \App\Models\Achievement | null
     */
    public function hasEarnedAchievment($type, $count)
    {
        return Achievement::where('type', $type)
            ->where('number_to_unlock', $count)
            ->first();
    }

    /**
     * Review badge eligibility of user
     *
     * @return void
     */
    public function reviewBadgeEligibilty()
    {
        $nextBadge = $this->badge?->nextBadge();
        if (! $nextBadge || $this->badge?->remainingAchievementsToUnlockNextBadge($this->achievements()->count()) !== 0) {
            return;
        }

        $this->badge()->associate($nextBadge);
        $this->save();

        BadgeUnlocked::dispatch($nextBadge->name, $this);
    }

    public function nextBadge()
    {
        return $this->badge?->nextBadge()?->name ?? '';
    }

    public function remainingAchievementsToUnlockNextBadge()
    {
        return (int) $this->badge->remainingAchievementsToUnlockNextBadge($this->achievements()->count());
    }

    public function handleWrittenComment()
    {
        if ($achievement = $this->hasEarnedAchievment('comment', $this->comments()->count())) {
            $this->unlockAchievement($achievement);
            $this->reviewBadgeEligibilty();
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }

    public function getAchievementNames()
    {
        return $this->achievements()->pluck('achievements.name')->all();
    }
}