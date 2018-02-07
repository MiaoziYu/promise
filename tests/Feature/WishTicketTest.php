<?php

namespace Tests\Feature;

use App\UserProfile;
use App\Wish;
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

    private function createWish($data)
    {
        $wish = factory(Wish::class)->create($data);

        $this->user->wishes()->attach($wish);

        return $wish;
    }

    private function createWishTicket($wish, $claimedAt = null)
    {
        $wishTicket = $wish->wishTickets()->create([
            'claimed_at' => $claimedAt
        ]);

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

        $wishOne = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        $wishTwo = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'nachos',
            'description' => 'buy nachos',
            'credits' => 200,
            'image_link' =>'another_example_link'
        ]);

        $this->createWishTicket($wishOne);
        $this->createWishTicket($wishTwo, Carbon::now());

        // Act
        $response = $this->get('/api/wish-tickets/?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $response->assertSee('potato chip');
        $response->assertSee('buy a package of potato chip');
        $response->assertSee('500');
        $response->assertSee('example_picture');
        $response->assertDontSee('nachos');
        $response->assertDontSee('buy nachos');
        $response->assertDontSee('200');
        $response->assertDontSee('another_example_link');
    }

    /** @test */
    public function can_view_claimed_wish_tickets()
    {
        // Arrange
        $wishOne = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        $wishTwo = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'nachos',
            'description' => 'buy nachos',
            'credits' => 200,
            'image_link' =>'another_example_link'
        ]);

        $this->createWishTicket($wishOne);
        $this->createWishTicket($wishTwo, Carbon::now());

        // Act
        $response = $this->get('/api/wish-tickets/?claimed=true&api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $response->assertDontSee('potato chip');
        $response->assertDontSee('buy a package of potato chip');
        $response->assertDontSee('500');
        $response->assertDontSee('example_picture');
        $response->assertSee('nachos');
        $response->assertSee('buy nachos');
        $response->assertSee('200');
        $response->assertSee('another_example_link');
    }

    /** @test */
    public function can_claim_a_wish_ticket()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        $wishTicket = $this->createWishTicket($wish);

        $userTwo = factory(User::class)->create();

        $userTwo->wishTickets()->attach($wishTicket);

        // Act
        $response = $this->put('/api/wish-tickets/' . $wishTicket->id . '/claim?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertNotNull($this->user->wishTickets()->find($wishTicket->id)->claimed_at);
        $this->assertNotNull($userTwo->wishTickets()->find($wishTicket->id)->claimed_at);
    }

    /** @test */
    public function can_delete_a_wish_ticket()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        $wishTicket = $this->createWishTicket($wish);

        // Act
        $response = $this->delete('/api/wish-tickets/' . $wishTicket->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertNull($this->user->wishTickets()->find($wishTicket->id));
    }
}
