<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Wish::class, function (Faker $faker) {
    return [
        'name' => 'example name',
        'description' => 'example description',
        'credits' => 50,
        'image_link' => 'example_link'
    ];
});
