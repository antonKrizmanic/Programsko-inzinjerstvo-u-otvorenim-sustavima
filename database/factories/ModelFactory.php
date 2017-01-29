<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
$factory->define(App\Event::class, function(Faker\Generator $faker){
    return[
        'title' => $faker->sentence($nbWords = 4, $variableNbWords = true),
        'short_description' => $faker->realText($maxNbChars=50, $indexSize=2),
        'long_description' => $faker->realText($maxNbChars=100, $indexSize=2),
        'user_id' => $faker->numberBetween($min=1, $max=51),
    ];
});
