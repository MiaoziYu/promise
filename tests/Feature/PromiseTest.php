<?php

namespace Tests\Feature;

use App\Checklist;
use App\Promise;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromiseTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_view_promise_with_checklists()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promise->id,
            'text' => 'go to gym'
        ]);

        // Act
        $response = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');
        $response->assertSee('go to gym');
    }

    /** @test */
    public function can_view_all_promises_with_checklists()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promiseOne = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promiseOne->id,
            'text' => 'go to gym'
        ]);

        $promiseTwo = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promiseTwo->id,
            'text' => 'read books'
        ]);

        // Act

        $response = $this->get('/api/promises/?api_token=' . $user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');
        $response->assertSee('go to gym');

        $response->assertSee('Ice cream');
        $response->assertSee('I want ice cream');
        $response->assertSee('2016-12-13');
        $response->assertSee('read books');
    }

    /** @test */
    public function can_view_unfinished_promises()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        // Act

        $response = $this->get('/api/promises/?finished=false&api_token=' . $user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertDontSee('Ice cream');
    }

    /** @test */
    public function can_view_finished_promises()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        // Act

        $response = $this->get('/api/promises/?finished=true&api_token=' . $user->api_token);

        // Assert
        $response->assertDontSee('KFC hot wings');
        $response->assertSee('Ice cream');
    }

    /** @test */
    public function can_create_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        // Act
        $postResponse = $this->post('/api/promises?api_token=' . $user->api_token, [
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'punch_card_total' => '10',
            'punch_card_finished' => '5',
            'reward_type' => 'points',
            'reward_credits' => '500'
        ]);

        $getResponse = $this->get('/api/promises/?api_token=' . $user->api_token);

        // Assertion
        $postResponse->assertStatus(201);
        $getResponse->assertSee('KFC hot wings');
        $getResponse->assertSee('18 kfc hot wings');
        $getResponse->assertSee('points');
        $getResponse->assertSee('500');
    }

    /** @test */
    public function can_update_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'punch_card_total' => 11,
            'punch_card_finished' => 3,
        ]);

        // Act
        $putResponse = $this->put('/api/promises/' . $promise->id . '?api_token=' . $user->api_token, [
            'name' => 'KFC sweet wings'
        ]);
        $putResponse->assertStatus(200);

        $getResponse = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

        // Assertion
        $getResponse->assertSee('KFC sweet wings');
        $getResponse->assertSee('11');
        $getResponse->assertSee('3');
    }

    /** @test */
    public function can_delete_promise()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
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
