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

    /** @test */
    public function can_purchase_a_wish()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 800
        ]);
        $wish = factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link',
            'credits' => 500
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $userProfile = $user->userProfile()->first();
        $this->assertEquals(300, $userProfile->credits);

        $wishTicket = $user->wishTickets()->first();
        $this->assertEquals('funny frisch', $wishTicket->name);
        $this->assertEquals('example image link', $wishTicket->image_link);
    }

    /** @test */
    public function cannot_purchase_a_wish_when_credits_are_not_enough()
    {
        $this->disableExceptionHandling();

        // Arrange
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'credits' => 100
        ]);
        $wish = factory(Wish::class)->create([
            'user_id' => $user->id,
            'name' => 'funny frisch',
            'image_link' => 'example image link',
            'credits' => 500
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $user->api_token, []);

        // Assertion
        $response->assertStatus(422);

        $userProfile = $user->userProfile()->first();
        $this->assertEquals(100, $userProfile->credits);

        $wishTickets = $user->wishTickets()->get();
        $this->assertEquals(0, count($wishTickets));
    }
}