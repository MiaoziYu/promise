<?php

use App\WishTicket;
use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(WishTicket::class, function (Faker $faker) {
    return [
        'name' => 'funny frisch',
        'image_link' => 'example image link',
        'claimed_at' => null
    ];
});
