@props(['titulo' => 'Next To Play: Decide rápidamente tu siguiente juego'])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $titulo }}</title>
</head>
<body>
    {{ $slot }}
</body>
</html>