<?php

namespace Tests\Feature;

use App\User;
use App\UserProfile;
use App\Wish;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WishTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_view_wish()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $wish = factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'nachos',
            'credits' => 500
        ]);

        // Act
        $response = $this->get('/api/wishes/' . $wish->id . '?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('nachos');
        $response->assertSee('500');
    }

    /** @test */
    public function can_view_all_wishes()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'nachos',
            'credits' => 500
        ]);

        factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'potato chip',
            'credits' => 400
        ]);

        // Act
        $response = $this->get('/api/wishes/' . '?api_token=' . $user->api_token);

        // Assertion

        $response->assertStatus(200);
        $response->assertSee('nachos');
        $response->assertSee('potato chip');
        $response->assertSee('500');
        $response->assertSee('400');
    }

    /** @test */
    public function can_create_a_wish()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        // Act
        $postResponse = $this->post('/api/wishes/' . '?api_token=' . $user->api_token, [
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        // Assertion
        $getResponse = $this->get('/api/wishes/?api_token=' . $user->api_token);

        $postResponse->assertStatus(201);
        $getResponse->assertSee('potato chip');
        $getResponse->assertSee('buy a package of potato chip');
        $getResponse->assertSee('500');
        $getResponse->assertSee('example_link');
    }

    /** @test */
    public function can_update_a_wish()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $wish = factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'image_link' =>'example_link'
        ]);

        // Act
        $putResponse = $this->put('/api/wishes/' . $wish->id . '?api_token=' . $user->api_token, [
            'name' => 'nachos',
            'description' => 'buy a nachos',
            'image_link' =>'another_example_link'
        ]);

        // Assertion
        $getResponse = $this->get('/api/wishes/?api_token=' . $wish->id . $user->api_token);

        $putResponse->assertStatus(200);
        $getResponse->assertSee('nachos');
        $getResponse->assertSee('buy a nachos');
        $getResponse->assertSee('another_example_link');
    }

    /** @test */
    public function can_purchase_a_wish()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $userProfile = factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 800
        ]);
        $wish = factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'nachos',
            'credits' => 500
        ]);

        // Act
        $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $user->api_token, []);

        // Assertion
        $getResponse = $this->get('/api/wishes/?api_token=' . $wish->id . $user->api_token);
        $getResponse->assertStatus(200);

        $userProfileGetResponse = $this->get('/api/profile/' . '?api_token=' . $user->api_token);
        $userProfileGetResponse->assertSee('300');
    }
}