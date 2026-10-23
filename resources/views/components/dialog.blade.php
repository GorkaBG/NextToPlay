@props([
    'name'
])
<dialog 
    x-data="{ name: '{{ $name }}', itemId: null}"
    @open-dialog.window="
        if ($event.detail.name === name){ itemId = $event.detail.id; $el.showModal()}
    "
    x-transition:enter="ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4"
    x-transition:enter-end="ease-in opacity-100"     
    x-transition:leave="ease-in duration-200"            
    x-transition:leave-end="opacity-0 -translate-y-4 -translate-x-4"

    class="modal bg-alert/70 backdrop-blur-[2px]"
>
    <div class="modal-box bg-primary text-primary-content text-center">
        <h3 class="text-lg font-bold">¡Atención!</h3>
        {{ $slot }}
    </div>
</dialog>