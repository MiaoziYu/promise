<?php

namespace Tests\Feature;

use App\Habit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LootTest extends TestCase
{
    /** @test */
    public function can_apply_holiday_ticket()
    {
        // Arrange
        $holidayTicket = $this->createLoot([
            'type' => 'HolidayTicket',
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
        ]);

        // Act
        $response = $this->put("/api/loots/$holidayTicket->id/apply?api_token=$this->api_token");

        // Assert
        $response->assertStatus(200);

        $habits = $this->user->habits()->get();
        $this->assertEquals(1, $habits[0]->frozen);
        $this->assertEquals(1, $habits[1]->frozen);
    }
}
