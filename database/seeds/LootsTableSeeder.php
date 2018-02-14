<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LootsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loots = [
            [
                'id' => 1,
                'type' => 'HolidayTicket',
                'name' => 'holiday ticket',
                'drop_rate' => 5,
                'rarity' => 'legendary',
                'created_at' => Carbon::now(),
            ],
            [
                'type' => 'HabitFreezer',
                'name' => 'habit freezer',
                'drop_rate' => '10',
                'rarity' => 'epic',
                'created_at' => Carbon::now(),
            ],
            [
                'type' => 'HabitBooster',
                'name' => 'habit booster',
                'drop_rate' => '10',
                'rarity' => 'rare',
                'value' => '50',
                'created_at' => Carbon::now(),
            ]
        ];

        foreach ($loots as $loot) {
            DB::table('loots')->insert($loot);
        }
    }
}
