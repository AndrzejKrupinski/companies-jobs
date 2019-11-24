<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Requirement;
use Faker\Generator as Faker;

$factory->define(Requirement::class, function (Faker $faker) {
    return ['title' => \ucfirst($faker->word()),];
});
