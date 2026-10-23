<x-layout>
    @if ($groups->count() === 0)
        ¡Para comenzar crea tu primer grupo de juegos! De esta forma podrás agruparlos según te convenga.    
        <a href="{{ route('groups.create') }}" class="btn">Crear grupo</a>
    @else
        <div class="grid max-w-[1500px] m-auto mt-15 space-x-5 xl:grid-cols-3 lg:grid-cols-2 xs:grid-cols-1">
            @foreach($groups as $group)
                <x-list.group :group="$group" :userGames="$userGames" />                
            @endforeach
        </div>
    @endif

    <x-dialog name="confirm-dialog">
        <p class="py-4">
            El grupo se eliminará de forma definitiva junto a su contenido.
            ¿Desea continuar?
        </p>

        <div class="flex justify-between modal-action">
            <form method="dialog">
                @csrf
                <button class="btn">Cancelar</button>
            </form>
            <form 
                :action="`/groups/${itemId}`"
                method="POST">
                @csrf
                @method('DELETE')                
                <button type="submit" ref="`/groups/${itemId}/delete`" class="btn border-0 bg-error text-error-content">Sí, elimínalo</button>
            </form>
        </div>
    </x-dialog>

    @if( session('error') || session('success') )
        <x-alert type="{{ ((session('success'))?'success':'warning') }}" >
            {{ session('error') ?? session('success')  }}
        </x-alert>
    @endif   

    

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

            <div class="flex justify-between items-center pt-4">
                <a class="text-primary-content font-bold cursor-pointer" @click="show = false;$dispatch('close-modal')">Cerrar</a>

                <x-forms.button type="submit" data-test="btn-guardar-grupo">Guardar</x-forms.button>
            </div>

        </form>
    </x-modal>



</div>



</x-layout>