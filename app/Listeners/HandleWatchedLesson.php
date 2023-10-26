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
        //
        // 1. Mark that lesson as watched for the user
        // 2. Count the lessons watched
        // 3. Get the users next available achievements

        $event->user->handleWatchedLesson($event->lesson);
    }
}
