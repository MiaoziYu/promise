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
}