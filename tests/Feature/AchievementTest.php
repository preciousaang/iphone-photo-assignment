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

        //  If the same lesson is dispatch or watched it should not increment or
        LessonWatched::dispatch($lesson, $user);
        LessonWatched::dispatch($lesson, $user);

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched'],
            'next_available_achievements' => ['First Comment Written', '5 Lessons Watched'],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 3
        ]);
    }

    public function test_five_lesson_watched(): void
    {

        $this->seed();


        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(5)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched'],
            'next_available_achievements' => ['First Comment Written', '10 Lessons Watched'],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 2
        ]);
    }

    public function test_ten_lessons_watched()
    {
        $this->seed();


        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(10)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched'],
            'next_available_achievements' => ['First Comment Written', '25 Lessons Watched'],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 1
        ]);
    }

    public function test_twenty_five_lessons_watched()
    {
        $this->seed();


        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(25)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', '25 Lessons Watched'],
            'next_available_achievements' => ['First Comment Written', '50 Lessons Watched'],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaining_to_unlock_next_badge' => 4
        ]);
    }
}