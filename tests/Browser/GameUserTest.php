<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\GameGroup;
use App\Models\GameUSer;

it('Añadir un juego', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );
    $group = GameGroup::factory()->for($user)->create();

    $page = visit( route('games.list') )
        ->click('@crear-juego-'.$group->id)
        ->fill('@juego-busqueda', 'Tomb R');

    $gameName = $page->text('[data-test="juego-1"] .gameName') ;
    
    $page->click('@juego-1')
        ->click('[data-test="plataforma-2"] input')
        ->click('[data-test="plataforma-6"] label')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee('Juego añadido al listado')
        ->assertSee($gameName)
        ;

    $platforms_igdb = $user->userGames()->first()->platforms()->pluck('igdb_id')->toArray();
    
    expect($platforms_igdb )->toContain(48, 6);//Ps4 y epic

    $this->assertDatabaseHas('game_users',[
        'id' => $user->userGames()->first()->id,
        'finished' => false,
    ]);
});


it('Añadir varios juegos en distintos grupo', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );
    $group1 = GameGroup::factory()->for($user)->create();
    $group2 = GameGroup::factory()->for($user)->create();
    $group3 = GameGroup::factory()->for($user)->create();

    //añade al grupo 1
    $page = visit( route('games.list') )
        ->click('@crear-juego-'.$group1->id)
        ->fill('@juego-busqueda', 'Tomb R');

    $gameName1 = $page->text('[data-test="juego-1"] .gameName') ;
    
    $page->click('@juego-1')
        ->click('[data-test="plataforma-2"] input')
        ->click('[data-test="plataforma-6"] label')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee($gameName1);

    //añade al grupo 2
    $page->click('@crear-juego-'.$group2->id)
        ->fill('@juego-busqueda', 'witcher');

    $gameName2 = $page->text('[data-test="juego-2"] .gameName') ;
    
    $page->click('@juego-2')
        ->click('[data-test="plataforma-4"] input')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee($gameName2);

    //añade al grupo 2
    $page->click('@crear-juego-'.$group2->id)
        ->fill('@juego-busqueda', 'Tears of the');

    $gameName3 = $page->text('[data-test="juego-1"] .gameName') ;
    
    $page->click('@juego-1')
        ->click('[data-test="plataforma-1"] input')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee($gameName3);

    //añade al grupo 3
    $page->click('@crear-juego-'.$group3->id)
        ->fill('@juego-busqueda', 'nfl 22');

    $gameName4 = $page->text('[data-test="juego-1"] .gameName') ;
    
    $page->click('@juego-1')
        ->click('[data-test="plataforma-10"] input')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee($gameName4)
        ->assertScript(
            'Array.from(document.querySelectorAll("[class^=gamecard] .title"))
                .map(el => el.textContent.trim())',
            [$gameName1, $gameName2, $gameName3, $gameName4]
        )
        ->screenshot();


});

it('Editar un juego', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    $userGames = array_column($user->userGames->toArray(), 'id');
    
    $gameName=visit( route('games.list') )
        ->text('[data-game-user-id="'.$userGames[1].'"] .title');

    visit( route('games.list') )
        ->click('@menu-juego-'.$userGames[1])
        ->click('@editar-juego-'.$userGames[1])
        ->fill('@editar-nombre-juego', 'Nombre Juego Editado')
        ->click('[data-test="plataforma-1"] input')
        ->click('@btn-guardar-juego-editado')
        ->assertSee('Juego editado correctamente')
        ->assertSee('Nombre Juego Editado')
        ->assertDontSee($gameName)
        ->screenshot();

    $this->assertDatabaseHas('game_users',[
        'id' => $userGames[1],
        'custom_title' => 'Nombre Juego Editado',
    ]);

    $this->assertDatabaseHas('game_user_platform',[
        'game_user_id' => $userGames[1],
        'platform_id' => 1
    ]);
});




it('Finalizar un juego desde la edición', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    $userGames = array_column($user->userGames->toArray(), 'id');

    $gameName=visit( route('games.list') )
        ->text('[data-game-user-id="'.$userGames[1].'"] .title');

    visit( route('games.list') )
        ->click('@menu-juego-'.$userGames[1])
        ->click('@editar-juego-'.$userGames[1])
        ->click('@editar-finalizado')
        ->click('@btn-guardar-juego-editado')
        ->assertSee('Juego editado correctamente')
        ->assertDontSee($gameName)
        ->click('@btn-toggle-finished')
        ->assertSee($gameName)
        ->screenshot();

    $this->assertDatabaseHas('game_users',[
        'id' => $userGames[1],
        'finished' => true,
    ]);
});

it('Finalizar un juego desde el tablero', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    $userGames = array_column($user->userGames->toArray(), 'id');

    $gameName=visit( route('games.list') )
        ->text('[data-game-user-id="'.$userGames[1].'"] .title');

    visit( route('games.list') )
        ->click('@menu-juego-'.$userGames[1])
        ->click('@finalizar-juego-'.$userGames[1])
        ->assertSee('¡Enhorabuena! Ahora... ¡A por el siguiente!')
        ->assertDontSee($gameName)
        ->click('@btn-toggle-finished')
        ->assertSee($gameName)
        ->screenshot();

    $this->assertDatabaseHas('game_users',[
        'id' => $userGames[1],
        'finished' => true,
    ]);
});


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
