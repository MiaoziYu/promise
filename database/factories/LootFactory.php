<?php

use App\Loot;
use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Loot::class, function (Faker $faker) {
    return [
        'type' => 'HolidayTicket',
        'name' => 'holiday ticket',
        'drop_rate' => '1',
        'rarity' => 'legendary',
        'value' => null,
    ];
});
