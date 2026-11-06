<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Intenta iniciar sesión con el email y código de acceso facilitados. el código tiene una validez de 5 minutos
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users'],
            'code' => ['required', 'numeric', 'min_digits:4', 'max_digits:4'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if(Hash::check($request->code, $user->code)
            && $user->code_created_at->gt(now()->subMinutes(5)) ){

            Auth::login($user, true);
            $request->session()->regenerate();

            $user->update([
                'code' => null,
                'code_created_at' => null,
            ]);

            return to_route('games.list');
        }else
            return to_route('codeaccess')->with('error', true);
    }

    /**
     * Cierra la sesión del usuario
     */
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
