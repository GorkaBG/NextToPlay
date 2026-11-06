<!-- modal de nuevo/editar grupo -->
<x-modal name="modal-group">   
    <form 
        x-data="{ action: '{{ route('groups.store') }}', editing: false }"
        :action="action"
        method="POST"  
        @open-modal.window="
            if ($event.detail.name === name){                            
                if($event.detail.id) {
                    action = '{{ route('groups.update', ['gameGroup' => '__ID__']) }}'.replace('__ID__', $event.detail.id);                   
                    editing = true;
                }   
                $nextTick(() => {
                    $el.querySelector('#name').focus();
                });
            }
        "
        @close-modal.window="$el.reset();action = `{{ route('groups.store') }}`"
        class="flex flex-col w-full space-y-2 text-secondary-content">
        @csrf
        <template x-if="editing">
            <input type="hidden" name="_method" value="PATCH">
        </template>

        <x-forms.field name="name" required label="Nombre del grupo" maxlength="30" x-model="groupname" data-test="nombre-grupo" class="w-full" />

        <x-forms.field name="color" required type="color" x-model="color" label="Selecciona un color" data-test="color-grupo" class="max-w-2xl" />
    
        <x-forms.icon-selector name="icon" x-model="icon" data_test="icono-grupo" label="Selecciona un icono" />

        <x-modals.buttons type="grupo" />

    </form>
</x-modal>