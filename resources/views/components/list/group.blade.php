@props([
    'group',
    'userGames',
])
<div class=" flex flex-col flex-3 mb-10 bg-primary p-2 border border-secondary rounded-xl">
    <x-list.groupheader group_id="{{ $group->id }}" groupname="{{ $group->groupname }}" color="{{ $group->color }}" icon="{{ $group->icon }}">
        {{ $group->groupname }}
    </x-list.groupheader>
    <div>
        <ul 
            data-group-id="{{ $group->id }}" 
            @dragover.prevent
            @drop="
                if (dragged && placeholder) {
                    placeholder.replaceWith(dragged);
                    dragged.style.opacity = '1';
                    dragged = null;
                    placeholder = null;
                }
            "
            class="list group min-h-50">
        @foreach($userGames as $userGame)
            @if ($userGame->game_group_id == $group->id )
                <x-list.gamecard 
                    :id="$userGame->id"
                    :igdb_id="$userGame->game->igdb_id" 
                    :cover="$userGame->game->cover"
                    :name="((empty($userGame->custom_title))?$userGame->game->name:$userGame->custom_title)"
                    :developers="$userGame->game->developers"
                    :platforms="$userGame->platforms"
                    :rating="$userGame->game->total_rating"
                    :rating_count="$userGame->game->total_rating_count"
                    :finished="$userGame->finished"
                    :first_release_date="$userGame->game->first_release_date"
                    :game_id="$userGame->game_id"
                    :show="$userGame->finished == false || session('show_finished')"
                    hltb="{{ $userGame->position }}"  
                />
            @endif
        @endforeach
        </ul>
    </div>
    
    <a 
        x-data
        @click="$dispatch('open-modal', {name: 'modal-game-new', title: 'Añade un nuevo juego al grupo', id: {{ $group->id }} })"
    class="flex mt-auto p-4 gap-2 justify-center border border-secondary border-dashed border-2 rounded-xl text-secondary-content text-sm font-bold cursor-pointer" data-test="crear-juego-{{ $group->id }}">
        <x-icon name="heroicon-o-plus-circle" class="size-5" />
        Añadir juego
    </a>
</div>