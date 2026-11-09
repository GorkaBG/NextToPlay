<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\GameGroup;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit','Browser','Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


/**
 * Genera 3 grupos y 4 juegos (1 + 2 +1) para los test sin tener que repetir todo el código
 * @param User $user: Usuario conectado
 * @return $groups: devuelve el id de los grupos creados
 */
function rellenarTablero(User $user)
{
    $groups = [];
    $groups[] = GameGroup::factory()->for($user)->create()->id;
    $groups[] = GameGroup::factory()->for($user)->create()->id;
    $groups[] = GameGroup::factory()->for($user)->create()->id;

    test()->post(route('games.store'), [
        'game' => 37777,//Shadow of the Tomb Raider
        'group' => $groups[0],
        'platforms' => [48,'6|1'],
    ])->assertSessionHas(['success' => 'Juego añadido al listado']);

    test()->post(route('games.store'), [
        'game' => 1877,//Cyberpunk 2077
        'group' => $groups[1],
        'platforms' => ['6|3'],
    ])->assertSessionHas(['success' => 'Juego añadido al listado']);

    test()->post(route('games.store'), [
        'game' => 7334,//Bloodborne
        'group' => $groups[1],
        'platforms' => [48],
    ])->assertSessionHas(['success' => 'Juego añadido al listado']);

    test()->post(route('games.store'), [
        'game' => 119388,//Zelda: Tears of the Kingdom
        'group' => $groups[2],
        'platforms' => [130],
    ])->assertSessionHas(['success' => 'Juego añadido al listado']);

    test()->assertDatabaseHas('games',[ 'igdb_id' => 37777 ]);
    test()->assertDatabaseHas('games',[ 'igdb_id' => 1877 ]);
    test()->assertDatabaseHas('games',[ 'igdb_id' => 7334 ]);
    test()->assertDatabaseHas('games',[ 'igdb_id' => 119388 ]);

    return $groups;
}
