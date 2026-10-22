@props([
    'group',
    'userGames',
])
<div class=" flex flex-col flex-3 mb-10 bg-primary p-2 border border-secondary rounded-xl">
    <x-list.groupheader group_id="{{ $group->id }}" color="{{ $group->color }}" icon="{{ $group->icon }}">
        {{ $group->groupname }}
    </x-list.groupheader>
    <div>
        <ul class="list group">
        @foreach($userGames as $userGame)
            @if ($userGame->game_group_id == $group->id &&
                ($userGame->finished == false || session('show_finished') ))
                <x-list.gamecard 
                    :id="$userGame->id" 
                    :cover="$userGame->game->cover"
                    :name="((empty($userGame->custom_name))?$userGame->game->name:$userGame->custom_name)"
                    :developers="$userGame->game->developers"
                    :platforms="$userGame->platforms"
                    :rating="$userGame->game->total_rating"
                    :rating_count="$userGame->game->total_rating_count"
                    :finished="$userGame->finished"
                    :first_release_date="$userGame->game->first_release_date"
                    hltb=""  
                />
            @endif
        @endforeach
        </ul>
    </div>
    <a href="{{ route('games.new', $group->id) }}" class="flex mt-auto p-4 gap-2 justify-center border border-secondary border-dashed border-2 rounded-xl text-secondary-content text-sm font-bold" data-test="crear-juego-{{ $group->id }}">
        <x-icon name="heroicon-o-plus-circle" class="size-5" />
        Añadir juego
    </a>
</div>