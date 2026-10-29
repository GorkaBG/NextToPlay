<x-layout>


    @if ($groups->count() === 0)
        <div class="flex flex-col mt-15 w-full  max-w-xl text-center justify-center text-primary-content ">
            <p>¡Para comenzar crea tu primer grupo de juegos! De esta forma podrás agruparlos según te convenga.</p>    
        
            <a 
                x-data
                @click="$dispatch('open-modal', {name: 'modal-group', title: 'Crea un nuevo grupo para tus juegos'})"
                class="btn mt-5"
                data-test="crear-grupo-cuerpo"
            >
                Crear grupo
            </a>
        </div>
    @else
        <div x-data="{gamelist: []}" class="flex flex-row w-full">
            <!-- grupos de juegos del usuario -->
            <div class="grid w-[1500px]  m-auto mt-15 space-x-5 xl:grid-cols-3 lg:grid-cols-2 xs:grid-cols-1">
                @foreach($groups as $group)
                    <x-list.group :group="$group" :userGames="$userGames" />                
                @endforeach
            </div>

            @if( session('error') || session('success') )
                <!-- mensaje de error o success-->
                <x-alert type="{{ ((session('success'))?'success':'warning') }}" >
                    {{ session('error') ?? session('success')  }}
                </x-alert>
            @endif   
    
            <!-- Dialog de eliminación de juego -->
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
            <x-list.modals.newGame />
            <x-list.modals.editGame />
        </div>
    @endif        
        <x-list.modals.newEditGroup />

</x-layout>