@props(['titulo' => 'Next To Play: Decide rápidamente tu siguiente juego'])
<!DOCTYPE html>
<html lang="es" data-theme="{{ 
    ( (auth()->check())? ((auth()->user()->theme ==='dark')?'moon':'sun') : 'moon' ) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $titulo }}</title>
</head>
<body>
    @if( Illuminate\Support\Facades\Auth::id() )
        <x-navbar/>
    @endif
    <main class="flex flex-row w-full justify-center">
        {{ $slot }}
    </main>
    
</body>
</html>