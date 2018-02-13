<?php

namespace App\Utils;

use App\Loot;

class LootManager
{
    public function generate()
    {
        $possibleLoots = Loot::orderBy('drop_rate')->get();
        $dropRate = random_int(1, $possibleLoots->sum('drop_rate') * 2);
        $accumulator = 0;

        foreach ($possibleLoots as $loot) {
            $accumulator+= $loot->drop_rate;
            if ($dropRate < $accumulator) {
                return $loot;
            }
        }

        return null;
    }

    public function apply($type)
    {
        $method = 'apply' . $type;
        $this->$method();
    }

    private function applyHolidayTicket()
    {
        foreach (auth()->user()->habits()->get() as $habit) {
            $habit->update([
                'frozen' => true
            ]);
        }
    }
}
