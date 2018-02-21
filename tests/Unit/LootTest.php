<?php

namespace Tests\Unit;

use App\Habit;
use App\Loot;
use App\Utils\LootManager;
use Tests\TestCase;

class LootTest extends TestCase
{
    /** @test */
    public function can_drop_loot()
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

        // Act
        $lootManager = new LootManager($this->user);
        $loots = [];

        foreach(range(0, 999) as $num) {
            $loots[] = $lootManager->drop();
        }

        // Assert
        $holidayTickets = [];
        $freezers = [];
        $boosters = [];
        $crystals = [];
        $accumulation = Loot::all()->sum('drop_rate');
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
    public function can_attach_loot()
    {
        // Arrange
        $holidayTicket = $holidayTicket = $this->createLoot([
            'type' => 'HolidayTicket',
            'name' => 'holiday ticket',
            'drop_rate' => '5',
            'rarity' => 'legendary',
        ]);

        // Act
        $lootManager = new LootManager($this->user);
        $lootManager->attach($holidayTicket, $this->user);

        // Assertion
        $userLoot = $this->user->loots()->find($holidayTicket->id);
        $this->assertNotNull($userLoot);
        $this->assertEquals('holiday ticket', $userLoot->name);
    }

    /** @test */
    public function can_freeze_habit()
    {
        // Arrange
        $freezer = $this->createLoot([
            'type' => 'HabitFreezer',
        ]);

        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
        ]);

        // Act
        $lootManager = new LootManager($this->user);
        $lootManager->apply($freezer->type, $habit->id);
        
        // Assert
        $this->assertEquals(1, $this->user->habits()->findOrFail($habit->id)->frozen);
    }

    /** @test */
    public function can_release_frozen_habits_after_one_day()
    {
        // Arrange
        factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'frozen' => 1
        ]);

        factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'frozen' => 1
        ]);

        // Act
        $this->artisan('habits:check');

        // Assert
        $habits = $this->user->habits()->get();
        $this->assertEquals(0, $habits[0]->frozen);
        $this->assertEquals(0, $habits[1]->frozen);
    }
}
