<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class IGDBService
{
    private string $token;

    public function __construct()
    {
        $this->token = (( session('access_token') /* y no ha expirado */ )? $token = session('access_token') : $this->access_token() );
    }

    private function access_token()
    {
        $response = Http::withHeaders([
            'client_id' => env('IGDB_CLIENT'),
            'client_secret' => env('IGDB_SECRET'),
            'grant_type' => 'client_credentials',
        ])
        ->post('https://id.twitch.tv/oauth2/token?client_id='.env('IGDB_CLIENT').'&client_secret='.env('IGDB_SECRET').'&grant_type=client_credentials');

       
        if($response->successful()){
            session( ['access_token' => $response->json('access_token')] );
            session( ['access_token_expires' =>$response->json('expires_in')] ); 
            return $response->json('access_token');
        }

    }

    public function search(Request $request)/*: ?JsonResponse*/
    {
        $request->validate([
            'name' => ['required', 'string', 'min:4']
        ]);

        $body = 'fields 
                    name, 
                    cover.url, 
                    involved_companies.developer,
                    involved_companies.company.name,
                    first_release_date,
                    platforms.name;
                where  
                    ( name ~ "'.$request->name.'"* | name ~ *" '.$request->name.'"* ) 
                    & game_type = (0, 2, 4) 
                    & game_status = null 
                    & version_parent = null 
                    & parent_game = null 
                    & platforms!= null 
                    & platforms != (52,55,34,39) 
                    & involved_companies.developer = true 
                    & first_release_date!= null;
                sort total_rating_count desc; 
                limit 9;';

        $headers = [
            'Client-ID' => env('IGDB_CLIENT'),
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
        $response = Http::withHeaders($headers)
        ->withBody($body, 'text/plain')
        ->post('https://api.igdb.com/v4/games');
        
        
        return $response->successful() ? $response->json() : [];

    }

    public function game(int $game_id)/*: ?JsonResponse*/
    {
        $body = 'fields 
                    name, 
                    cover.image_id, 
                    involved_companies.developer,
                    involved_companies.company.name,
                    first_release_date,
                    platforms.name,
                    total_rating,
                    total_rating_count;
                where  
                    id = '.$game_id.';
                ';

        $headers = [
            'Client-ID' => env('IGDB_CLIENT'),
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
        $response = Http::withHeaders($headers)
        ->withBody($body, 'text/plain')
        ->post('https://api.igdb.com/v4/games');
        
        return $response->successful() ? $response->json() : [];
    }
}
