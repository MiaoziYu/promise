<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class WishTicketTest extends TestCase
{
    /** @test */
    public function can_get_formatted_claimed_date()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        $wishTicket = $this->createWishTicket($wish, Carbon::parse('2018-12-01 8:00pm'));

        // Assert
        $this->assertEquals('December 1, 2018', $wishTicket->formatted_claimed_date);
    }
}
