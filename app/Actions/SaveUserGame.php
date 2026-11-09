<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\GameGroup;
use App\Models\GameUser;
use App\Models\Game;
use App\Models\Platform;
use App\Models\Developer;
use App\Services\IGDBService;
use App\Services\HLTBService;

class SaveUserGame
{
    /**
     * Asigna el juego a un usuario dentro de una categoría. Esto tiene varios pasos y subpasos.
     * 1. Comprueba si los datos del juego de IGDB ya están almacenados. Si no lo están:
     *  1.1 Obtiene los datos deseados de IGDB
     *  1.2 Guarda su imagen de cover
     *  1.3 Guarda sus desarrolladores
     *  1.4 Guarda la información del juego, incluyendo al ruta local a su cover y las relaciones con sus dasarroladores
     *  1.5 Comprueba que sus plataformas existan, si no existen en la BD se guardan.
     * 2. Comprueba si el usuario ya tiene ese juego asignado (no se permiten repetidos). Si no lo tiene:
     *  2.1 Asigna el juego creado al usuario
     *  2.1 Guarda las plataformas en las que tiene el juego
     * 
     *  @param Request $request: Formulario enviado, contiene:
     *      game => id en IGDB del juego 
     *      group => grupo en el que se asigna el juego
     *      platforms => id en IGDB de las plaformas seleccionadas (En el caso especial del PC, donde hay tiendas predefinidas, tiene formado "igdb_id|platform_id")
     *  @return bool: true si se ha asignado, false en caso contrario.
     */
    public function handle(Request $request)
    {
        $game = Game::where('igdb_id', $request->game)->first();

        if ( $game === null ){
            $game_data = (new IGDBService())->game($request->game);

            if( empty($game_data) || ! isset($game_data[0]['id']) || !is_int($game_data[0]['id']))
                //return false;//game() ha fallado
                return ["error","Ocurrió un problema"];

            $duration = $this->getGameDuration($game_data[0]['name']);

            $image_path = $this->storeImage($game_data[0]['cover']['image_id'] ?? "");

            $game = DB::transaction(function () use($game_data, $image_path, $duration) {                 

                $developers = $this->storeDevelopers($game_data[0]['involved_companies'] ?? []);

                $game = $this->storeGame($game_data[0], $image_path, $duration);

                $game->developers()->attach($developers);

                $this->storePlatforms($game_data[0]['platforms'], $game);

                $this->storeGamePlatforms($game_data[0]['platforms'], $game);

                return $game;
            });
        }

        if( ! GameUser::where('user_id', Auth::id())->where('game_id', $game->id)->exists()){
            $gameUser = DB::transaction(function () use($request, $game) { 
                $gameUser = $this->storeGameUser($game->id, $request->group);

                if( ! empty($request->platforms) && !empty($gameUser ) )
                    $this->storeGameUserPlatforms($gameUser, $request->platforms);
                
                return $gameUser;
            });
        }else{
            return ["error","El juego ya está en uno de los grupos"];
        }

        if (! empty($gameUser) ){
            return ["success", "Juego añadido al listado"];
        }else{
            return ["error", "Ocurrió un error"];
        }

    }

