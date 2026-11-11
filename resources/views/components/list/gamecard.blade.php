@props([
    'id',
    'game_id',
    'igdb_id',
    'cover' => null,
    'name',
    'developers' => [],
    'platforms' => [],
    'rating' => null,
    'rating_count' => null,
    'finished' => false,
    'first_release_date' => null,
    'show' => true,
    'hltb',
])

<li 
    x-init="gamelist.push({{ $igdb_id }}); "
    x-show="{{ (($show === true)?'true':'false') }}"
    draggable="false"
    data-game-user-id="{{ $id }}"
    @dragstart="
        dragged = $el;

        placeholder = document.createElement('li');
        placeholder.style.height = ($el.offsetHeight/1.5) + 'px';
        placeholder.classList.add('placeholder');

        $el.parentNode.insertBefore(placeholder, $el.nextSibling);

        $el.style.opacity = '0.4';
    "
    @dragend="
        dragged = null;
        placeholder = null;
        $el.style.opacity = '1';
        $el.setAttribute('draggable', 'false');
        
        reorder_games(
            $el.dataset.gameUserId, 
            $el.parentNode.dataset.groupId, 
            Array.from($el.parentNode.children).indexOf($el)+1
        );
    "
    @dragover.prevent="
        if (dragged && $el !== dragged && $el !== placeholder) {
            const rect = $el.getBoundingClientRect();
            const after = $event.clientY > rect.top + rect.height / 2;

            $el.parentNode.insertBefore(
                placeholder,
                after ? $el.nextElementSibling : $el
            );
        }
    "    
    class="gamecard mb-2 rounded-xl  {{ (($finished)?'opacity-50 ':'')}}" 
    style="background-image: url( {{ (( ! empty($cover))? route('media.show', $cover) /*asset('storage') .'/'. $cover*/ : '/images/NextToPlay.webp') }} ); background-position: center;background-size:cover; "
