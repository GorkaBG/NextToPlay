<!-- modal de nuevo/editar grupo -->
<x-modal name="modal-group">   
    <form 
        x-data="{ action: '{{ route('groups.store') }}', editing: false }"
        :action="action"
        method="POST"  
        @open-modal.window="
            if ($event.detail.name === name){                            
                if($event.detail.id) {
                    action = '/groups/'+$event.detail.id;                   
                    editing = true;
                }   
                $nextTick(() => {
                    $el.querySelector('#name').focus();
                });
            }
        "
        @close-modal.window="$el.reset();"
        class="flex flex-col w-full space-y-2 text-secondary-content">
        @csrf
        <template x-if="editing">
            <input type="hidden" name="_method" value="PATCH">
        </template>

        <x-forms.field name="name" label="Nombre del grupo" class="w-full" x-model="groupname" required/>

        <x-forms.field name="color" type="color" label="Selecciona un color" class="max-w-2xl" required  x-model="color" />
    
        <x-forms.icon-selector name="icon" label="Selecciona un icono" x-model="icon" />

        <x-list.modals.buttons />

    </form>
</x-modal>