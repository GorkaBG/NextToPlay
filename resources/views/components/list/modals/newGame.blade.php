<x-modal name="modal-game-new">
    <form 
        action="{{ route('games.store') }}"
        method="POST"  
        x-data="{groupId: 0 }"
        @open-modal.window="
            if ($event.detail.name === name){
                if($event.detail.id){
                    groupId=$event.detail.id;
                    $nextTick(() => {
                        $el.querySelector('#gameSelector').focus();
                    });
                }else
                    show=false;
            }
        "
        @close-modal.window="$el.reset();"
        class="flex flex-col w-full space-y-2 text-secondary-content">
        @csrf

        <input type="hidden" name="group" :value="groupId" />

        <x-forms.game-selector name="game" label="Selecciona el juego" x-model="game" />

        <x-list.modals.buttons />
        
    </form>
    
</x-modal> 