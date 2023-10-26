<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\AchievementUnlocked;
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

    public function achievements()
    {
        return $this->belongsToMany(User::class);
    }

    public function handleWatchedLesson(Lesson $lesson)
    {
        $this->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
        if ($achievment = $this->hasEarnedLessonAchievment()) {
            event(new AchievementUnlocked($achievment->name, $this));
        }
    }

    public function unlockAchievment(string $name)
    {
        $achievement = Achievement::whereName($name)->first();
        if (!$achievement) {
            throw new Exception('Achievement does not exist');
        }
        $this->achievements()->syncWithoutDetaching($achievement);
    }

    public function hasEarnedLessonAchievment()
    {
        return Achievement::where('type', 'lesson')
            ->where('number_to_achieve', $this->watched()->count())
            ->first();
    }

    public function handleWrittenComment(Comment $comment)
    {
    }
}
