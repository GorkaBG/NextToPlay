<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Actions\SendAccessCode;

class UserController extends Controller
{
    /**
     * Muestra el perfil de usuario [No existe en esta versión]
     */
    public function index()
    {
        return view('/profile');
    }

    /**
     * Envía el código de acceso al usuario. Si e'usuario no existe, previamente lo crea con el email facilitado.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email']
        ]);
        
        (new SendAccessCode)->handle($request->email);

        session(['emailAttempt' => $request->email]);
        return to_route('codeaccess');
    }

    /**
     * Cambia el tema preferido del usuario de claro a oscuro o viceversa
     */
    public function toggleTheme()
    {
        if( Auth::user()->theme =='dark' ){
            Auth::user()->update(['theme'=>'light']);
        }else{
            Auth::user()->update(['theme' => 'dark']);
        }
        return back();
    }
}
