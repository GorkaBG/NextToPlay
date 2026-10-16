<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\GameGroup;
use App\Models\Game;
use App\Models\Developer;
use App\Services\IGDBService;

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
        $request->validate([
            'game' => ['required', 'integer', 'max_digits:6'],
            'group' => ['required', 'integer', 'max_digits:2'],
        ]);

        $game = Game::where('igdb_id', $request->game)->first();

        //si no existe, se crea
        if ( $game === null ){
            $igdb = new IGDBService();
            $game_data = $igdb->game($request->game);
            
            //Guardar Desarrolladores
            $developers = [];
            
            if(!empty($game_data[0]['involved_companies'])){
                
                foreach($game_data[0]['involved_companies'] as $company){
                    //separa los desarrolladores del resto de compañías implicadas
                    if($company['developer']){   
                                
                        $dev = Developer::where ('igdb_id',$company['company']['id'])->first(); 
                        
                        if( $dev === null ){
                            $dev = Developer::create([
                                'igdb_id' => $company['company']['id'],
                                'name' => $company['company']['name'],
                            ]);
                        }
                        $developers[] = $dev->id;    
                    }                    
                }
            }
            
            //guardar imagen
            
            $image_path = "";
            if( isset($game_data[0]['cover']['image_id']) ){
                $image = $game_data[0]['cover']['image_id'].'.jpg';
                $response = Http::get( 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$image );
                
                if($response->successful()){                    
                    if( Storage::disk('public')->put('covers/'.$image, $response->body()) ){
                        $image_path = 'covers/'.$image;
                    }
                }
            }

            //guardar juego
            $game = Game::create([
                'igdb_id' => $game_data[0]['id'],
                'name' => $game_data[0]['name'],         
                'cover' => $image_path,
                'release_date' => $game_data[0]['first_release_date'],
                'total_rating' => round( $game_data[0]['total_rating'],1 ),
                'total_rating_count' => $game_data[0]['total_rating_count'],
            ]);

            $game->developers()->attach($developers);

        }

        //guardar en game_user, tabla de datos de los juegos de un usuario        

        //guarda las plataformas que no existan
        $platforms = [];            
        if(!empty($game_data[0]['platforms'])){
            
            foreach($game_data[0]['platforms'] as $platform){                            
                $plat = Platform::where ('',$company['company']['id'])->first(); 
                    
                if( $dev === null ){
                    $dev = Developer::create([
                            'igdb_id' => $company['company']['id'],
                            'name' => $company['company']['name'],
                        ]);
                    }
                    $developers[] = $dev->id;    
                }                    
            }
        }


        return to_route('games.list');
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
