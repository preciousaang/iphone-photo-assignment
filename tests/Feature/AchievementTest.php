<?php

namespace Tests\Feature;

use App\Events\LessonWatched;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class AchievementTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_first_lesson_watched(): void
    {
        $this->seed();

        $lesson = Lesson::factory()->create();

        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->hasAttached($lesson, ['watched' => true])
            ->create();

        LessonWatched::dispatch($lesson, $user);

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched'],
            'next_available_achievements' => ['First Comment Written', '5 Lessons Watched'],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaing_to_unlock_next_badge' => 4
        ]);
    }
}
