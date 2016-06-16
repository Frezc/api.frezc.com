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
$statusArr = ['todo', 'complete', 'layside', 'abandon'];
$typeArr = ['default', 'entertainment', 'work', 'study', 'trivia', 'outOfDoor'];

$factory->define(App\Todo::class, function (Faker\Generator $faker) use($statusArr, $typeArr) {
	$r = rand(0, 3);
	$end = $r != 0;
    return [
        'user_id' => rand(1, 2),
        'title' => $faker->text(30),
        'status' => $statusArr[$r],
        'type' => $typeArr[rand(0, 5)],
        'start_at' => time() - 3600,
        'deadline' => time() + 3600 * 24 * 30,
        'priority' => rand(1, 9),
        'location' => $faker->address,
        'end_at' => $end ? time() : 0,
        'contents' => '[{"content":"'.$faker->text(255).'","status":1}]'
    ];
});
