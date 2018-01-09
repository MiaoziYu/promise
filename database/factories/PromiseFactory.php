<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Promise::class, function (Faker $faker) {
    return [
        'name' => 'example name',
        'description' => 'example description',
        'punch_card_total' => 10,
        'punch_card_finished' => 5,
        'reward_type' => 'points',
        'reward_credits' => '500',
        'reward_name' => 'example reward name',
        'reward_image_link' => 'example_link',
        'due_date' => null,
        'expired' => null,
        'finished_at' => null,
    ];
});
