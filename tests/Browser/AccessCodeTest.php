<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

it ('Inicia sesión con el código correcto', function(){
    $email = fake()->safeEmail();
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => now()
    ]);
    session(['emailAttempt' => $email]);

    visit('/codeaccess')
        ->fill('code', '1234')
        ->click('Aceptar')
        ->assertPathIs('/games');

    $this->assertAuthenticated();
});

it('Impide acceder sin solicitar código', function(){
    visit('/codeaccess')
        ->fill('code', '4321')
        ->click('Aceptar')
        ->assertSee('El código no es válido o ha caducado.')
        ->assertPathIs('/codeaccess');

    $this->assertGuest();
});

it ('Impide iniciar sesión con un código incorrecto', function(){
    $email = fake()->safeEmail();
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => now()
    ]);
    session(['emailAttempt' => $email]);

    visit('/codeaccess')
        ->fill('code', '4321')
        ->click('Aceptar')
        ->assertSee('El código no es válido o ha caducado.')
        ->assertPathIs('/codeaccess');

    $this->assertGuest();
});

it ('Impide iniciar sesión con un código caducado', function(){
    $email = fake()->safeEmail();
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => now()->subMinutes(6)
    ]);
    session(['emailAttempt' => $email]);

    visit('/codeaccess')
        ->fill('code', '1234')
        ->click('Aceptar')
        ->assertSee('El código no es válido o ha caducado.')
        ->assertPathIs('/codeaccess');

    $this->assertGuest();
});

it('El código nuevo invalida el anterior', function(){
    $email = fake()->safeEmail();
    $originalTime = now()->subMinutes(1);
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => $originalTime
    ]);

    visit('/')
        ->fill('email', $email)
        ->click('@btn-entrar')
        ->assertPathIs('/codeaccess');

    $user = User::where('email', $email)->firstOrFail();
    expect( $user->code_created_at === $originalTime )->toBeFalse();
    expect( Hash::check('1234', $user->code) )->toBeFalse();
});

it('Impedir reutilizar un código', function(){
    $email = fake()->safeEmail();
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => now()
    ]);
    session(['emailAttempt' => $email]);

    visit('/codeaccess')
        ->fill('code', '1234')
        ->click('Aceptar')
        ->assertPathIs('/games');

    $this->assertAuthenticated();
    Auth::logout();
    $this->assertGuest();

    visit('/codeaccess')
        ->fill('code', '1234')
        ->click('Aceptar')
        ->assertPathIs('/codeaccess')
        ->assertSee('El código no es válido o ha caducado');
    
    $this->assertGuest();
});

it('Impide la introducción de 6 códigos seguidos en 1 minuto', function(){
    $email = fake()->safeEmail();
    User::factory()->create([
        'email' => $email,
        'code' => '1234',
        'code_created_at' => now()
    ]);
    session(['emailAttempt' => $email]);

    visit('/codeaccess')
    ->fill('code', '4321')
    ->click('Aceptar')
    ->assertSee('El código no es válido o ha caducado')
    ->fill('code', '7655')
    ->click('Aceptar')
    ->assertSee('El código no es válido o ha caducado')
    ->fill('code', '6575')
    ->click('Aceptar')
    ->assertSee('El código no es válido o ha caducado')
    ->fill('code', '3476')
    ->click('Aceptar')
    ->assertSee('El código no es válido o ha caducado')
    ->fill('code', '0765')
    ->click('Aceptar')
    ->assertSee('El código no es válido o ha caducado')
    ->fill('code', '3457')
    ->click('Aceptar')
    ->assertSee('Too Many Requests');
});