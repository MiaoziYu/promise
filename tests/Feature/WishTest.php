<?php

namespace Tests\Feature;

use App\User;
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
            'name' => 'nachos'
        ]);

        // Act
        $response = $this->get('/api/wishes/' . $wish->id . '?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('nachos');
    }

    /** @test */
    public function can_view_all_wishes()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'nachos'
        ]);

        factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'potato chip'
        ]);

        // Act
        $response = $this->get('/api/wishes/' . '?api_token=' . $user->api_token);

        // Assertion

        $response->assertStatus(200);
        $response->assertSee('nachos');
        $response->assertSee('potato chip');
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
            'image_link' =>'example_link'
        ]);

        // Assertion
        $getResponse = $this->get('/api/wishes/?api_token=' . $user->api_token);

        $postResponse->assertStatus(201);
        $getResponse->assertSee('potato chip');
        $getResponse->assertSee('buy a package of potato chip');
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
        $getResponse = $this->get('/api/wishes/?api_token=' . $user->api_token);

        $putResponse->assertStatus(200);
        $getResponse->assertSee('nachos');
        $getResponse->assertSee('buy a nachos');
        $getResponse->assertSee('another_example_link');
    }
}