<?php

namespace Tests\Feature;

use App\WishTicket;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WishTicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_view_unclaimed_wish_tickets()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(WishTicket::class)->create([
            'user_id' => $user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);
        factory(WishTicket::class)->create([
            'user_id' => $user->id,
            'name' => 'nachos',
            'claimed_at' => Carbon::now()
        ]);

        // Act
        $response = $this->get('/api/wish-tickets/?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('funny frisch');
        $response->assertSee('example image link');
        $response->assertDontSee('nachos');
    }

    /** @test */
    public function can_claim_a_wish_ticket()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $wishTicket = factory(WishTicket::class)->create([
            'user_id' => $user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        // Act
        $response = $this->put('/api/wish-tickets/' . $wishTicket->id . '/claim?api_token=' . $user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertNotNull($user->wishTickets()->find($wishTicket->id)->claimed_at);
    }

    /** @test */
    public function can_delete_a_wish_ticket()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        $wishTicket = factory(WishTicket::class)->create([
            'user_id' => $user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        // Act
        $response = $this->delete('/api/wish-tickets/' . $wishTicket->id . '?api_token=' . $user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertNull($user->wishTickets()->find($wishTicket->id));
    }
}