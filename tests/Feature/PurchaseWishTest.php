<?php

namespace Tests\Feature;

use App\User;
use App\UserProfile;
use App\Wish;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseWishTest extends TestCase
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

    /** @test */
    public function can_purchase_a_wish()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 800
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link',
            'credits' => 500
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(300, $this->user->userProfile->credits);

        $wishTicket = $this->user->wishTickets()->first();
        $this->assertEquals('funny frisch', $wishTicket->name);
        $this->assertEquals('example image link', $wishTicket->image_link);
    }

    /** @test */
    public function cannot_purchase_a_wish_when_credits_are_not_enough()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link',
            'credits' => 500
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(422);

        $this->assertEquals(100, $this->user->userProfile->credits);

        $this->assertEquals(0, count($this->user->wishTickets()->get()));
    }
}
