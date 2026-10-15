<x-layout>
<nav class="flex justify-between mb-4 w-full p-3"><a href="/logout">Logout</a> <a href="{{ route('groups.create') }}" class="btn">Crear grupo</a></nav>
    @if ($groups->count() === 0)
¡Para comenzar crea tu primer grupo de juegos! De esta forma podrás agruparlos según te convenga.    
        <a href="{{ route('groups.create') }}" class="btn">Crear grupo</a>
    @else
        <div class="flex max-w-[1500px] m-auto space-x-5 grid-cols-3">
            @foreach($groups as $group)
                <div class="w-1/3 text-center">
                    {{ $group->groupname }}<br>

                    <a href="{{ route('games.new', $group->id) }}" class="btn mt-3" data-test="crear-juego-{{ $group->id }}">
                        <x-icon name="heroicon-o-plus-circle" class="size-5" />
                        Añadir juego
                    </a>
                </div>
            @endforeach
        <div>
    @endif
</x-layout>