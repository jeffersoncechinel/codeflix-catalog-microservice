<?php

/** @var Factory $factory */

use App\models\Genre;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Genre::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'is_active' => $faker->boolean,
    ];
});
