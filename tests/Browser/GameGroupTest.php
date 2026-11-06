<?php

use App\Models\User;
use App\Models\GameGroup;

it('Crea el primer grupo sin icono', function(){
    $this->actingAs( User::factory()->create() );

    $group = implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2));
    visit( route('games.list') )
        ->click('@crear-grupo-cuerpo')
        ->fill('@nombre-grupo', substr($group, 0, 25) )
        ->fill('@color-grupo', fake()->hexColor())
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha creado correctamente')
        ->assertSee($group);
});

it('Crea varios grupos con y sin icono y en orden correcto', function(){
    $this->actingAs( User::factory()->create() );

    $group1 = substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29);
    $group2 = substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29);
    $group3 = substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29);

    visit( route('games.list') )
        ->click('@crear-grupo-cuerpo')
        ->fill('@nombre-grupo', $group1 )
        ->fill('@color-grupo', fake()->hexColor())
        ->fill('@icono-grupo-search', 'game')
        ->click('#icon-list li:nth-child(5) button')
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha creado correctamente')

        ->click('@crear-grupo')
        ->fill('@nombre-grupo', $group2 )
        ->fill('@color-grupo', fake()->hexColor())
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha creado correctamente')

        ->click('@crear-grupo')
        ->fill('@nombre-grupo', $group3 )
        ->fill('@color-grupo', fake()->hexColor())
        ->fill('@icono-grupo-search', 'control')
        ->click('#icon-list li:nth-child(6) button')
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha creado correctamente')        
        ->assertScript(
            'Array.from(document.querySelectorAll("[id^=group-header-] h2"))
                .map(el => el.textContent.trim())',
            [$group1, $group2, $group3]
        );
});

it('Dos usuarios crean grupos con el mismo nombre', function(){
    $user1 = User::factory()->create();

    $group = GameGroup::factory()->for($user1)->create();

    $user2 = User::factory()->create();
    $this->actingAs($user2);

    $this->post(route('groups.store'), [
        'name' => $group->groupname,
        'color' => fake()->hexColor(),
        'icon' => null,
    ])->assertSessionHas(['success' => 'El grupo se ha creado correctamente']);
});

it('Intenta crear un grupo repetido', function(){
    $user = User::factory()->create();
    $this->actingAs($user);

    $group = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);

    visit( route('games.list') )
        ->click('@crear-grupo')
        ->fill('@nombre-grupo', $group->groupname )
        ->fill('@color-grupo', fake()->hexColor())
        ->click('@btn-guardar-grupo')
        ->assertSee('Ya tienes un grupo con ese nombre');

});

it('Intenta crear un grupo con nombre corto', function(){
    $this->actingAs( User::factory()->create() );

    visit( route('games.list') )
        ->click('@crear-grupo-cuerpo')
        ->fill('@nombre-grupo', substr(fake()->word(), 0, 3) )
        ->fill('@color-grupo', fake()->hexColor())
        ->click('@btn-guardar-grupo')
        ->assertSee('El nombre del grupo debe tener al menos 5 caracteres');
});

it('Intenta crear un grupo valores incorrectos', function(){
    $this->actingAs( User::factory()->create() );

    //sin nombre
    $this->post(route('groups.store'), [
        'name' => '',
        'color' => fake()->hexColor(),
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['name' => 'Debes darle un nombre no repetido al grupo']);
    //nombre de más de 30 caracteres
    $this->post(route('groups.store'), [
        'name' => fake()->paragraph().' '.fake()->paragraph(),
        'color' => fake()->hexColor(),
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['name' => 'El nombre del grupo no puede superar los 30 caracteres']);
    //sin código de color
    $this->post(route('groups.store'), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => '',
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['color' => 'Debes seleccionar un color']);
    //código de color no válido
    $this->post(route('groups.store'), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => 'aeiou',
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['color' => 'El color seleccionado no es válido']);
    //icono no válido
    $this->post(route('groups.store'), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => fake()->hexColor(),
        'icon' => 'aeiou',
    ])->assertSessionHasErrors(['icon' => 'El icono debe pertenecer a una de las librerías permitidas']);
});

it('Edita un grupo', function(){
    $user = User::factory()->create();

    $this->actingAs($user);
    $group = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);

    $groupName = substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29);

    visit( route('games.list') )
        ->click('@menu-grupo-'.$group->id)
        ->click('@editar-grupo-'.$group->id)
        ->fill('@nombre-grupo', $groupName )
        ->fill('@color-grupo', '#444444')
        ->fill('@icono-grupo-search', 'bomb')
        ->click('#icon-list li:nth-child(2) button')
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha modificado correctamente')
        ->assertSee($groupName)
        ->assertAttribute('group-header-'.$group->id, 'style', 'background-color: #444444;')
        ->assertPresent('#group-header-'.$group->id.' svg#mdi-bomb');
});

