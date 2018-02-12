<?php

namespace Tests\Feature;

use App\User;
use App\UserProfile;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    /** @test */
    public function can_get_user_profile()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 650,
            'picture' => 'picture'
        ]);

        // Act
        $response = $this->get('/api/profile/' . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $response->assertSee('650');
        $response->assertSee('picture');
    }

    /** @test */
    public function can_update_user_profile()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'picture' => 'picture'
        ]);

        // Act
        $response = $this->put('/api/profile/' . '?api_token=' . $this->user->api_token, [
            'name' => 'miaozi',
            'picture' => 'another_picture'
        ]);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals('miaozi', User::findOrFail($this->user->id)->name);
        $this->assertEquals('another_picture', $this->user->userProfile->picture);
    }
}
