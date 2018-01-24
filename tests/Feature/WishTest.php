<?php

namespace Tests\Feature;

use App\User;
use App\Wish;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WishTest extends TestCase
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
    public function can_view_a_wish()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        // Act
        $response = $this->get('/api/wishes/' . $wish->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);
        $response->assertSee('potato chip');
        $response->assertSee('buy a package of potato chip');
        $response->assertSee('500');
        $response->assertSee('example_link');
    }

    /** @test */
    public function can_view_unresolved_wishes()
    {
        // Arrange
        $this->createWish([
            'owner' => $this->user->id,
            'name' => 'nachos',
            'credits' => 500
        ]);
        $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'credits' => 400,
            'resolved_at' => Carbon::now()
        ]);

        // Act
        $response = $this->get('/api/wishes/' . '?resolved=false&api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $response->assertSee('nachos');
        $response->assertSee('500');
        $response->assertDontSee('potato chip');
        $response->assertDontSee('400');
    }

    /** @test */
    public function can_view_resolved_wishes()
    {
        // Arrange
        $this->createWish([
            'owner' => $this->user->id,
            'name' => 'nachos',
            'credits' => 500
        ]);
        $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'credits' => 400,
            'resolved_at' => Carbon::now()
        ]);

        // Act
        $response = $this->get('/api/wishes/' . '?resolved=true&api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $response->assertDontSee('nachos');
        $response->assertDontSee('500');
        $response->assertSee('potato chip');
        $response->assertSee('400');
    }

    /** @test */
    public function can_create_a_wish()
    {
        // Act
        $response = $this->post('/api/wishes/' . '?api_token=' . $this->user->api_token, [
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        // Assertion
        $response->assertStatus(201);

        $wish = $this->user->wishes()->first();
        $this->assertEquals('potato chip', $wish->name);
        $this->assertEquals('buy a package of potato chip', $wish->description);
        $this->assertEquals(500, $wish->credits);
        $this->assertEquals('example_link', $wish->image_link);

        $this->assertEquals($this->user->id, $this->user->wishes()->find($wish->id)->pivot->user_id);
    }

    /** @test */
    public function can_update_a_wish()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'image_link' =>'example_link'
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '?api_token=' . $this->user->api_token, [
            'name' => 'nachos',
            'description' => 'buy a nachos',
            'image_link' =>'another_example_link'
        ]);

        // Assertion
        $response->assertStatus(200);

        $updatedWish = $this->user->wishes()->findOrfail($wish->id);
        $this->assertEquals('nachos', $updatedWish->name);
        $this->assertEquals('buy a nachos', $updatedWish->description);
        $this->assertEquals('another_example_link', $updatedWish->image_link);
    }

    /** @test */
    public function can_share_a_wish()
    {
        // Arrange
        $userTwo = factory(User::class)->create([
            'email' => 'bearzk@example.com'
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'name' => 'new PC',
            'description' => 'fancy PC for gaming',
        ]);

        // Act
        $response = $this->put('/api/wishes/' . $wish->id . '/share?api_token=' . $this->user->api_token, [
            'shared_user_email' => $userTwo->email
        ]);

        // Assert
        $response->assertStatus(200);

        $sharedWish = $userTwo->wishes()->find($wish->id);
        $this->assertNotNull($sharedWish);
        $this->assertEquals('new PC', $sharedWish->name);
        $this->assertEquals('fancy PC for gaming', $sharedWish->description);
    }

    /** @test */
    public function can_delete_a_wish()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        $userTwo = factory(User::class)->create();
        $userTwo->wishes()->attach($wish);

        // Act
        $response = $this->delete('/api/wishes/' . $wish->id . '?api_token=' . $this->user->api_token);

        // Assertion
        $response->assertStatus(200);

        $this->assertNull($this->user->wishes()->find($wish->id));
        $this->assertNull($userTwo->wishes()->find($wish->id));
    }
}
