<?php

namespace Tests\Feature;

use App\Habit;
use Tests\TestCase;

class LootTest extends TestCase
{
    /** @test */
    public function can_get_all_loots()
    {
        // Arrange
        $holidayTicket = $this->createLoot([
            'type' => 'HolidayTicket',
            'name' => 'holiday ticket',
            'drop_rate' => '5',
            'rarity' => 'legendary',
        ]);

        $freezer = $this->createLoot([
            'type' => 'HabitFreezer',
            'name' => 'habit freezer',
            'drop_rate' => '10',
            'rarity' => 'epic',
        ]);

        // Act
        $response = $this->get("/api/loots/?api_token=$this->api_token");

        // Assert
        $response->assertSee('holiday ticket');
        $response->assertSee('legendary');
        $response->assertSee('habit freezer');
        $response->assertSee('epic');
    }

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
