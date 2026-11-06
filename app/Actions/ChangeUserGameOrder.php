<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\GameUser;

class ChangeUserGameOrder
{
    /**
     * Modifica el orden de todos los juegos de un grupo a partir del juego indicado y la posición final. 
     * Para ello a todos los ítems del grupo se les asigna su nueva posición en base al índice $k (al empezar en 0 la posición hasta el objeto que se ordena aumenta en uno). Cuando la posición en la que se insertará el juego es encontrada se pasa a aumentarlo en 2, loq ue hace que quede esa posición del orden libre para asignárla. 
     * Se tienen en cuenta los elementos completados aunque no estén visibles (y por lo tanto no entren en el conteo del tablero) o estén colocados al final independientemente de su orden. De esta forma en vez de pasar a ser los últimos, mantienen su orden original alterado solo por las nuevas incorporaciones por encima suyo.
     * Finalmente se asigna la nueva posición y grupo al juego que se ha movido.
     * 
     * @param Request $request: datos pasados por el javascript que controla el movimiento (position y group_id)
     * @param Collection $groupGames: Colección de juegos que conforman el grupo que recibe el juego y hay que reordenar
     * @param GameUser $game: instancia del juego del usuario
     */
    public function changeOrder(Request $request, Collection $groupGames, GameUser $game)
    {

        DB::transaction(function () use($request, $groupGames, $game) {
            $finished=0;
            $found=false; 
            if(!empty($groupGames)){
                foreach($groupGames as $k=>$gameAux){               
                    if($k+1 == $request->position + $finished)
                        $found=true;

                    $gameAux->update([
                        'position' => (( ($k+1 < $request->position + $finished) && ! $found)? $k+1 : $k+2),
                    ]); 
                    if($gameAux->finished && !$found)
                        $finished++; 
                }   
            }

            $game->update([
                'game_group_id' => $request->group_id,
                'position' => $request->position+$finished,
            ]);   
        });

    }
}