@props(['action'])
<x-layout>
    <div class="flex flex-col h-screen mx-auto max-w-sm items-center  space-y-5 justify-center">
        
        <div class="flex flex-col items-center bg-white shadow-lg rounded px-8 pt-10 pb-10 text-center">
            <img src="images/NextToPlay.webp" alt="Next To Play, decide tu siguiente juego" width="150" class="center">
            
            <form action="{{ $action }}" method="POST">
                @csrf
                {{ $slot }}
            </form>        
        </div>
    </div>
</x-layout>