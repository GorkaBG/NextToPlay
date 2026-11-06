<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use BladeUI\Icons\Factory;
use App\Models\GameGroup;

class GameGroupController extends Controller
{    
    /**
     * Guarda un nuevo grupo para el usuario. No puede tener 2 del mismo nombre
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string', 
                'min:5', 
                'max:30', 
                Rule::unique('game_groups', 'groupname')->where( fn(Builder $query) => $query->where('user_id', Auth::id() ) )],
            'color' => ['required', 'string', 'hex_color'],
            'icon' => ['nullable', 'string', 'starts_with:heroicon,mdi,si'],
        ], [
            'name.required' => 'Debes darle un nombre no repetido al grupo',
            'name.min' => 'El nombre del grupo debe tener al menos 5 caracteres',
            'name.max' => 'El nombre del grupo no puede superar los 30 caracteres',
            'name.unique' => 'Ya tienes un grupo con ese nombre',
            'color.required' => 'Debes seleccionar un color',
            'color.hex_color' => 'El color seleccionado no es válido',
            'icon.starts_with' => 'El icono debe pertenecer a una de las librerías permitidas',
        ]);

        $userLastGroup = GameGroup::where('user_id', Auth::id())->orderBy('order','DESC')->first();
        $order = (( ! empty($userLastGroup) )? $userLastGroup->order + 1 : 1 );

        GameGroup::create([
            'user_id' => Auth::id(),
            'groupname' => $request->name,
            'color' => $request->color,
            'icon' => $request->icon,
            'order' => $order,
        ]);

        return redirect()->route( 'games.list' )->with('success','El grupo se ha creado correctamente');
    }

    /**
     * Actualiza el grupo del usuario.
     */
    public function update(Request $request, GameGroup $gameGroup): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string', 
                'min:5', 
                'max:30', 
                Rule::unique('game_groups', 'groupname')
                ->where(fn (Builder $query) => $query->where('user_id', Auth::id()))
                ->ignore($gameGroup->id)
            ],
            'color' => ['required', 'string', 'hex_color'],
            'icon' => ['string', 'starts_with:heroicon,mdi'],
        ], [
            'name.required' => 'Debes darle un nombre no repetido al grupo',
            'name.min' => 'El nombre del grupo debe tener al menos 5 caracteres',
            'name.max' => 'El nombre del grupo no puede superar los 30 caracteres',
            'name.unique' => 'Ya tienes un grupo con ese nombre',
            'color.required' => 'Debes seleccionar un color',
            'color.hex_color' => 'El color seleccionado no es válido',
            'icon.starts_with' => 'El icono debe pertenecer a una de las librerías permitidas',
        ]);

        if( Gate::denies('workWith', $gameGroup) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        }        

        $gameGroup->update([
            'groupname' => $request->name,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);

        return redirect()->route( 'games.list' )->with('success','El grupo se ha modificado correctamente');
    }

    /**
     * Elimina un grupo del usuario. Las asignaciones de juegos de ese grupo se eiminarán en cascada.
     */
    public function destroy(GameGroup $gameGroup): RedirectResponse
    {
        if( Gate::denies('workWith', $gameGroup) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        } 

        $gameGroup->delete();
        return back()->with('success', 'Se ha eliminado el grupo y su contenido');
    }

    /**
     * Busca iconos dentro de las librerías instaladas cuyo nombre contenga el texto indicado para mostrar un listado seleccionable. NOTA: Función hecha con ayuda de la IA
     */
    public function iconSearch(Request $request, Factory $factory): JsonResponse
    {
        $search = strtolower($request->get('q', ''));
        $allIcons = [];

        // Iteramos sobre el array de configuraciones de los sets instalados
        foreach ($factory->all() as $set) {
            $prefix = $set['prefix'] ?? '';
            $paths = $set['paths'] ?? [];

            if (empty($prefix) || empty($paths)) {
                continue;
            }

            // Recorremos cada directorio físico asignado a este set de iconos
            foreach ($paths as $path) {
                if (!File::isDirectory($path)) {
                    continue;
                }

                // Obtenemos todos los archivos .svg del directorio
                $files = File::files($path);

                foreach ($files as $file) {
                    if ($file->getExtension() === 'svg') {
                        // Nombre del archivo sin la extensión .svg
                        $iconName = $file->getFilenameWithoutExtension();
                        
                        // Formamos el nombre completo: "prefix-nombre" (ej: heroicon-o-user)
                        $fullName = strtolower($prefix . '-' . $iconName);

                        // Filtramos si hay una búsqueda activa
                        if (empty($search) || str_contains($fullName, $search)) {
                            $allIcons[] = $fullName;
                        }
                    }
                }
                unset($files);//liberación de memoria. $files con miles archivos pega duro.
            }
        }

        // Ordenamos alfabéticamente y eliminamos posibles duplicados
        $allIcons = array_unique($allIcons);
        sort($allIcons);
        // Limitamos el resultado a 42 para que queden bien en el cuadro
        return response()->json(array_slice($allIcons, 0, 42));

    }    

    /**
     * Obtiene el svg de un icono para mostrarlo de forma dinamica. NOTA: Función hecha con ayuda de la IA
     */
    public function getIcon (Request $request, Factory $factory): Response
    {
        $name = $request->get('name');
        try {
            // SVG directo listo para inyectar con x-html
            return response($factory->svg($name)->toHtml(), 200)
                    ->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            return response('', 404);
        }
    }
}
