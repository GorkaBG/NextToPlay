@foreach($games as $game)
<div class="w-1/3 text-center">
    {{ $game->name }}<br>
    {{ dump($game)}}
</div>
@endforeach