<?php

namespace Tests\Feature;

use App\Promise;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $response = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

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

        $response = $this->get('/api/promises/?api_token=' . $user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');

        $response->assertSee('Ice cream');
        $response->assertSee('I want ice cream');
        $response->assertSee('2016-12-13');
    }

    /** @test */
    public function user_can_view_unfinished_promises()
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

        $response = $this->get('/api/promises/?finished=false&api_token=' . $user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertDontSee('Ice cream');
    }

    /** @test */
    public function user_can_view_finished_promises()
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

        $response = $this->get('/api/promises/?finished=true&api_token=' . $user->api_token);

        // Assert
        $response->assertDontSee('KFC hot wings');
        $response->assertSee('Ice cream');
    }

    /** @test */
    public function user_can_create_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        // Act
        $postResponse = $this->post('/api/promises?api_token=' . $user->api_token, [
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'check_list_quantity' => 18,
        ]);
        $postResponse->assertStatus(201);

        $getResponse = $this->get('/api/promises/?api_token=' . $user->api_token);

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
        $putResponse = $this->put('/api/promises/' . $promise->id . '?api_token=' . $user->api_token, [
            'title' => 'KFC sweet wings'
        ]);
        $putResponse->assertStatus(200);

        $getResponse = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

        // Assertion
        $getResponse->assertSee('KFC sweet wings');
    }

    /** @test */
    public function user_can_delete_promise()
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
        $deleteResponse = $this->delete('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);
        $deleteResponse->assertStatus(200);

        try {
            $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);
        } catch (ModelNotFoundException $exception) {
            return;
        }

        $this->fail();
    }
}
