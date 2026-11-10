<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class HLTBService
{
    /**
     * Obtiene un token de HTLB
     * 
     * @return string: devuelve el token obtenido
     */
    public function getToken()
    {
        $timestamp = (int) floor(microtime(true) * 1000);

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:158.0) Gecko/20100101 Firefox/158.0',
            'Referer' => 'https://howlongtobeat.com/',
        ])->get('https://howlongtobeat.com/api/search/site/init', [
            't' => $timestamp,
        ]);

        return $response->json('token');
    }

    /**
     * Realiza una busqueda en How Long to Beat utilizando sus propios endpoints y una función para filtrar los resultados y obtener el mejor.
     * 
     * @param string $name: texto de la búsqueda (nombre parcial del juego)
     * @param string $token: el token de HLTB obtenido previamente
     * 
     * @return ?array: Devuelve un array con los datos del juego exacto
     */
    public function searchGame(string $name, string $token): ?Array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:158.0) Gecko/20100101 Firefox/158.0',
            'Referer' => 'https://howlongtobeat.com/',
            'Origin' => 'https://howlongtobeat.com',
            'x-auth-token' => $token,
        ])->post('https://howlongtobeat.com/api/search/site', [
            'searchType' => 'games',
            'searchTerms' => [$name],
            'searchPage' => 1,
            'size' => 20,
            'searchOptions' => [
                'games' => [
                    'userId' => 0,
                    'platform' => ['mode' => 'include', 'values' => []],
                    'sortCategory' => 'popular',
                    'rangeCategory' => 'main',
                    'rangeTime' => ['min' => null, 'max' => null],
                    'gameplay' => [
                        'perspective' => ['mode' => 'include', 'values' => []],
                        'flow' => ['mode' => 'include', 'values' => []],
                        'genre' => ['mode' => 'include', 'values' => []],
                    ],
                    'year' => ['mode' => 'include', 'values' => []],
                    'modifier' => '',
                ],
                'users' => ['sortCategory' => 'postcount'],
                'lists' => ['sortCategory' => 'follows'],
                'filter' => '',
                'sort' => 0,
                'randomizer' => 0,
            ],
            'useCache' => true,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $this->getExactGame($name, $response->json('data'));
    }

    /**
     * En este método se filtran los resultados devueltos por HowLongToBeat para buscar el juego exacto, ignorando DLCs. Normalmente, al buscarse con el nombre de IGDB será el primer resultado, pero no se puede asegurar. Por ello si no encuentra el nombre exacto, devuelve el primer resultado que sea de tipo 'game'.
     * 
     * @param string $name: Nombre del juego que se busca, obtenido de IGDB
     * @param array $results: resultados de búsqueda que h devuelto HLTB
     * 
     * @return array: Devuelve el subarrayconcreto del juego encontrado o null si no se encontrase nada.
     */
    private function getExactGame(string $name, array $results): ?Array
    {
        foreach($results as $game){
            if( $game['game_type'] === 'game' && ( $game['game_name'] == $name || $game['game_alias'] == $name ) )
                return $game;
        }

        foreach($results as $game){
            if( $game['game_type'] === 'game')
                return $game;
        }

        return null;
    }
}