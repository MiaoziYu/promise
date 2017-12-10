<?php

namespace Tests\Feature;

use App\Promise;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromisesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'check_list_quantity' => 18,
            'check_list_finished' => 14,
            'finished_at' => null,
        ]);

        // Act

        $response = $this->actingAs($user)->get('/api/promises/' . $promise->id);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');
    }

    /** @test */
    public function user_can_view_all_promises()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'check_list_quantity' => 18,
            'check_list_finished' => 14,
            'finished_at' => null,
        ]);

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'title' => 'Ice cream',
            'description' => 'I want ice cream',
            'check_list_quantity' => 5,
            'check_list_finished' => 1,
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        // Act

        $response = $this->actingAs($user)->get('/api/promises/');

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');

        $response->assertSee('Ice cream');
        $response->assertSee('I want ice cream');
        $response->assertSee('2016-12-13');
    }

    /** @test */
    public function user_can_create_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        // Act
        $postResponse = $this->actingAs($user)->post('/api/promises', [
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'check_list_quantity' => 18,
        ]);
        $postResponse->assertStatus(201);

        $getResponse = $this->actingAs($user)->get('/api/promises/');

        // Assertion
        $getResponse->assertSee('KFC hot wings');
        $getResponse->assertSee('8 kfc hot wings');
    }

    /** @test */
    public function user_can_update_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'check_list_quantity' => 18,
            'check_list_finished' => 14,
            'finished_at' => null,
        ]);

        // Act
        $putResponse = $this->actingAs($user)->put('/api/promises/' . $promise->id, [
            'title' => 'KFC sweet wings',
            'check_list_finished' => 18,
        ]);
        $putResponse->assertStatus(200);

        $getResponse = $this->actingAs($user)->get('/api/promises/' . $promise->id);

        // Assertion
        $getResponse->assertSee('KFC sweet wings');
    }
}