it('Edita un grupo conservando su nombre', function(){
    $user = User::factory()->create();

    $this->actingAs($user);
    $group = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);

    visit( route('games.list') )
        ->click('@menu-grupo-'.$group->id)
        ->click('@editar-grupo-'.$group->id)
        ->fill('@color-grupo', '#444444')
        ->click('@btn-guardar-grupo')
        ->assertSee('El grupo se ha modificado correctamente')
        ->assertSee($group->groupname)
        ->assertAttribute('group-header-'.$group->id, 'style', 'background-color: #444444;');
});

it('Intenta editar un grupo con nombre repetido', function(){
    $user = User::factory()->create();

    $this->actingAs($user);
    $group1 = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);
    $group2 = GameGroup::factory()->for($user)->create();
    
    visit( route('games.list') )
        ->click('@menu-grupo-'.$group2->id)
        ->click('@editar-grupo-'.$group2->id)
        ->fill('@nombre-grupo', $group1->groupname )
        ->click('@btn-guardar-grupo')
        ->assertSee('Ya tienes un grupo con ese nombre');

});

it('Intenta edita un grupo con valores incorrectos', function(){
    $user = User::factory()->create();

    $this->actingAs($user);
    $group = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);

    $this->patch(route('groups.update', ['gameGroup' => $group->id]), [
        'name' => '',
        'color' => fake()->hexColor(),
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['name' => 'Debes darle un nombre no repetido al grupo']);
    //nombre de más de 30 caracteres
    $this->patch(route('groups.update', $group->id), [
        'name' => fake()->paragraph().' '.fake()->paragraph(),
        'color' => fake()->hexColor(),
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['name' => 'El nombre del grupo no puede superar los 30 caracteres']);
    //sin código de color
    $this->patch(route('groups.update', $group->id), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => '',
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['color' => 'Debes seleccionar un color']);
    //código de color no válido
    $this->patch(route('groups.update', $group->id), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => 'aeiou',
        'icon' => 'heroicon-o-academic-cap',
    ])->assertSessionHasErrors(['color' => 'El color seleccionado no es válido']);
    //icono no válido
    $this->patch(route('groups.update', $group->id), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => fake()->hexColor(),
        'icon' => 'aeiou',
    ])->assertSessionHasErrors(['icon' => 'El icono debe pertenecer a una de las librerías permitidas']);
});


it('Intenta editar un grupo de otro usuario', function(){
    $user1 = User::factory()->create();
    $group = GameGroup::factory()->for($user1)->create();

    $user2 = User::factory()->create();
    $this->actingAs($user2);

    $this->patch(route('groups.update', ['gameGroup' => $group->id]), [
        'name' => substr(implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)), 0, 29),
        'color' => fake()->hexColor(),
        'icon' => 'mdi-controller',
    ])->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);
});


it('Elimina grupos', function(){
    $user = User::factory()->create();

    $this->actingAs($user);
    $group1 = GameGroup::factory()->for($user)->create(['icon' => 'mdi-controller']);
    $group2 = GameGroup::factory()->for($user)->create();

    visit( route('games.list') )
        ->click('@menu-grupo-'.$group1->id)
        ->click('@eliminar-grupo-'.$group1->id)
        ->click('@Eliminar')
        ->assertDontSee($group1->groupname)
        ->click('@menu-grupo-'.$group2->id)
        ->click('@eliminar-grupo-'.$group2->id)
        ->click('@Eliminar')
        ->assertDontSee($group2->groupname)
        ->assertSee('crea tu primer grupo');

    $this->assertDatabaseMissing('game_groups', [
        'id' => $group1->id,
    ]); 
    $this->assertDatabaseMissing('game_groups', [
        'id' => $group2->id,
    ]); 
});


it('Intenta eliminar un grupo de otro usuario', function(){
    $user1 = User::factory()->create();
    $group = GameGroup::factory()->for($user1)->create();

    $user2 = User::factory()->create();
    $this->actingAs($user2);

    $this->delete( route('groups.delete', $group->id))
        ->assertSessionHas(['error' => 'No tienes permiso para hacer esto']);
});