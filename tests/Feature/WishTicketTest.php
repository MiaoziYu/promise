<?php

namespace Tests\Feature;

use App\UserProfile;
use App\WishTicket;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WishTicketTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    private function createWishTicket($data)
    {
        $wishTicket = factory(WishTicket::class)->create($data);

        $this->user->wishTickets()->attach($wishTicket);

        return $wishTicket;
    }

    /** @test */
    public function can_view_unclaimed_wish_tickets()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 800,
            'picture' => 'example_picture'
        ]);

        $this->createWishTicket([
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        $this->createWishTicket([
            'name' => 'nachos',
            'claimed_at' => Carbon::now()
        ]);

        // Act
        $response = $this->get('/api/wish-tickets/?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('funny frisch');
        $response->assertSee('example_picture');
        $response->assertDontSee('nachos');
    }

    /** @test */
    public function can_view_claimed_wish_tickets()
    {
        // Arrange
        $this->createWishTicket([
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        $this->createWishTicket([
            'name' => 'nachos',
            'claimed_at' => Carbon::now()
        ]);

        // Act
        $response = $this->get('/api/wish-tickets/?claimed=true&api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertDontSee('funny frisch');
        $response->assertSee('nachos');
    }

    /** @test */
    public function can_claim_a_wish_ticket()
    {
        // Arrange
        $wishTicket = $this->createWishTicket([
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        // Act
        $response = $this->put('/api/wish-tickets/' . $wishTicket->id . '/claim?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertNotNull($this->user->wishTickets()->find($wishTicket->id)->claimed_at);
    }

    /** @test */
    public function can_delete_a_wish_ticket()
    {
        // Arrange
        $wishTicket = $this->createWishTicket([
            'name' => 'funny frisch',
            'image_link' => 'example image link'
        ]);

        // Act
        $response = $this->delete('/api/wish-tickets/' . $wishTicket->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertNull($this->user->wishTickets()->find($wishTicket->id));
    }
}
