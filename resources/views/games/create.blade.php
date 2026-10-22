<x-layout>
    <div class="flex flex-col space-y-4 max-w-xl">
        <form action="{{ route('IGDB.search', $group->id) }}" method="POST">
            @csrf
            <x-forms.field label="Búsqueda" name="name"/>   
            <x-forms.button type="submit">Buscar</x-forms.buttom>         
        </form>
       
        @if (isset($games) && count($games)>0) 
        <form action="{{ route('games.store') }}" class="space-x-5" method="POST">
            @csrf
            <x-forms.field type="hidden" name="group" value="{{ $group->id }}"/>
            <ul class="list bg-base-100 rounded-box shadow-md">
            @foreach($games as $game)
                <li class="list-row">
                    <label class="list-row  w-full">
                        
                        <input type="radio" name="game" value="{{ $game['id'] }}" required />
                        @if ( ! empty($game['cover']))
                        <div>
                            <img class="size-15 rounded-box" alt="{{ $game['name'] }}" src="{{ $game['cover']['url']}}"/>
                        </div>
                        @endif
                        <div class="flex flex-col justify-between ">
                            <div>{{ $game['name'] ?? 'desconocido' }} ({{ Carbon\Carbon::createFromTimestamp($game['first_release_date'])->format('Y') }})</div>
                            
                            @if( !empty($game['involved_companies']))
                            <div class="text-xs uppercase font-semibold opacity-60">
                                @foreach($game['involved_companies'] as $company)
                                    @if($company['developer'])
                                        {{ $company['company']['name'] ?? ''}}
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>                    
                        
                    </label>
                </li>
            @endforeach
            </ul>
            <div class="flex flex-col">
                @foreach ( $game['platforms'] as $platform)
                    @if ($platform['id'] == 6)
                        <input type="checkbox" name="platforms[]" id="Steam" value="6|2"/><label for="Steam">Steam</label>
                        <input type="checkbox" name="platforms[]" id="Epic" value="6|3"><label for="Epic">Epic</label>
                        <input type="checkbox" name="platforms[]" id="GOG" value="6|4"><label for="GOG">GOG</label>
                        <input type="checkbox" name="platforms[]" id="Xbox Game Pass" value="6|5"><label for="Game Pass PC">Game Pass PC</label>
                        <input type="checkbox" name="platforms[]" id="CD/DVD" value="6|1"><label for="CD/DVD">CD/DVD</label>
                        <input type="checkbox" name="platforms[]" id="Battle.net" value="6|6"><label for="Battle.net">Battle.net</label>
                        <input type="checkbox" name="platforms[]" id="GeForce Now" value="6|7"><label for="GeForce Now">GeForce Now</label>
                        <input type="checkbox" name="platforms[]" id="EA App" value="6|8"><label for="EA App">EA App</label>
                        <input type="checkbox" name="platforms[]" id="Ubisoft Connect" value="6|9"><label for="Ubisoft Connect">Ubisoft Connect</label>
                        <input type="checkbox" name="platforms[]" id="Rockstar Launcher" value="6|10"><label for="Rockstar Launcher">Rockstar Launcher</label>
                        <input type="checkbox" name="platforms[]" id="Itch.io" value="6|11"><label for="Itch.io">Itch.io</label>
                        <input type="checkbox" name="platforms[]" id="Microsoft Store" value="6|12"><label for="Microsoft Store">Microsoft Store</label>
                        <input type="checkbox" name="platforms[]" id="Amazon Luna" value="6|13"><label for="Amazon Luna">Amazon Luna</label>
                    @else
                        <input type="checkbox" name="platforms[]" id="{{ $platform['name'] }}" value="{{ $platform['id'] }}">{{ $platform['name'] }}</input>
                    @endif
                @endforeach
            </div>
            <x-forms.button type="submit">Añadir Juego</x-forms.buttom> 
        </form>
        @endif
        @error('platforms')
        Debes elegir al menos una plataforma
        @enderror
    </div>


</x-layout>