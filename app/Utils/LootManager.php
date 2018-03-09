<?php

namespace App\Utils;

use App\Loot;
use App\User;
use Illuminate\Support\Facades\DB;

class LootManager
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function drop()
    {
        $possibleLoots = Loot::orderBy('drop_rate')->get();
        $dropRate = random_int(1, $possibleLoots->sum('drop_rate'));
        $accumulator = 0;

        foreach ($possibleLoots as $loot) {
            $accumulator+= $loot->drop_rate;
            if ($dropRate < $accumulator) {
                if ($loot->type === 'Stone') {
                    return null;
                }
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

    public function apply($lootId, $targetId = null)
    {
        $loot = $this->user->loots()->findOrFail($lootId);

        $method = 'apply' . $loot->type;

        DB::transaction(function () use ($targetId, $method, $loot) {
            $this->$method($targetId);
            $loot->delete();
        });
    }

    private function applyHolidayTicket()
    {
        foreach ($this->user->habits()->get() as $habit) {
            $habit->update([
                'frozen' => true
            ]);
        }
    }

    private function applyHabitFreezer($id)
    {
        $this->user->habits()->findOrFail($id)->update([
            'frozen' => true
        ]);
    }
}