>
    <div class="flex rounded-xl border border-secondary shadow-md h-[120px] py-2 px-1 bg-gradient w-full" style="background: 
    linear-gradient(
        to right,
        color-mix(in srgb, var(--color-secondary) 97%, transparent) 10%,
        color-mix(in srgb, var(--color-secondary) 97%, transparent) 40%,
        color-mix(in srgb, var(--color-secondary) 92%, transparent) 60%,
        color-mix(in srgb, var(--color-secondary) 92%, transparent) 80%,
        color-mix(in srgb, var(--color-secondary) 97%, transparent) 95%,
        var(--color-secondary) 100%
    );

    ">
        <div 
            @mousedown="$el.closest('li').setAttribute('draggable', 'true')"
            @mouseup.window="$el.closest('li').setAttribute('draggable', 'false')"
            class="flex w-[6%] space-x-0 content-center cursor-move dragpoint">        
            <x-icon name="mdi-drag-vertical" class="size-5" class="text-secondary-content"/>
        </div>

        <div class="flex w-[19%] items-center {{ (($finished)?'grayscale ':'')}} roundex-lg overflow-hidden">
            @if( ! empty($cover))
            <img src="{{-- route('media.show', $cover) --}}{{ asset('storage') .'/'. $cover }}" alt="{{ $name }}" 
    draggable="false" class="w-full max-w-100 rounded-box">
            @endif
        </div>
        <div class="flex flex-col w-[53%] justify-between text-left py-3 px-3">
            <div class="title text-primary-content font-bold text-base/5 line-clamp-3">{{ $name }}</div>

            <div class="text-xs uppercase font-semibold opacity-60 text-secondary-content">
            @foreach($developers as $i=>$developer)
                    {{ (($i>0)?' | ':'').$developer['name'] }}            
            @endforeach
            </div>
        </div>
        <div class="flex flex-col w-[16%] h-full px-2 py-4 text-secondary-content text-xs">
            @if ( ! empty($hltb) && $hltb[0] != 0 )
                @php
                    foreach($hltb as $k=>$duration){
                        $hours = floor($duration);
                        $decimal = $duration - $hours;
                        $hltb[$k] = $hours.(( $decimal >= 0.25 && $decimal <= 0.75 )? '½':'');
                    }
                @endphp
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-clock" class="size-4 mr-1"/> {{$hltb[0]}}h
            </div>
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-sparkles" class="size-4 mr-1"/> {{ $hltb[1] }}h
            </div>
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-trophy" class="size-4 mr-1"/> {{ $hltb[2] }}h
            </div>
            @endif
        </div>
        <div class="flex flex-col w-[12%] justify-center space-y-2 items-center">
            @foreach($platforms as $platform)
                <span title="{{ $platform['name'] }}">
                    @if( ! empty($platform['icon']))
                        @if( str_contains($platform['icon'], 'ico-') )
                            <x-dynamic-component :component="'icons.' . substr($platform['icon'], 7)" style="opacity:0.7;" class="text-secondary-content size-15 h-{{ (int)substr($platform['icon'], 4,2) }}"/>
                        @else
                            <x-icon name="{{ $platform['icon'] }}" style="opacity:0.7" class="text-secondary-content 
                            {{  (( $platform['name'] === 'Steam')?'size-5':
                                (( $platform['name'] === 'Battle.net')?'size-5':
                                (( strpos($platform['name'], 'PlayStation') !== false )?'size-8':
                                'size-6'))) }}"/>
                        @endif
                    @else
                    <x-icon name="mdi-controller-classic" style="opacity:0.7" class="text-secondary-content size-6"/>
                    @endif
                </span>
            @endforeach
        </div>

        <div class="w-[4%] dropdown dropdown-end">
            <div tabindex="0" role="button" class="avatar cursor-pointer">                 
                <x-icon name="mdi-dots-vertical" class="size-5 text-secondary-content"  data-test="menu-juego-{{ $id }}"/>           
            </div>
            <ul
                tabindex="-1"
                class="menu menu-xs dropdown-content bg-secondary rounded-box z-1 w-52 p-2 shadow space-y-2 text-secondary-content font-bold "
            >
                <li>
                    <form action="{{ route ('games.toggle.finished', $id)}}" method="POST" class="block w-full p-0">
                        @csrf
                        @method('PATCH')                     
                        <button type="submit" class="flex w-full gap-2 text-sm items-center p-1 rounded-lg cursor-pointer hover:text-primary-content" data-test="finalizar-juego-{{ $id }}">
                        @if ($finished)
                            <x-icon name="mdi-checkbox-blank-circle-outline" class="size-4"/> Quitar finalizado
                        @else
                            <x-icon name="mdi-checkbox-marked-circle-outline" class="size-4"/> Finalizado
                        @endif
                        </button>
                    </form>
                </li>
                <li>
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 
                        { 
                            name:'modal-game-edit', 
                            title: 'Modifica los datos del juego', 
                            id: {{ $id }}, 
                            game_id: {{ $game_id }}, 
                            gamename: @js($name), 
                            cover: '{{ $cover }}',
                            developers: '@foreach($developers as $i=>$developer){{ (($i>0)?' | ':'').$developer['name'] }} @endforeach',
                            finished: {{ $finished }},
                            platforms: @json($platforms->pluck('id'))
                        });"
                        class="flex w-full gap-2 text-sm items-center p-1 rounded-lg cursor-pointer hover:text-primary-content"
                        data-test="editar-juego-{{ $id }}"
                    >
                        <x-icon name="mdi-pencil-circle-outline" class="size-4"/> Editar
                    </button>
                </li>
                <li>
                    <form action="{{ route ('games.delete', $id) }}" method="POST" class="block w-full p-0">
                        @csrf
                        @method('DELETE')
                        <x-forms.field type="hidden" name="gameId" value="{{ $id }}" />
                        <button type="submit" class="flex w-full gap-2 text-sm items-center p-1 rounded-lg cursor-pointer hover:text-primary-content" data-test="quitar-juego-{{ $id }}">
                            <x-icon name="mdi-progress-close" class="size-4"/> Quitar
                        </button>
                    </form>
                    
                </li>
            </ul>
        </div>
    </div>
</li>