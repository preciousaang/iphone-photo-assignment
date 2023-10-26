<?php

namespace App\Listeners;

use App\Events\LessonWatched;

class HandleWatchedLesson
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
    public function handle(LessonWatched $event): void
    {
        $event->user->handleWatchedLesson($event->lesson);
    }
}
