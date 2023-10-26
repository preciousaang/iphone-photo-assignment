<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Exception;
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

    protected $with = ['badge'];

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

    public function handleWatchedLesson(Lesson $lesson)
    {
        $this->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
        if ($achievement = $this->hasEarnedAchievment('lesson', $this->lessons()->count())) {
            $this->unlockAchievment($achievement);
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }


    /**
     * unlock an for the user
     * @return void
     */
    public function unlockAchievment(Achievement $achievement)
    {
        $this->achievements()->syncWithoutDetaching($achievement);
    }


    /**
     * check if there is an earned lessen achievement
     * @return \App\Models\Achievement | null
     */
    public function hasEarnedAchievment($type, $count)
    {
        return Achievement::where('type', $type)
            ->where('number_to_achieve', $count)
            ->first();
    }

    /**
     * Review badge eligibility of user
     * @return void
     */
    public function reviewBadgeEligibilty()
    {
        $nextBadge = $this->badge?->nextBage;
        if (!$nextBadge || $this->badge?->remainingToUnlockNextBadge() === 0) {
            return;
        }

        $this->badge_id = $nextBadge->id;
        $this->save();

        BadgeUnlocked::dispatch($this->badge->name, $this);
    }

    public function handleWrittenComment()
    {
        if ($achievement = $this->hasEarnedAchievment('comment', $this->comments()->count())) {
            $this->unlockAchievment($achievement);
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }
}
