<?php

use App\WishTicket;
use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(WishTicket::class, function (Faker $faker) {
    return [
        'claimed_at' => null
    ];
});
