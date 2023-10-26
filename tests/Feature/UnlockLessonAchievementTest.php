<?php

namespace Tests\Feature;

use App\Events\LessonWatched;
use App\Listeners\HandleWatchedLesson;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnlockLessonAchievementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_watch_lesson_can_be_observed(): void
    {
        Event::fake();
        Event::assertListening(LessonWatched::class, HandleWatchedLesson::class);
    }

    public function test_achievement_is_added_when_lesson_watched_event_is_fired(): void
    {

        $this->seed();
        $user = User::factory()->state([
            'badge_id' => Badge::first()->id,
        ])->create();
        $lesson = Lesson::factory()->create();

        LessonWatched::dispatch($lesson, $user);

        $this->assertTrue($user->lessons()->where('lesson_id', $lesson->id)->exists());
    }
}
