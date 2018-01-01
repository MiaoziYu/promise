<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Promise::class, function (Faker $faker) {
    return [
        'name' => 'KFC hot wings',
        'description' => '18 kfc hot wings',
        'punch_card_total' => 10,
        'punch_card_finished' => 5,
        'reward_type' => 'points',
        'reward_credits' => '500',
        'reward_image_link' => 'example_link',
        'finished_at' => null,
    ];
});
