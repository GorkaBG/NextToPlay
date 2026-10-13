<?php

namespace App\Actions;
use App\Models\User;
use App\Notifications\AccessCodeNotification;

class SendAccessCode
{

    /**
     * Genera un código de acceso y lo guarda para el usuario. 
     * Si el usuario no existe, se crea.
     * Finalmente envía una notificación por correo electrónico.
     * 
     *  @param String $email: dirección de correo del usuario
     *  @return User: Usuario al que se asocia el código
     */
    public function handle(String $email)
    {
        $code = random_int(1111, 9999);
        $user = User::where('email', $email)->first();
        
        if(! $user){
            $user = User::create([
                'email' => $email,
                'code' => $code,
                'code_created_at' => now(),
            ]);
        }else{
            //El código anterior queda invalidado al generar uno nuevo        
            $user->update([
                'code' => $code,
                'code_created_at' => now(),
            ]);
        }

        if($user){
            $user->notify(new AccessCodeNotification($user, $code));
        }

    }
}
