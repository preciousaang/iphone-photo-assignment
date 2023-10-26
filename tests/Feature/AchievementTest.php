<?php

namespace Tests\Feature;

use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * A basic feature test example.
     */
    public function test_first_lesson_watched(): void
    {

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
            'remaining_to_unlock_next_badge' => 3,
        ]);
    }

    public function test_five_lesson_watched(): void
    {
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
            'remaining_to_unlock_next_badge' => 2,
        ]);
    }

    public function test_ten_lessons_watched()
    {
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
            'remaining_to_unlock_next_badge' => 1,
        ]);
    }

    public function test_twenty_five_lessons_watched()
    {
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
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }

    public function test_fifty_lessons_watched()
    {
        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', '25 Lessons Watched', '50 Lessons Watched'],
            'next_available_achievements' => ['First Comment Written'],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaining_to_unlock_next_badge' => 3,
        ]);
    }

    public function test_fifty_lessons_watched_and_a_comment_written()
    {
        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $comment = Comment::factory()->for($user)->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        CommentWritten::dispatch($comment);

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
            ],
            'next_available_achievements' => ['3 Comments Written'],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaining_to_unlock_next_badge' => 2,
        ]);
    }

    public function test_fifty_lessons_watched_and_3_comments_written()
    {
        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        for ($i = 1; $i <= 3; $i++) {
            $comment = Comment::factory()->for($user)->create();
            CommentWritten::dispatch($comment);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
            ],
            'next_available_achievements' => ['5 Comments Written'],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaining_to_unlock_next_badge' => 1,
        ]);
    }

    public function test_fifty_lessons_watched_and_5_comments_written()
    {
        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        for ($i = 1; $i <= 5; $i++) {
            $comment = Comment::factory()->for($user)->create();
            CommentWritten::dispatch($comment);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
            ],
            'next_available_achievements' => ['10 Comments Written'],
            'current_badge' => 'Advanced',
            'next_badge' => 'Master',
            'remaining_to_unlock_next_badge' => 2,
        ]);
    }

    public function test_fifty_lessons_watched_and_10_comments_written()
    {
        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        for ($i = 1; $i <= 10; $i++) {
            $comment = Comment::factory()->for($user)->create();
            CommentWritten::dispatch($comment);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
            ],
            'next_available_achievements' => ['20 Comments Written'],
            'current_badge' => 'Advanced',
            'next_badge' => 'Master',
            'remaining_to_unlock_next_badge' => 1,
        ]);
    }

    public function test_all_achievments_work()
    {

        $user = User::factory()->state(fn () => ['badge_id' => Badge::first()->id])
            ->create();

        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['watched' => true]]);
            LessonWatched::dispatch($lesson, $user);
        }

        for ($i = 1; $i <= 20; $i++) {
            $comment = Comment::factory()->for($user)->create();
            CommentWritten::dispatch($comment);
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
                '20 Comments Written',
            ],
            'next_available_achievements' => [],
            'current_badge' => 'Master',
            'next_badge' => '',
            'remaining_to_unlock_next_badge' => 0,
        ]);
    }
}
