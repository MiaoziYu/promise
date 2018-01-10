<?php

namespace Tests\Feature;

use App\User;
use App\UserProfile;
use App\WeeklyChallenge;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WeeklyChallengeTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function can_create_a_weekly_challenge()
    {
        // Act
        $response = $this->post('/api/weekly-challenges/?api_token=' . $this->user->api_token, [
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 10,
            'goal' => 2,
        ]);

        // Assert
        $response->assertStatus(201);

        $weeklyChallenge = $this->user->weeklyChallenges()->first();
        $this->assertEquals('workout at gym', $weeklyChallenge->name);
        $this->assertEquals('at least 2 time', $weeklyChallenge->description);
        $this->assertEquals('10', $weeklyChallenge->credits);
        $this->assertEquals(2, $weeklyChallenge->goal);
    }

    /** @test */
    public function can_view_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 10,
            'goal' => 2,
        ]);

        // Act
        $response = $this->get('/api/weekly-challenges/' . $challenge->id . '?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('workout at gym');
        $response->assertSee('at least 2 time');
        $response->assertSee('10');
        $response->assertSee('2');
    }

    /** @test */
    public function can_view_all_weekly_challenges()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 10,
            'goal' => 2,
        ]);
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'cleaning',
            'description' => 'the apartment',
            'credits' => 15,
            'goal' => 1,
        ]);

        // Act
        $response = $this->get('/api/weekly-challenges/?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('workout at gym');
        $response->assertSee('at least 2 time');
        $response->assertSee('10');
        $response->assertSee('2');
        $response->assertSee('cleaning');
        $response->assertSee('the apartment');
        $response->assertSee('15');
        $response->assertSee('1');
    }

    /** @test */
    public function can_update_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 10,
            'goal' => 2,
        ]);

        // Act
        $response = $this->put('/api/weekly-challenges/' . $challenge->id . '?api_token=' . $this->user->api_token, [
            'name' => 'cleaning',
            'description' => 'the apartment',
            'credits' => 15,
            'goal' => 1,
        ]);

        // Assert
        $response->assertStatus(200);

        $updatedChallenge = $this->user->weeklyChallenges()->findOrFail($challenge->id);
        $this->assertEquals('cleaning', $updatedChallenge->name);
        $this->assertEquals('the apartment', $updatedChallenge->description);
        $this->assertEquals(15, $updatedChallenge->credits);
        $this->assertEquals(1, $updatedChallenge->goal);
    }

    /** @test */
    public function can_delete_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 10,
            'goal' => 2,
        ]);

        // Act
        $response = $this->delete('/api/weekly-challenges/' . $challenge->id . '?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);

        $this->assertNull($this->user->weeklyChallenges()->find($challenge->id));
    }

    /** @test */
    public function can_fail_a_weekly_challenge()
    {
        // Arrange
        factory(WeeklyChallenge::class)->create([
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
        $this->assertEquals(true, $this->user->weeklyChallenges()->first()->failed);

        $this->assertEquals(90, $this->user->userProfile->first()->credits);
    }

    /** @test */
    public function can_check_a_weekly_challenge()
    {
        // Arrange
        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 20,
            'goal' => 2,
            'count' => 0,
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $response = $this->put('/api/weekly-challenges/' . $challenge->id . '/check?api_token=' . $this->user->api_token, []);

        // Assert
        $response->assertStatus(200);

        $this->assertEquals(1, $this->user->weeklyChallenges()->findOrfail($challenge->id)->count);

        $this->assertEquals(110, $this->user->userProfile->first()->credits);
    }

    /** @test */
    public function can_get_bonus_credits_from_a_weekly_challenge()
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

        $this->assertEquals(3, $this->user->weeklyChallenges()->findOrfail($challenge->id)->count);

        $this->assertEquals(120, $this->user->userProfile->first()->credits);
    }

    /** @test */
    public function can_refresh_weekly_challenges_when_new_week_starts()
    {
        // Arrange
        factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'goal' => 2,
            'count' => 2,
        ]);

        // Act
        $this->artisan('challenge:check');

        // Assert
        $challenge = $this->user->weeklyChallenges()->first();

        $this->assertEquals(0, $challenge->count);
        $this->assertEquals(0, $challenge->failed);
    }
}
