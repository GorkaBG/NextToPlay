@props([
    'id',
    'cover' => null,
    'name',
    'developers' => [],
    'platforms' => [],
    'rating' => null,
    'rating_count' => null,
    'finished' => false,
    'first_release_date' => null,
    'hltb' => null,
])
<li 
    class=" gamecard mb-2 rounded-box border border-secondary {{ (($finished)?'opacity-50 ':'')}}" 
    style="background-image: url( {{ asset('storage') .'/'. $cover }} ); background-position: center;background-size:cover; ">
    <div class="flex shadow-md h-[120px] py-2 px-1 bg-gradient w-full" style="background: 
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
        <div class="flex w-[6%] space-x-0 content-center ">        
            <x-icon name="mdi-drag-vertical" class="size-5" class="text-secondary-content"/>
        </div>

        <div class="flex w-[19%] items-center {{ (($finished)?'grayscale ':'')}}">
            <img src="{{ asset('storage') .'/'. $cover }}" alt="{{ $name }}"  class="w-full rounded-box">
        </div>
        <div class="flex flex-col w-[53%] justify-between text-left py-4 px-3">
            <div class="title text-primary-content font-bold text-base">{{ $name }}</div>

            <div class="text-xs uppercase font-semibold opacity-60 text-secondary-content">
            @foreach($developers as $i=>$developer)
                    {{ (($i>0)?' | ':'').$developer['name'] }}            
            @endforeach
            </div>
        </div>
        <div class="flex flex-col w-[15%] h-full px-2 py-4 text-secondary-content text-xs">
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-clock" class="size-4 mr-1"/> 30h
            </div>
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-sparkles" class="size-4 mr-1"/> 20h
            </div>
            <div class="flex flex-1 items-center">
                <x-icon name="heroicon-o-trophy" class="size-4 mr-1"/> 150h
            </div>
        </div>
        <div class="flex flex-col w-[13%] justify-center space-y-2 items-center">
            @foreach($platforms as $platform)
                <span title="{{ $platform['name'] }}">
                    @if( ! empty($platform['icon']))
                    <x-icon name="{{ $platform['icon'] }}" style="opacity:0.7" class="text-secondary-content 
                    {{  (( $platform['name'] === 'Steam')?'size-5':
                        (( $platform['name'] === 'Battle.net')?'size-5':
                        (( strpos($platform['name'], 'PlayStation') !== false )?'size-8':
                        'size-6'))) }}"/>
                    @else
                    <x-icon name="mdi-controller-classic" style="opacity:0.7" class="text-secondary-content size-6"/>
                    @endif
                </span>
            @endforeach
        </div>

        <div class="w-[4%] dropdown dropdown-end">
            <div tabindex="0" role="button" class="avatar cursor-pointer">                 
                <x-icon name="mdi-dots-vertical" class="size-5 text-secondary-content" />           
            </div>
            <ul
                tabindex="-1"
                class="menu menu-xs dropdown-content bg-secondary rounded-box z-1 w-52 p-2 shadow space-y-2 text-secondary-content  "
            >
                <li>
                    <form action="{{ route ('games.toggle.finished')}}" method="POST" class="block w-full p-0">
                        @csrf
                        <x-forms.field type="hidden" name="gameId" value="{{ $id }}" />
                        <button type="submit" class="flex w-full gap-2 text-sm items-center p-1 rounded-lg cursor-pointer hover:text-primary-content">
                        @if ($finished)
                            <x-icon name="mdi-checkbox-blank-circle-outline" class="size-4"/> Quitar finalizado
                        @else
                            <x-icon name="mdi-checkbox-marked-circle-outline" class="size-4"/> Finalizado
                        @endif
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route ('games.delete', $id) }}" method="POST" class="block w-full p-0">
                        @csrf
                        @method('DELETE')
                        <x-forms.field type="hidden" name="gameId" value="{{ $id }}" />
                        <button type="submit" class="flex w-full gap-2 text-sm items-center p-1 rounded-lg cursor-pointer hover:text-primary-content">
                            <x-icon name="mdi-progress-close" class="size-4"/> Quitar
                        </button>
                    </form>
                    
                </li>
            </ul>
        </div>
    </div>
</li>