<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\GameGroup;
use App\Http\Controllers\IGDBController;

class GameListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Auth::user()->gameGroups()->latest()->get();

        return view('games.list', [
            'groups' => $groups
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GameGroup $group)
    {
        return view('games.create', [
            'group' => $group
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validation([
            'game' => ['required', 'integer', 'max:6'],
            'group' => ['required', 'integer', 'max:2'],
        ]);

        $game_data = IGDBController::game($request->game);

        $sacar el developer de $game_data['involved_companies']

        $guardar imagen y sacar path

        $game = Game::create([
            'igdb_id' => $game_data['id'],
            'name' => $game_data['name'],
            'developer' => $developer,            
            'cover' => $path,
            'rating' => $game_data['total_rating'],
            'rating_count' => $game_data['total_rating_count'],
        ]);
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
