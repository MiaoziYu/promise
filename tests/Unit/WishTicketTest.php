<?php

namespace Tests\Unit;

use App\User;
use App\WishTicket;
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

    /** @test */
    public function can_get_formatted_claimed_date()
    {
        // Arrange
        $wishTicket = factory(WishTicket::class)->create([
            'name' => 'funny frisch',
            'image_link' => 'example image link',
            'claimed_at' => Carbon::parse('2018-12-01 8:00pm'),
        ]);

        // Assert
        $this->assertEquals('December 1, 2018', $wishTicket->formatted_claimed_date);
    }
}
