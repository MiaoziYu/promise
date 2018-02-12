<?php

namespace Tests\Feature;

use App\User;
use App\UserProfile;
use Tests\TestCase;

class PurchaseWishTest extends TestCase
{
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
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(300, $this->user->userProfile->credits);

        $wishTicket = $this->user->wishTickets()->first();
        $this->assertEquals('potato chip', $wishTicket->wish->name);
        $this->assertEquals('buy a package of potato chip', $wishTicket->wish->description);
        $this->assertEquals(500, $wishTicket->wish->credits);
        $this->assertEquals('example_link', $wishTicket->wish->image_link);
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
            'credits' => 500
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/purchase' .'?api_token=' . $this->user->api_token, []);

        // Assertion
        $response->assertStatus(422);

        $this->assertEquals(100, $this->user->userProfile->credits);

        $this->assertEquals(0, count($this->user->wishTickets()->get()));
    }

    /** @test */
    public function can_contribute_credits_to_a_shared_wish()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 100
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $this->user->api_token, [
            'credits' => 50
        ]);

        // Assertion
        $response->assertStatus(200);

        $this->assertEquals(50, $this->user->userProfile->credits);

        $this->assertEquals(50, $this->user->wishes()->findOrFail($wish->id)->pivot->credits);
    }

    /** @test */
    public function cannot_contribute_credits_when_user_credits_is_not_enough()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 20
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $this->user->api_token, [
            'credits' => 50
        ]);

        // Assertion
        $response->assertStatus(422);

        $this->assertEquals(20, $this->user->userProfile->credits);

        $this->assertEquals(0, $this->user->wishes()->findOrFail($wish->id)->pivot->credits);
    }

    /** @test */
    public function cannot_contribute_credits_more_that_required()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 200
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'credits' => 100
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $this->user->api_token, [
            'credits' => 150
        ]);

        // Assert
        $response->assertStatus(200);

        $this->assertEquals(100, $this->user->userProfile->credits);

        $this->assertEquals(0, $this->user->wishes()->findOrFail($wish->id)->pivot->credits);
    }

    /** @test */
    public function can_resolve_a_shared_wish_when_credits_are_enough()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 200
        ]);

        $userTwo = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $userTwo->id,
            'credits' => 100
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'new PC',
            'description' => 'fancy PC for gaming',
            'image_link' => 'example image link',
            'credits' => 100
        ]);

        $userTwo->wishes()->attach($wish);

        // Act
        $responseOne = $this->actingAs($this->user)->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $this->user->api_token, [
            'credits' => 50
        ]);
        $responseTwo = $this->actingAs($userTwo)->put('/api/wishes/' . $wish->id . '/contribute?api_token=' . $userTwo->api_token, [
            'credits' => 50
        ]);

        // Assertion
        $responseOne->assertStatus(200);
        $responseTwo->assertStatus(200);

        $userOneWishTicket = $this->user->wishTickets()->first();
        $this->assertEquals('new PC', $userOneWishTicket->wish->name);
        $this->assertEquals('example image link', $userOneWishTicket->wish->image_link);
        $this->assertEquals(0, $this->user->wishes()->findOrFail($wish->id)->pivot->credits);

        $userTwoWishTicket = $userTwo->wishTickets()->first();
        $this->assertEquals('new PC', $userTwoWishTicket->wish->name);
        $this->assertEquals('example image link', $userTwoWishTicket->wish->image_link);
        $this->assertEquals(0, $userTwo->wishes()->findOrFail($wish->id)->pivot->credits);

        $this->assertEquals(0, $wish->users()->sum('credits'));
    }
}
