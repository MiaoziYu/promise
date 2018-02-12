<?php

namespace Tests\Feature;

use App\Checklist;
use App\Promise;
use App\User;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    /** @test */
    public function can_create_checklist()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promise = factory(Promise::class)->create([
            'user_id' => $user->id
        ]);

        // Act
        $response = $this->post('/api/promises/' . $promise->id . '/checklists/'. '?api_token=' . $user->api_token, [
            'text' => 'go to gym'
        ]);

        // Assert
        $response->assertStatus(201);
    }

    /** @test */
    public function can_update_checklist()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        $checklist = factory(Checklist::class)->create([
            'promise_id' => $promise->id,
            'text' => 'go to gym'
        ]);

        // Act
        $putResponse = $this->put('/api/promises/' . $promise->id . '/checklists/' . $checklist->id . '?api_token=' . $user->api_token, [
            'text' => 'read books',
            'status' => true
        ]);
        $getResponse = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

        // Assert
        $putResponse->assertStatus(200);
        $getResponse->assertSee('read books');
        $getResponse->assertDontSee('go to gym');
    }

    /** @test */
    public function can_delete_checklist()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();

        $promise = factory(Promise::class)->create([
            'user_id' => $user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        $checklist = factory(Checklist::class)->create([
            'promise_id' => $promise->id,
            'text' => 'go to gym'
        ]);

        // Act
        $putResponse = $this->delete('/api/promises/' . $promise->id . '/checklists/' . $checklist->id . '?api_token=' . $user->api_token);
        $getResponse = $this->get('/api/promises/' . $promise->id . '?api_token=' . $user->api_token);

        // Assert
        $putResponse->assertStatus(200);
        $getResponse->assertDontSee('go to gym');
    }
}
