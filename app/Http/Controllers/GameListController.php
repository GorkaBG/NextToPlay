<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GameGroup;

class GameListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Auth::user()->gameGroups()->orderBy('order','Asc')->get();
        $userGames = Auth::user()->userGames()->orderBy('finished','Asc')->orderBy('position','Asc')->get();

        return view('games.list', [
            'groups' => $groups,
            'userGames' => $userGames,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GameGroup $group)
    {
        //hacer esto con politica
        if ($group->user_id != Auth::id()){
            return back()->with('error', 'No tienes permiso para hacer esto');;
        }

        return view('games.create', [
            'group' => $group
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
