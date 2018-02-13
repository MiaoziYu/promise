<?php

namespace Tests\Unit;

use App\Habit;
use App\Loot;
use App\Utils\LootManager;
use Tests\TestCase;

class LootTest extends TestCase
{
    /** @test */
    public function can_generate_loot()
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

        $booster = $this->createLoot([
            'type' => 'HabitBooster',
            'name' => 'habit booster',
            'drop_rate' => '20',
            'rarity' => 'rare',
        ]);

        $crystal = $this->createLoot([
            'type' => 'Crystal',
            'name' => 'crystal',
            'drop_rate' => '50',
            'rarity' => 'common',
            'value' => '1'
        ]);

        $lootManager = new LootManager();

        // Act
        $loots = [];

        foreach(range(0, 999) as $num) {
            $loots[] = $lootManager->generate();
        }

        // Assert
        $holidayTickets = [];
        $freezers = [];
        $boosters = [];
        $crystals = [];
        $accumulation = Loot::all()->sum('drop_rate') * 2;
        $coefficient = 1.2;

        foreach ($loots as $loot) {
            if ($loot !== null) {
                if ($loot->type === 'HolidayTicket') {
                    $holidayTickets[] = $loot;
                } elseif ($loot->type === 'HabitFreezer') {
                    $freezers[] = $loot;
                } elseif ($loot->type === 'HabitBooster') {
                    $boosters[] = $loot;
                } elseif ($loot->type === 'Crystal') {
                    $crystals[] = $loot;
                }
            }
        }

        $this->assertTrue(count($holidayTickets) / 1000 <= $holidayTicket->drop_rate * $coefficient / $accumulation );
        $this->assertTrue(count($freezers) / 1000 <= $freezer->drop_rate * $coefficient / $accumulation );
        $this->assertTrue(count($boosters) / 1000 <= $booster->drop_rate * $coefficient / $accumulation );
        $this->assertTrue(count($crystals) / 1000 <= $crystal->drop_rate * $coefficient / $accumulation );
    }

    /** @test */
    public function can_freeze_all_habits()
    {
        // Arrange

        // Act
        
        // Assert
    }

    /** @test */
    public function can_release_holiday_ticket_after_one_day()
    {
        // Arrange
        $this->createLoot([
            'type' => 'HolidayTicket',
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
        ]);

        // Act
        $this->artisan('habits:check');

        // Assert
        $habits = $this->user->habits()->get();
        $this->assertEquals(0, $habits[0]->frozen);
        $this->assertEquals(0, $habits[1]->frozen);
    }
}
