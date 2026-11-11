<?php

use App\Models\User;
use App\Models\GameGroup;

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
        ->assertSee($gameName);

    $platforms_igdb = $user->userGames()->first()->platforms()->pluck('igdb_id')->toArray();
    expect( $platforms_igdb )->toContain(48, 6);//Ps4 y epic

    $this->assertDatabaseHas('game_users',[
        'id' => $user->userGames()->first()->id,
        'finished' => false,
    ]);
    
    expect( $user->userGames()->first()->game_group_id )->toBe($group->id);

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

    //añade al grupo 3, sin seleccionar plataformas
    $page->click('@crear-juego-'.$group3->id)
        ->fill('@juego-busqueda', 'nfl 22');

    $gameName4 = $page->text('[data-test="juego-1"] .gameName') ;
    
    $page->click('@juego-1')
        ->click('@btn-guardar-nuevo-juego')
        ->assertSee($gameName4)
        ->assertScript(
            'Array.from(document.querySelectorAll("[class^=gamecard] .title"))
                .map(el => el.textContent.trim())',
            [$gameName1, $gameName2, $gameName3, $gameName4]
        );
});

it('Intentar añadir un juego que ya está',function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    
    //comprueba que no permita añadirlo en el frontend. Como el juego bloodborne ya está añadido, su cuadro para seleccionarlo estará oculto, sin embargo el del Hitman no.
    visit( route('games.list'))
        ->click('@crear-juego-'.$groups[1])
        ->fill('@juego-busqueda', 'Blood')
        ->assertSourceHas('data-tip="Hitman: Blood Money"')
        ->assertSourceHas('data-tip="Bloodborne"')
        ->assertAttributeMissing('[data-tip="Hitman: Blood Money"]','style')
        ->assertAttribute('[data-tip="Bloodborne"]','style','display: none;');

    //comprueba que no permita añadirlo en el backend aunque se le pasen los datos
    test()->post(route('games.store'), [
        'game' => 1877,//Cyberpunk
        'group' => $groups[0],
        'platforms' => ['6|1'],
    ])->assertSessionHas(['error' => 'El juego ya está en uno de los grupos']);  
    
});

it('Intentar añadir un juego a un grupo de otro usuario', function(){
    $user1= User::factory()->create();
    $this->actingAs( $user1 );
    $groups = rellenarTablero($user1);

    $user2= User::factory()->create();
    $this->actingAs( $user2 );

    test()->post(route('games.store'), [
        'game' => 4843,//KC: Deliverance
        'group' => $groups[0],
        'platforms' => ['6|1'],
    ])->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);  

});

