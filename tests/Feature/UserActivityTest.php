<?php

namespace Tests\Feature;

use App\Habit;
use App\Promise;
use App\User;
use App\UserProfile;
use App\WeeklyChallenge;
use App\Wish;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserActivityTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    private function createWish($data)
    {
        $wish = factory(Wish::class)->create($data);

        $this->user->wishes()->attach($wish);

        return $wish;
    }

    /** @test */
    public function can_update_user_activities_after_checking_a_habit()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'credits' => 5,
            'count' => 8,
            'streak' => 8
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $activity = $this->user->userActivities()->where('name', 'habit_checked')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($habit->id, $activity->subject_id);
        $this->assertEquals(Habit::class, $activity->subject_type);
        $this->assertEquals(10, $activity->value);
    }

    /** @test */
    public function can_update_user_activities_after_finishing_a_promise()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'credits' => 500,
        ]);

        // Act
        $response = $this->put('/api/promises/' . $promise->id . '/finish?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $activity = $this->user->userActivities()->where('name', 'promise_finished')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($promise->id, $activity->subject_id);
        $this->assertEquals(Promise::class, $activity->subject_type);
        $this->assertEquals(500, $activity->value);
    }

    /** @test */
    public function can_update_user_activities_after_checking_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 20,
            'goal' => 2,
            'count' => 2,
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $response = $this->put('/api/weekly-challenges/' . $challenge->id . '/check?api_token=' . $this->user->api_token, []);

        // Assert
        $response->assertStatus(200);

        $activity = $this->user->userActivities()->where('name', 'weekly_challenge_checked')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($challenge->id, $activity->subject_id);
        $this->assertEquals(WeeklyChallenge::class, $activity->subject_type);
        $this->assertEquals(20, $activity->value);
    }

    /** @test */
    public function can_update_user_activities_after_failing_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 20,
            'goal' => 2,
            'count' => 1,
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $this->artisan('challenge:check');

        // Assert
        $activity = $this->user->userActivities()->where('name', 'weekly_challenge_failed')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($challenge->id, $activity->subject_id);
        $this->assertEquals(WeeklyChallenge::class, $activity->subject_type);
        $this->assertEquals(floor($challenge->credits / 2), $activity->value);
    }

    /** @test */
    public function can_update_user_activities_after_finishing_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 20,
            'goal' => 2,
            'count' => 3,
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $this->artisan('challenge:check');

        // Assert
        $activity = $this->user->userActivities()->where('name', 'weekly_challenge_finished')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($challenge->id, $activity->subject_id);
        $this->assertEquals(WeeklyChallenge::class, $activity->subject_type);
        $this->assertNull($activity->value);
    }

    /** @test */
    public function can_update_user_activities_after_contributing_credits_to_a_shared_wish()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $this->user->api_token, [
            'credits' => 50
        ]);

        // Assertion
        $response->assertStatus(200);

        $activity = $this->user->userActivities()->where('name', 'credits_contributed')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($wish->id, $activity->subject_id);
        $this->assertEquals(Wish::class, $activity->subject_type);
        $this->assertEquals(50, $activity->value);
    }
}
