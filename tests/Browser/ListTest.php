<?php

use App\Models\User;

it('Mostrar y ocultar los juegos completados', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );
    $groups = rellenarTablero($user);

    $userGame = $user->userGames()->first();

    test()->patch(route('games.toggle.finished', $userGame->id), [])
        ->assertSessionHas(['success' => '¡Enhorabuena! Ahora... ¡A por el siguiente!']); 

    visit( route('games.list') )
        ->assertDontSee($userGame->game->name)
        ->click('@btn-toggle-finished')
        ->assertSee($userGame->game->name)
        ->screenshot();
});

it('Mover juego a otra columna', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );
    $groups = rellenarTablero($user);


    visit( route('games.list') )
        ->assertPresent('#group-1 li[data-game-user-id="1"]')
        ->drag('#group-1 li[data-game-user-id="1"] .dragpoint', '#group-2 ul.list')
        ->assertNotPresent('#group-1 li[data-game-user-id="1"]')
        ->assertPresent('#group-2 li[data-game-user-id="1"]');
    //Comprueba que al recargar se mantiene
    visit( route('games.list') )
        ->assertNotPresent('#group-1 li[data-game-user-id="1"]')
        ->assertPresent('#group-2 li[data-game-user-id="1"]')
        ->screenshot();
        
    $this->assertDatabaseHas('game_users',[
        'id' => 1,
        'game_group_id' => 2,
    ]);
});

it('Mover varios juegos entre columnas', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );
    $groups = rellenarTablero($user);
    // J1-> tomb Riser, J2-> Cyberpunk, J3-> Bloodborne, J4-> Zelda
    //Comprobación de movimiento con JS
    visit( route('games.list') )
        ->drag('#group-2 li[data-game-user-id="2"] .dragpoint', '#group-3 ul.list') // Juego 2 a Grupo 3
        ->drag('#group-3 li[data-game-user-id="4"] .dragpoint', '#group-2 ul.list') // Juego 4 a grupo 2
        ->drag('#group-2 li[data-game-user-id="3"] .dragpoint', '#group-1 ul.list') //Juego 3 a grupo 1
        ->drag('#group-1 li[data-game-user-id="1"] .dragpoint', '#group-2 ul.list') // Juego 1 a grupo 2
        ->drag('#group-2 li[data-game-user-id="1"] .dragpoint', '#group-3 ul.list') // Juego 1 a grupo 3
        ->assertPresent('#group-1 li[data-game-user-id="3"]')
        ->assertPresent('#group-2 li[data-game-user-id="4"]')
        ->assertPresent('#group-3 li[data-game-user-id="1"]')
        ->assertPresent('#group-3 li[data-game-user-id="2"]')
        ->screenshot();
    //comprobación de cambios tras recargar
    visit( route('games.list') )
        ->assertPresent('#group-1 li[data-game-user-id="3"]')
        ->assertPresent('#group-2 li[data-game-user-id="4"]')
        ->assertPresent('#group-3 li[data-game-user-id="1"]')
        ->assertPresent('#group-3 li[data-game-user-id="2"]');

    $this->assertDatabaseHas('game_users',[
        'id' => 1,
        'game_group_id' => 3,
    ]);
    $this->assertDatabaseHas('game_users',[
        'id' => 2,
        'game_group_id' => 3,
    ]);
    $this->assertDatabaseHas('game_users',[
        'id' => 3,
        'game_group_id' => 1,
    ]);
    $this->assertDatabaseHas('game_users',[
        'id' => 4,
        'game_group_id' => 2,
    ]);    
});
*/
it('Mueve juegos en su columna', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );
    $groups = rellenarTablero($user);

    // J1-> tomb Riser, J2-> Cyberpunk, J3-> Bloodborne, J4-> Zelda
    //primero agrupamos los 4 en una columna para moverlos mejor
    visit( route('games.list') )
        ->drag('#group-1 li[data-game-user-id="1"] .dragpoint', '#group-2 ul.list li[data-game-user-id="3"]')
        ->drag('#group-3 li[data-game-user-id="4"] .dragpoint', '#group-2 ul.list li[data-game-user-id="3"]')
    //drag lo arrastra antes del objetivo, así que el orden queda: CyberPunk, Tomb Raider, Zelda, Bloodborne
        ->drag('#group-2 li[data-game-user-id="2"] .dragpoint', '#group-2 ul.list li[data-game-user-id="4"]') // CP antes de Zelda
        ->drag('#group-2 li[data-game-user-id="3"] .dragpoint', '#group-2 ul.list li[data-game-user-id="1"]') // Bloodborne antes de Tomb Raider
        ->drag('#group-2 li[data-game-user-id="3"] .dragpoint', '#group-2 ul.list li[data-game-user-id="2"]') // Bloodborne antes de Cyberpunk
    //el orden ahora debe ser Tomb Raider, Bloodborne, CyberPunk, Zelda
        ->screenshot();

    visit( route('games.list') )
        ->assertScript('Array.from(document.querySelectorAll("li[data-game-user-id]"))
                .map(el => el.dataset.gameUserId)',
            ['1', '3', '2', '4']
        );

    $this->assertDatabaseHas('game_users',[
        'id' => 1,
        'game_group_id' => 2,
        'position' => 1
    ]);
    $this->assertDatabaseHas('game_users',[
        'id' => 2,
        'game_group_id' => 2,
        'position' => 3
    ]); 
    $this->assertDatabaseHas('game_users',[
        'id' => 3,
        'game_group_id' => 2,
        'position' => 2
    ]); 
    $this->assertDatabaseHas('game_users',[
        'id' => 4,
        'game_group_id' => 2,
        'position' => 4
    ]); 
    
});

it('Intenta cambiar orden de un juego a otro usuario', function(){
    $user1= User::factory()->create();
    $this->actingAs( $user1 );
    $groups = rellenarTablero($user1);

    $user2= User::factory()->create();
    $this->actingAs( $user2 );
    
    test()->post( route('games.reorder'), [
        'id' => 2,
        'group_id' => 2,
        'position' =>2
    ])->assertSessionHas(['error' => 'No tienes permiso para hacer esto']); 
});

it('Cambia el theme', function(){
    $user= User::factory()->create();
    $this->actingAs( $user );

    expect($user->theme)->toBe('dark'); 

    visit( route('games.list') )
        ->assertPresent('html[data-theme="moon"]')
        ->click('@btn-toggle-theme')
        ->assertPresent('html[data-theme="sun"]');
    expect($user->theme)->toBe('light');    
    
    visit( route('games.list') )
        ->assertPresent('html[data-theme="sun"]')
        ->click('@btn-toggle-theme')
        ->assertPresent('html[data-theme="moon"]');
    expect($user->theme)->toBe('dark'); 
});