it('Intentar añadir un juego con datos incorrectos', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);

    test()->post(route('games.store'), [
        'game' => '',
        'group' => $groups[0],
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['game' => 'Debes seleccionar un juego de la lista']);  
    test()->post(route('games.store'), [
        'game' => 'string',
        'group' => $groups[0],
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['game' => 'Debes seleccionar un juego de la lista']);  
    test()->post(route('games.store'), [
        'game' => '09876543210',
        'group' => $groups[0],
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['game' => 'Debes seleccionar un juego de la lista']);
    
    test()->post(route('games.store'), [
        'game' => 4843,//KC: Deliverance
        'group' => '',
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['group' => 'Añade el juego a uno de tus grupos']);  
    test()->post(route('games.store'), [
        'game' => 4843,//KC: Deliverance
        'group' => 'string',
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['group' => 'Añade el juego a uno de tus grupos']);  
    test()->post(route('games.store'), [
        'game' => 4843,//KC: Deliverance
        'group' => '01234567890',
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['group' => 'Añade el juego a uno de tus grupos']);

    test()->post(route('games.store'), [
        'game' => 4843,//KC: Deliverance
        'group' => $groups[0],
        'platforms' => '6|1',
    ])->assertSessionHasErrors(['platforms' => 'Selecciona las plataformas donde posees el juego']);
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
        ->assertDontSee($gameName);

    $this->assertDatabaseHas('game_users',[
        'id' => $userGames[1],
        'custom_title' => 'Nombre Juego Editado',
    ]);

    $this->assertDatabaseHas('game_user_platform',[
        'game_user_id' => $userGames[1],
        'platform_id' => 1
    ]);
    
});

it('Editar un juego quitando las plataformas', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    $userGames = array_column($user->userGames->toArray(), 'id');
    
    $gameName=visit( route('games.list') )
        ->text('[data-game-user-id="'.$userGames[1].'"] .title');

    visit( route('games.list') )
        ->click('@menu-juego-'.$userGames[1])
        ->click('@editar-juego-'.$userGames[1])
        ->click('[data-test="plataforma-3"] input')
        ->click('@btn-guardar-juego-editado')
        ->assertSee('Juego editado correctamente');

    $this->assertDatabaseMissing('game_user_platform',[
        'game_user_id' => $userGames[1],
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
        ->assertSee($gameName);

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
        ->assertSee($gameName);

    $this->assertDatabaseHas('game_users',[
        'id' => $userGames[1],
        'finished' => true,
    ]);
});

it('Intentar editar un juego con datos incorrectos', function(){
    $user = User::factory()->create();
    $this->actingAs( $user );

    $groups = rellenarTablero($user);
    $gameUserId = $user->userGames()->first()->id;
    test()->patch(route('games.update', ['gameUser' => $gameUserId]), [
        'name' => 'ASD',
        'finished' => true,
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['name' => 'El nombre personalizado del juego debe tener al menos 5 caracteres']);

    test()->patch(route('games.update', ['gameUser' => $gameUserId]), [
        'name' => '',
        'finished' => 'finalizado',
        'platforms' => ['6|1'],
    ])->assertSessionHasErrors(['finished' => 'Selecciona el estado del juego']); 

    test()->patch(route('games.update', ['gameUser' => $gameUserId]), [
        'name' => '',
        'finished' => false,
        'platforms' => '6|1',
    ])->assertSessionHasErrors(['platforms' => 'Selecciona las plataformas donde posees el juego']);
});

it('Intentar editar un juego de otro usuario', function(){
    $user1= User::factory()->create();
    $this->actingAs( $user1 );
    $groups = rellenarTablero($user1);
    

    $user2= User::factory()->create();
    $this->actingAs( $user2 );

    test()->patch(route('games.update', $user1->userGames()->first()->id), [
        'name' => 'KC: Deliverance',
        'finished' => true,
        'platforms' => ['6|1'],
    ])->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);  
});

it('Intentar finalizar el juego de otro usuario', function(){
    $user1= User::factory()->create();
    $this->actingAs( $user1 );
    $groups = rellenarTablero($user1);

    $user2= User::factory()->create();
    $this->actingAs( $user2 );

    test()->patch(route('games.toggle.finished', $user1->userGames()->first()->id), [])
        ->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);  
});

it('Eliminar un juego de la lista', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );
    $groups = rellenarTablero($user);
    $userGames = array_column($user->userGames->toArray(), 'id');

    $gameName=visit( route('games.list') )
        ->text('[data-game-user-id="'.$userGames[1].'"] .title');

    visit( route('games.list') )
        ->click('@menu-juego-'.$userGames[1])
        ->click('@quitar-juego-'.$userGames[1])
        ->assertSee('Juego eliminado correctamente ')
        ->click('@btn-toggle-finished')
        ->assertDontSee($gameName);

    $this->assertDatabaseMissing('game_users', [
        'id' => $userGames[1]
    ]);     
});

it('Intentar eliminar un juego de la lista de otro usuario', function(){
    $user1= User::factory()->create();
    $this->actingAs( $user1 );
    $groups = rellenarTablero($user1);
    $gameId = $user1->userGames()->first()->id;

    $user2= User::factory()->create();
    $this->actingAs( $user2 );

    test()->delete(route('games.delete',  $gameId), [])
        ->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);

    $this->assertDatabaseHas('game_users', [
        'id' =>  $gameId
    ]);            
});

