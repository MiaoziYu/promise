<?php

namespace Tests\Feature;

use App\Promise;
use App\User;
use App\UserProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserProfileTest extends TestCase {
    use DatabaseMigrations;

    /** @test */
    public function can_get_user_profile()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $userProfile = factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 650
        ]);

        // Act
        $response = $this->get('/api/profile/' . '?api_token=' . $user->api_token);

        // Assertion
        $response->assertSee($user->name);
        $response->assertSee('650');
    }

    /** @test */
    public function can_update_user_profile()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $userProfile = factory(UserProfile::class)->create([
            'user_id' => $user->id
        ]);

        // Act
        $putResponse = $this->put('/api/profile/' . '?api_token=' . $user->api_token, [
            'name' => 'miaozi'
        ]);
        $getResponse = $this->get('/api/profile/' . '?api_token=' . $user->api_token);

        // Assertion
        $putResponse->assertStatus(200);
        $getResponse->assertSee('miaozi');
    }

    /** @test */
    public function can_update_credits_when_a_promise_finished()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $userProfile = factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 100
        ]);
        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'reward_type' => 'points',
            'reward_credits' => 500,
        ]);

        // Act
        $putResponse = $this->put('/api/promises/' . $promise->id . '?api_token=' . $user->api_token, [
            'finished' => 'true'
        ]);

        // Assertion
        $getResponse = $this->get('/api/profile/' . '?api_token=' . $user->api_token);
        $getResponse->assertSee('600');
    }
}