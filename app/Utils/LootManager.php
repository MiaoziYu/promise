<?php

namespace App\Utils;

use App\Loot;
use App\User;

class LootManager
{
    public function drop()
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

    public function attach(Loot $loot, User $user)
    {
        $user->loots()->attach($loot);
    }

    public function gave($user)
    {
        $loot = $this->drop();
        if ($loot !== null) {
            $this->attach($loot, $user);
        }

        return $loot;
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
