<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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

            Auth::login($user);
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
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
