<?php 

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AccessCodeNotification;
use Illuminate\Support\Facades\Auth;

it ('Crea un usuario', function(){
    Notification::fake();
    $email = fake()->safeEmail();

    visit('/')
        ->fill('email', $email)
        ->click('@btn-entrar')
        ->assertPathIs('/codeaccess');

    $user = User::where('email', $email)->firstOrFail();
    expect( $user->exists() )->toBe(true);

    Notification::assertSentTo($user, function(AccessCodeNotification $notification, array $channels) use($user, $email){
        $html = $notification->toMail($user)->render();
        return in_array('mail', $channels)
            && $user->routeNotificationFor('mail') === $email
            && preg_match(
                '/<div id="code"[^>]*>\s*\d{4}\s*<\/div>/',
                $html
            ) === 1;
    });

});

it('Impide creación de usuario sin introducir un email válido', function(){
    visit('/')
        ->fill('email', fake()->name() )
        ->click('@btn-entrar')
        ->assertScript(
            "document.querySelector('input[name=\"email\"]').checkValidity()",
            false
        )
        ->assertPathIs('/');
});

it('Devuelve al inicio si el email no es válido', function(){    
    $page = visit('/');
    $page->script("document.querySelector('#loginForm').noValidate = true");

    $page->fill('email', fake()->name())
        ->click('@btn-entrar')
        ->assertPathIs('/')
        ->assertSee('Introduce un email válido');
});

it('Impide la introducción de 3 usuarios seguidos en 1 minuto', function(){
    visit('/')
        ->fill('email', fake()->safeEmail())        
        ->click('@btn-entrar')
        ->navigate('/')
        ->fill('email', fake()->safeEmail())
        ->click('@btn-entrar')
        ->navigate('/')
        ->fill('email', fake()->safeEmail())        
        ->click('@btn-entrar')
        ->assertSee('Too Many Requests');
});