    /**
     * Almacena en la base de datos los desarrolladores de un juego si no existen ya. La información que devuelve IGDB mezcla todas las compañías involucradas, no solo los desarrolladores principales, por lo que se recorre para separarlos (['developer']==true).
     * 
     * @param array $involved_companies: contiene todas las compañías involucradas en el desarrollo, tanto desarrolladores principales como apoyos y consultores.
     * @return array: devuelve un array con los ids de los desarrolladores en la base de datos.
     */
    private function storeDevelopers(Array $involved_companies): array
    {        
        $developers = [];            
        if( ! empty($involved_companies)){
            
            foreach($involved_companies as $company){
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
        return $developers;
    }

    /**
     * Reconstruye la url de IGDB de tamaño cover_big y guarda una copia en disco dentro de public/covers. 
     * 
     * @param string $imageId: id alfanumérico de la imagen cover que se obtiene de IGDB.
     * @return string: ruta relativa local a la imagen guardada.
     */
    private function storeImage(?String $imageId): string
    {
        $image_path = "";
        if( ! empty($imageId) ){
            $image = $imageId.'.jpg';
            $response = Http::get( 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$image );
            
            if($response->successful()){                    
                if( Storage::disk('public')->put('covers/'.$image, $response->body()) ){
                    $image_path = 'covers/'.$image;
                }
            }
        }
        return $image_path;
    }

    /**
     * Reliza una búsqueda un HLTB utilizando el servicio contruido a tal efecto, que obtiene el mejor resultado
     * @param string $name: texto de la búsqueda (nombre del juego de IGDB)
     * @return array: devuelve un array, vacío si no encuentra nada o con los valores de las 3 duraciones (Main, Main + Extra y Completionist)
     */
    private function getGameDuration(String $name): Array
    {
        $data = [];
        $hltb = new HLTBService();
        $token = $hltb->getToken();
        if(!empty($token)){
            $hltb_results = $hltb->searchGame($name, $token);
            
            if(!empty ($hltb_results) ){
                $data[] = round($hltb_results['comp_main'] / 60 / 60, 1);
                $data[] = round($hltb_results['comp_plus'] / 60 / 60, 1);
                $data[] = round($hltb_results['comp_100'] / 60 / 60, 1);
            }
        }
        return $data;
    }

    /**
     * Guarda los datos generales de un juego obtenidos de IGDB.
     * 
     * @param array @game_data: datos del juego obtenidos de IGDB. Contiene el id de IGDB, el nombre del juego, la fecha de primer lanzamiento, su rating y el número de valoraciones.
     * @param string $image_path: ruta relativa local de la imagen cover.
     * @param array $duration: duración del juego en HLTB
     */
    private function storeGame(Array $game_data, String $image_path, Array $duration): Game
    {
        $game = Game::create([
                'igdb_id' => $game_data['id'],
                'name' => $game_data['name'],         
                'cover' => $image_path,
                'release_date' => $game_data['first_release_date'],
                'total_rating' => round( $game_data['total_rating'] ?? 0,1 ),
                'total_rating_count' => $game_data['total_rating_count'] ?? 0,
                'duration' => $duration,
            ]);

        return $game;    
    }

    /**
     * Guarda las plataformas que no existan ya en la base de datos. Esto permite que esta tabla vaya creciendo de forma natural según se añadan juegos en plataformas no definidas, y asegura que un juego no estará disponible en una plaforma no contemplada. Para evitar múltiples llamadas a la base de datos, se obtiene un array con las igdb_id de todas las plataformas y se comprueba que cada plataforma del juego esté entre ellas.
     * 
     * @param array $gamePlatforms: Plataformas que devuelve IDGB del juego.
     */
    private function storePlatforms(Array $gamePlatforms): void
    {
        if( ! empty($gamePlatforms)){
            $dbplatform_igdb = Platform::select('igdb_id')->distinct('igdb_id')->get()->pluck('igdb_id')->toArray();

            foreach($gamePlatforms as $platform){  
                if( ! in_array( $platform['id'], $dbplatform_igdb ) ) {
                    Platform::create([
                        'igdb_id' => $platform['id'],
                        'name' => $platform['name'],
                    ])->id;                     
                }

            }
        }
    }

    /**
     * Obtiene un array con los ids de la base de datos de todas las plataformas en las que se encuentra el juego en IGDB y los asocia en la tabla pivote. De esta forma tenemos guardadas todas las plataformas disponibles, utilizado en la edición de un juego para no tener que volver a hacer la petición a IGDB
     * 
     * @param array $platforms: plataformas devueltas por IGDB donde está disponible el juego (contiene id y el nombre)
     * @param Game $game: instancia del juego para asignar la relación
     */
    public function storeGamePlatforms(Array $platforms, Game $game): void
    {
        $db_platforms = Platform::whereIn('igdb_id', array_column($platforms, 'id') )->pluck('id')->toArray();

        $game->platforms()->attach($db_platforms);
    }

    /**
     * Crea el registro de la asociación entre usuario y juego en game_users.
     * 
     * @param int $gameId: id del juego a asignar en la base de datos.
     * @param int $groupId: id del grupo del usuario en donde estará asignado el juego.
     * @return GameUser: instancia de la relación insertada en la tabla pivote game_users.
     */
    private function storeGameUser(int $gameId, int $groupId): GameUser
    {
        $position = GameUser::where('user_id', Auth::id())->where('game_group_id', $groupId)->count() + 1;

        $gameUser = GameUser::create([
            'user_id' => Auth::id(),
            'game_id' => $gameId,
            'game_group_id' => $groupId,
            'custom_title' => null,
            'custom_cover' => null,
            'position' => $position
        ]);

        return $gameUser;
    }

    /**
     * Almacena las plataformas en las que un usuario tiene un juego.
     * 
     * @param GameUser $gameUser: instancia de la relación juego-usuario quese relacionará con las plataformas.
     * @param array $platforms: Array obtenido del formulario donde el usuario marca las plataformas. Contiene los id en IGDB de las plaformas seleccionadas (En el caso especial del PC, id = 6, donde se relaciona con tiendas predefinidas en lugar de la plataforma, tiene formado "igdb_id|platform_id") 
     */
    private function storeGameUserPlatforms(GameUser $gameUser, Array $platforms): void
    {
        $db_platforms = Platform::select('id','igdb_id')->get();
        $db_platforms_array = array_column($db_platforms->toArray(), 'id', 'igdb_id');
        $gamePlatforms = [];

        foreach($platforms as $platform){

            $explode_platform = explode('|', $platform);

            if($explode_platform[0] == 6){//Caso específico del PC
                $gamePlatforms[] = (int)$explode_platform[1];
            }else{  
                $gamePlatforms[] = $db_platforms_array[$explode_platform[0]];
            }

        }
        $gameUser->platforms()->attach($gamePlatforms);
    }
}
