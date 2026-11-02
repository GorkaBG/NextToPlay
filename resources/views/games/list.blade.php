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
        <div x-data="{gamelist: [], dragged: null, placeholder: null}" class="flex flex-row w-full">
            <!-- grupos de juegos del usuario -->
            <div class="grid w-[1500px]  m-auto mt-15 px-4 gap-5 xl:grid-cols-3 lg:grid-cols-2 grid-cols-1  ">
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

<script>
/*const items = document.querySelectorAll('.gamecard');
const lists = document.querySelectorAll('.group');

// 1. Configurar los elementos movibles (LI)
items.forEach(item => {
    item.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', item.id);
        // Retraso mínimo para que el efecto visual no desaparezca al instante
        setTimeout(() => item.style.display = 'none', 1000);
    });

    item.addEventListener('dragend', () => {
        item.style.display = 'block';
    });
});

// 2. Configurar las zonas de destino (UL)
lists.forEach(list => {
    list.addEventListener('dragover', (e) => {
        e.preventDefault(); // Permite soltar el elemento
        list.classList.add('drag-over');
    });

    list.addEventListener('dragleave', () => {
        list.classList.remove('drag-over');
    });

    list.addEventListener('drop', (e) => {
        e.preventDefault();
        list.classList.remove('drag-over');
        
        const id = e.dataTransfer.getData('text/plain');
        const draggableElement = document.getElementById(id);
        
        // Añade el LI directamente al UL de destino
        list.appendChild(draggableElement);
    });
});*/
</script>
</x-layout>