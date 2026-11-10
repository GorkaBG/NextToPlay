<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class IGDBService
{
    private ?string $token;

    public function __construct()
    { 
        $this->token = Cache::get('igdb_access_token');

        if( ! $this->token){
            $this->token = $this->get_access_token();
        }        
    }

    private function get_access_token()
    {
        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'client_id' => config('services.igdb.client_id'),
            'client_secret' => config('services.igdb.client_secret'),
            'grant_type' => 'client_credentials',
        ]);

        $response->throw();
        $token = $response->json('access_token');
        $expires = $response->json('expires_in');
        
        Cache::put(
            'igdb_access_token', 
            $token, 
            now()->addSeconds( $expires - 60) 
        );

        return $token;
    }

    private function igdb_request(array $headers, string $body){
        $response = Http::withHeaders($headers)
        ->withBody($body, 'text/plain')
        ->post('https://api.igdb.com/v4/games');

        if ($response->status() === 401) { // Renueva el token y repite si IGDB lo ha anulado
            $this->token = $this->get_access_token();

            $response = Http::withHeaders($headers)
            ->withBody($body, 'text/plain')
            ->post('https://api.igdb.com/v4/games');
        }

        $response->throw();

        return $response;
    }

    public function search(String $search)/*: ?JsonResponse*/
    {
        $body = 'fields 
                    name, 
                    cover.url, 
                    involved_companies.developer,
                    involved_companies.company.name,
                    first_release_date,
                    platforms.name;
                where  
                    ( name ~ "'.$search.'"* | name ~ *" '.$search.'"* ) 
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
            'Client-ID' => config('services.igdb.client_id'),
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];

        $response = $this->igdb_request($headers, $body);

        return $response->successful() ? $response->json() : [];

    }

    public function search2(Request $request)/*: ?JsonResponse*/
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
            'Client-ID' => config('services.igdb.client_id'),
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
        
        $response = $this->igdb_request($headers, $body);
        
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
            'Client-ID' => config('services.igdb.client_id'),
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
        
        $response = $this->igdb_request($headers, $body);
        
        return $response->successful() ? $response->json() : [];
    }
}
