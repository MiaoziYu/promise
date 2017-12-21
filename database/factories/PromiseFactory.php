<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Promise::class, function (Faker $faker) {
    return [
        'title' => 'KFC hot wings',
        'description' => '18 kfc hot wings',
        'reward_type' => 'points',
        'reward_content' => '500',
        'finished_at' => null,
    ];
});
