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
        'nombres' => $faker->nombres,
        'apellidos' => $faker->apellidos,
        'dni' => $faker->dni,
        'login' => $faker->login,
        'password' => $password ?: $password = bcrypt('secret'),
        'telefono' => $faker->telefono,
        'email' => $faker->email,
        'remember_token' => str_random(10),
    ];
});
