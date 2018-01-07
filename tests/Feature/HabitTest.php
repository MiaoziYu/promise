<?php

namespace Tests\Feature;

use App\Habit;
use App\User;
use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_view_habits()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(Habit::class)->create([
            'user_id' => $user->id,
            'name' => 'learn german',
            'description' => 'every day',
            'credits' => 5,
        ]);

        factory(Habit::class)->create([
            'user_id' => $user->id,
            'name' => 'workout',
            'description' => 'for one hour',
            'credits' => 10,
        ]);

        // Act
        $response = $this->get('/api/habits?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('learn german');
        $response->assertSee('every day');
        $response->assertSee('workout');
        $response->assertSee('for one hour');
        $response->assertSee('5');
        $response->assertSee('10');

        $habits = $user->habits()->get();
        $this->assertEquals(2, count($habits));
    }

    /** @test */
    public function can_create_a_habit()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        // Act
        $response = $this->post('/api/habits/?api_token=' . $user->api_token, [
            'name' => 'workout',
            'description' => 'for one hour',
            'credits' => 10,
        ]);

        // Assertion
        $habit = $user->habits()->first();
        $this->assertEquals('workout', $habit->name);
        $this->assertEquals('for one hour', $habit->description);
        $this->assertEquals(10, $habit->credits);
    }

    /** @test */
    public function can_check_a_habit()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $user->id,
            'credits' => 5,
            'count' => 0,
            'streak' => 0
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);

        $updatedHabit = $user->habits()->findOrfail($habit->id);
        $this->assertEquals(1, $updatedHabit->count);
        $this->assertEquals(1, $updatedHabit->streak);
        $this->assertNotNull($updatedHabit->checked_at);

        $this->assertEquals(5, $user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_streak_a_habit()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $user->id,
            'streak' => 7,
            'checked_at' => Carbon::today()->subHours(8)
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(8, $user->habits()->findOrfail($habit->id)->streak);

        $this->assertEquals(10, $user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_fail_a_habit_streak()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $user->id,
            'streak' => 7,
            'checked_at' => Carbon::yesterday()->subHours(1)
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(0, $user->habits()->findOrfail($habit->id)->streak);

        $this->assertEquals(5, $user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_only_check_a_habit_once_per_day()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 5
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $user->id,
            'count' => 3,
            'streak' => 3,
            'checked_at' => Carbon::today()
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(422);

        $updatedHabit = $user->habits()->findOrfail($habit->id);
        $this->assertEquals(3, $updatedHabit->streak);
        $this->assertEquals(3, $updatedHabit->count);

        $this->assertEquals(5, $user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_delete_a_habit()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $habit = factory(Habit::class)->create([
            'user_id' => $user->id,
            'count' => 3,
            'streak' => 3,
            'checked_at' => Carbon::today()
        ]);

        // Act
        $response = $this->delete('/api/habits/' . $habit->id . '?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);
        $this->assertNull($user->habits()->find($habit->id));
    }
}