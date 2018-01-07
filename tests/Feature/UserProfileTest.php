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
        $userOne = factory(User::class)->create([
            'name' => 'mao mao',
        ]);
        $userTwo = factory(User::class)->create([
            'name' => 'bearzk',
        ]);
        $userProfileOne = factory(UserProfile::class)->create([
            'user_id' => $userOne->id,
            'credits' => 650
        ]);
        $userProfileTwo = factory(UserProfile::class)->create([
            'user_id' => $userTwo->id,
            'credits' => 400
        ]);

        // Act
        $responseOne = $this->get('/api/profile/' . '?api_token=' . $userOne->api_token);
        $responseTwo = $this->get('/api/profile/' . '?api_token=' . $userOne->api_token);

        // Assertion
        $responseOne->assertSee($userOne->name);
        $responseOne->assertSee('650');

        $responseTwo->assertSee($userTwo->name);
        $responseTwo->assertSee('400');
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