<?php

namespace Tests\Feature;

use App\Checklist;
use App\Promise;
use App\User;
use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromiseTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function can_view_promise_with_checklists()
    {

        // Arrange
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promise->id,
            'text' => 'go to gym'
        ]);

        // Act
        $response = $this->get('/api/promises/' . $promise->id . '?api_token=' . $this->user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('8 kfc hot wings');
        $response->assertSee('go to gym');
    }

    /** @test */
    public function can_view_all_promises_with_checklists()
    {

        // Arrange

        $promiseOne = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promiseOne->id,
            'text' => 'go to gym'
        ]);

        $promiseTwo = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        factory(Checklist::class)->create([
            'promise_id' => $promiseTwo->id,
            'text' => 'read books'
        ]);

        // Act

        $response = $this->get('/api/promises/?api_token=' . $this->user->api_token);

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
        // Arrange
        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        // Act

        $response = $this->get('/api/promises/?finished=false&api_token=' . $this->user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertDontSee('Ice cream');
    }

    /** @test */
    public function can_view_finished_promises()
    {
        // Arrange
        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'finished_at' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        // Act

        $response = $this->get('/api/promises/?finished=true&api_token=' . $this->user->api_token);

        // Assert
        $response->assertDontSee('KFC hot wings');
        $response->assertSee('Ice cream');
    }

    /** @test */
    public function can_not_view_expired_promises()
    {
        // Arrange
        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'expired' => null
        ]);

        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'nachos',
            'description' => 'nachos wont hurt',
            'expired' => 'pending'
        ]);

        factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'Ice cream',
            'description' => 'I want ice cream',
            'expired' => 'true'
        ]);

        // Act

        $response = $this->get('/api/promises/?finished=false&api_token=' . $this->user->api_token);

        // Assert
        $response->assertSee('KFC hot wings');
        $response->assertSee('nachos');
        $response->assertDontSee('Ice cream');
    }

    /** @test */
    public function can_create_promise_with_points_reward()
    {
        // Act
        $postResponse = $this->post('/api/promises?api_token=' . $this->user->api_token, [
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'punch_card_total' => '10',
            'punch_card_finished' => '5',
            'reward_type' => 'points',
            'reward_credits' => '500'
        ]);

        $getResponse = $this->get('/api/promises/?api_token=' . $this->user->api_token);

        // Assertion
        $postResponse->assertStatus(201);
        $getResponse->assertSee('KFC hot wings');
        $getResponse->assertSee('18 kfc hot wings');
        $getResponse->assertSee('points');
        $getResponse->assertSee('500');
    }

    /** @test */
    public function can_create_promise_with_gift_reward()
    {
        // Act
        $response = $this->post('/api/promises?api_token=' . $this->user->api_token, [
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'punch_card_total' => '10',
            'punch_card_finished' => '5',
            'reward_type' => 'gift',
            'reward_name' => 'kfc',
            'reward_image_link' => 'example_link'
        ]);

        // Assertion
        $response->assertStatus(201);

        $promise = $this->user->promises()->first();
        $this->assertEquals('KFC hot wings', $promise->name);
        $this->assertEquals('18 kfc hot wings', $promise->description);
        $this->assertEquals('gift', $promise->reward_type);
        $this->assertEquals('kfc', $promise->reward_name);
        $this->assertEquals('example_link', $promise->reward_image_link);
    }

    /** @test */
    public function can_update_promise()
    {
        // Arrange
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
            'punch_card_total' => 11,
            'punch_card_finished' => 3,
        ]);

        // Act
        $putResponse = $this->put('/api/promises/' . $promise->id . '?api_token=' . $this->user->api_token, [
            'name' => 'KFC sweet wings'
        ]);
        $putResponse->assertStatus(200);

        $getResponse = $this->get('/api/promises/' . $promise->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $getResponse->assertSee('KFC sweet wings');
        $getResponse->assertSee('11');
        $getResponse->assertSee('3');
    }

    /** @test */
    public function can_finish_a_promise_with_credits_reward()
    {

        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'reward_type' => 'points',
            'reward_credits' => 500,
        ]);

        // Act
        $response = $this->put('/api/promises/' . $promise->id . '/finish?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertNotNull($this->user->promises()->find($promise->id)->finished_at);

        $userProfile = $this->user->userProfile()->first();
        $this->assertEquals(600, $userProfile->credits);
    }

    /** @test */
    public function can_finish_a_promise_with_gift_reward()
    {
        // Arrange
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'reward_type' => 'gift',
            'reward_name' => 'kfc hot wings',
            'reward_image_link' => 'example_link',
        ]);

        // Act
        $response = $this->put('/api/promises/' . $promise->id . '/finish?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertNotNull($this->user->promises()->find($promise->id)->finished_at);

        $wishTicket = $this->user->wishTickets()->first();
        $this->assertNotNull($wishTicket);
        $this->assertEquals('kfc hot wings', $wishTicket->name);
        $this->assertEquals('example_link', $wishTicket->image_link);
    }

    /** @test */
    public function can_delete_promise()
    {
        // Arrange
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'name' => 'KFC hot wings',
            'description' => '18 kfc hot wings'
        ]);

        // Act
        $deleteResponse = $this->delete('/api/promises/' . $promise->id . '?api_token=' . $this->user->api_token);
        $deleteResponse->assertStatus(200);

        try {
            $this->get('/api/promises/' . $promise->id . '?api_token=' . $this->user->api_token);
        } catch (ModelNotFoundException $exception) {
            return;
        }

        $this->fail();
    }
    
    /** @test */
    public function promise_with_due_data_can_be_expired()
    {
        // Arrange
        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'reward_credits' => 50,
            'due_date' => Carbon::yesterday(),
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);
        
        // Act
        $response = $this->get('/api/promises/?finished=false&api_token=' . $this->user->api_token);
        
        // Assert
        $response->assertStatus(200);

        $this->assertEquals('pending', $this->user->promises()->findOrFail($promise->id)->expired);

        $this->assertEquals(50, $this->user->userProfile()->first()->credits);
    }

    /** @test */
    public function can_reorder_promises()
    {
        // Arrange
        $promiseOne = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'order' => 1,
        ]);
        $promiseTwo = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'order' => 2
        ]);
        $promiseThree = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'order' => 3,
        ]);

        $data = [
            [
                'id' => $promiseOne->id,
                'order' => 2
            ],
            [
                'id' => $promiseTwo->id,
                'order' => 3
            ],
            [
                'id' => $promiseThree->id,
                'order' => 1
            ],
        ];

        // Act
        $response = $this->post('/api/promises/reorder?api_token=' . $this->user->api_token, $data);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(2, $this->user->promises()->findOrFail($promiseOne->id)->order);
        $this->assertEquals(3, $this->user->promises()->findOrFail($promiseTwo->id)->order);
        $this->assertEquals(1, $this->user->promises()->findOrFail($promiseThree->id)->order);
    }
}
