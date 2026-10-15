<x-layout>
    <div class="flex flex-col space-y-4 max-w-xl">
        <form action="{{ route('IGDB.search', $group) }}" method="POST">
            @csrf;
            <x-forms.field label="Búsqueda" name="name"/>   
            <x-forms.button type="submit">Buscar</x-forms.buttom>         
        </form>
        @if (isset($games))
        <form action="{{ route('games.store', $group) }}" class="space-x-5">
            @csrf;
            <x-forms.field type="hidden" name="group" />
            <ul class="list bg-base-100 rounded-box shadow-md">
            @foreach($games as $game)
                <li class="list-row">
                    <label class="list-row  w-full">
                        
                        <input type="radio" name="game" value="{{ $game['id'] }}" required>
                        <div>
                            <img class="size-15 rounded-box" alt="{{ $game['name'] }}" src="{{ $game['cover']['url']}}"/>
                        </div>
                        <div class="flex flex-col justify-between ">
                            <div>{{ $game['name'] }} ({{ Carbon\Carbon::createFromTimestamp($game['first_release_date'])->format('Y') }})</div>
                            <div class="text-xs uppercase font-semibold opacity-60">
                                @foreach($game['involved_companies'] as $company)
                                    @if($company['developer'])
                                        {{ $company['company']['name'] }}
                                    @endif
                                @endforeach
                            </div>
                        </div>                    
                        
                    </label>
                </li>
            @endforeach
            </ul>

            <x-forms.button type="submit">Añadir Juego</x-forms.buttom> 
        </form>
        @endif

    </div>


</x-layout>