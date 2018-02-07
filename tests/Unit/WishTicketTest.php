<?php

namespace Tests\Unit;

use App\User;
use App\Wish;
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

    private function createWish($data)
    {
        $wish = factory(Wish::class)->create($data);

        $this->user->wishes()->attach($wish);

        return $wish;
    }

    private function createWishTicket($wish, $data)
    {
        $wishTicket = $wish->wishTickets()->create($data);

        $this->user->wishTickets()->attach($wishTicket);

        return $wishTicket;
    }

    /** @test */
    public function can_get_formatted_claimed_date()
    {
        // Arrange
        $wish = $this->createWish([
            'owner' => $this->user->id,
        ]);

        $wishTicket = $this->createWishTicket($wish, [
            'claimed_at' => Carbon::parse('2018-12-01 8:00pm'),
        ]);

        // Assert
        $this->assertEquals('December 1, 2018', $wishTicket->formatted_claimed_date);
    }
}
