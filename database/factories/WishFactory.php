<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Wish::class, function (Faker $faker) {
    return [
        'name' => 'nachos',
        'description' => 'eat nachos',
        'credits' => 500,
        'image_link' => 'example_link'
    ];
});
