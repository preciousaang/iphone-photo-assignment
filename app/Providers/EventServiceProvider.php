<?php

namespace App\Providers;

use App\Events\AchievmentUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\HandleWatchedLesson;
use App\Listeners\HandleWrittenComment;
use App\Listeners\UnlockAchievement;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LessonWatched::class => [
            HandleWatchedLesson::class,
        ],
        CommentWritten::class => [
            HandleWrittenComment::class,
        ],
        AchievmentUnlocked::class => [
            UnlockAchievement::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
