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

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function can_view_a_habit()
    {
        // Arrange
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'name' => 'learn german',
            'description' => 'every day',
            'credits' => 5,
        ]);

        // Act
        $response = $this->get('/api/habits/' . $habit->id .  '?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('learn german');
        $response->assertSee('every day');
        $response->assertSee('5');
    }

    /** @test */
    public function can_view_habits()
    {
        // Arrange
        factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'name' => 'learn german',
            'description' => 'every day',
            'credits' => 5,
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'name' => 'workout',
            'description' => 'for one hour',
            'credits' => 10,
        ]);

        // Act
        $response = $this->get('/api/habits?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('learn german');
        $response->assertSee('every day');
        $response->assertSee('workout');
        $response->assertSee('for one hour');
        $response->assertSee('5');
        $response->assertSee('10');

        $habits = $this->user->habits()->get();
        $this->assertEquals(2, count($habits));
    }

    /** @test */
    public function can_create_a_habit()
    {
        // Act
        $response = $this->post('/api/habits/?api_token=' . $this->user->api_token, [
            'name' => 'workout',
            'description' => 'for one hour',
            'credits' => 10,
        ]);

        // Assertion
        $habit = $this->user->habits()->first();
        $this->assertEquals('workout', $habit->name);
        $this->assertEquals('for one hour', $habit->description);
        $this->assertEquals(10, $habit->credits);
    }

    /** @test */
    public function can_update_a_habit()
    {
        // Arrange
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'name' => 'learn german',
            'description' => 'every day',
            'credits' => 5,
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id .  '?api_token=' . $this->user->api_token, [
            'name' => 'learn english',
            'description' => 'every second day',
            'credits' => 10
        ]);

        // Assert
        $response->assertStatus(200);

        $updatedHabit = $this->user->habits()->findOrFail($habit->id);
        $this->assertEquals('learn english', $updatedHabit->name);
        $this->assertEquals('every second day', $updatedHabit->description);
        $this->assertEquals('10', $updatedHabit->credits);

    }

    /** @test */
    public function can_check_a_habit()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'credits' => 5,
            'count' => 0,
            'streak' => 0
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $updatedHabit = $this->user->habits()->findOrfail($habit->id);
        $this->assertEquals(1, $updatedHabit->count);
        $this->assertEquals(1, $updatedHabit->streak);
        $this->assertNotNull($updatedHabit->checked_at);

        $this->assertEquals(5, $this->user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_streak_a_habit()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 0
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'streak' => 6,
            'checked_at' => Carbon::today()->subHours(8)
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(7, $this->user->habits()->findOrfail($habit->id)->streak);

        $this->assertEquals(10, $this->user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_fail_a_habit_streak_at_the_beginning_of_a_day()
    {
        // Arrange
        factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'streak' => 7,
            'checked_at' => Carbon::yesterday()->subHours(1)
        ]);

        // Act
        $this->artisan('habits:check');

        // Assert
        $this->assertEquals(0, $this->user->habits()->first()->streak);

    }

    /** @test */
    public function can_only_check_a_habit_once_per_day()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 5
        ]);
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'count' => 3,
            'streak' => 3,
            'checked_at' => Carbon::today()
        ]);

        // Act
        $response = $this->put('/api/habits/' . $habit->id . '/check?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(422);

        $updatedHabit = $this->user->habits()->findOrfail($habit->id);
        $this->assertEquals(3, $updatedHabit->streak);
        $this->assertEquals(3, $updatedHabit->count);

        $this->assertEquals(5, $this->user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_delete_a_habit()
    {
        // Arrange
        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'count' => 3,
            'streak' => 3,
            'checked_at' => Carbon::today()
        ]);

        // Act
        $response = $this->delete('/api/habits/' . $habit->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);
        $this->assertNull($this->user->habits()->find($habit->id));
    }

    /** @test */
    public function can_reorder_habits()
    {
        // Arrange
        $habitOne = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'order' => 1,
        ]);
        $habitTwo = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'order' => 2
        ]);
        $habitThree = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'order' => 3,
        ]);

        $data = [
            [
                'id' => $habitOne->id,
                'order' => 2
            ],
            [
                'id' => $habitTwo->id,
                'order' => 3
            ],
            [
                'id' => $habitThree->id,
                'order' => 1
            ],
        ];

        // Act
        $response = $this->put('/api/habits/reorder?api_token=' . $this->user->api_token, $data);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(2, $this->user->habits()->findOrFail($habitOne->id)->order);
        $this->assertEquals(3, $this->user->habits()->findOrFail($habitTwo->id)->order);
        $this->assertEquals(1, $this->user->habits()->findOrFail($habitThree->id)->order);
    }
}
