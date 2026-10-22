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

    <x-dialog name="confirm-modal">
        <p class="py-4">
            El grupo se eliminará de forma definitiva junto a su contenido.
            ¿Desea continuar?
        </p>

        <div class="flex justify-between modal-action">
            <form method="dialog">
                @csrf
                <button class="btn">Cancelar</button>
            </form>

            <!--a :href="`/groups/${groupId}/delete`" class="btn border-0 bg-error text-error-content">Sí, elimínalo</a-->            
        </div>
    </x-dialog>

    @if( session('error') || session('success') )
        <x-alert type="{{ ((session('success'))?'success':'warning') }}" >
            {{ session('error') ?? session('success')  }}
        </x-alert>
    @endif   


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tarjetas = document.querySelectorAll('.gamecard');
        const columnas = document.querySelectorAll('.group');
        let tarjetaArrastrada = null;

        // Configurar tarjetas (li)
        tarjetas.forEach(tarjeta => {
            tarjeta.addEventListener('dragstart', (e) => {
                tarjetaArrastrada = tarjeta;
                setTimeout(() => tarjeta.classList.add('opacity-50'), 0);
            });

            tarjeta.addEventListener('dragend', () => {
                setTimeout(() => {
                    tarjeta.classList.remove('opacity-50');
                    tarjetaArrastrada = null;
                }, 0);
            });
        });

        // Configurar columnas (ul)
        columnas.forEach(columna => {
            columna.addEventListener('dragover', (e) => {
                e.preventDefault(); // Permitir soltar
            });

            columna.addEventListener('dragenter', (e) => {
                e.preventDefault();
                columna.classList.add('bg-gray-300', 'rounded'); // Efecto visual al pasar por encima
            });

            columna.addEventListener('dragleave', () => {
                columna.classList.remove('bg-gray-300');
            });

            columna.addEventListener('drop', () => {
                columna.classList.remove('bg-gray-300');
                if (tarjetaArrastrada) {
                    // Mueve físicamente el <li> dentro del nuevo <ul> de forma visual
                    columna.appendChild(tarjetaArrastrada);
                    
                    // Opcional: Aquí tienes el ID y el nuevo estado solo en el cliente
                    const tarjetaId = tarjetaArrastrada.getAttribute('data-id');
                    const nuevoEstado = columna.getAttribute('data-estado');
                    console.log(`Tarjeta ${tarjetaId} movida a ${nuevoEstado}`);
                }
            });
        });
    });
</script>

</x-layout>