<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'description' => rand(1, 0) % 2 == 0 ? $faker->sentence() : null
    ];
});
