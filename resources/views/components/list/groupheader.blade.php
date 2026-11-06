@props([
    'group_id',
    'groupname',
    'color' => '#152f59',
    'icon' => null,
])
<div id="group-header-{{ $group_id }}" class="flex text-center p-3 text-primary-content font-bold rounded-t-xl -mt-2 -mx-2 mb-4" style="background-color: {{ $color }};">
    <div class="flex w-[85%] items-center gap-3">
        @if( ! empty($icon) ) 
        <x-icon name="{{ $icon }}" class="size-8 text-[{{ $color }}] {{ ((auth()->user()->theme ==='dark')?'brightness-150':'brightness-200') }}" />
        @endif
        <h2 class="text-lg {{ ((auth()->user()->theme ==='light')?'invert-[.94]':'') }}">
            {{ $slot }}
        </h2>
    </div>
    <div class="w-[15%] flex items-center justify-between dropdown dropdown-end">
        <a 
        x-data
        @click="$dispatch('open-modal', {name: 'modal-game-new', title: 'Añade un nuevo juego al grupo', id: {{ $group_id }} })"
        class="cursor-pointer"
        data-test="crear-juego-header-{{ $group_id }}"
        >
            <x-icon name="mdi-plus-circle-outline" class="size-6 opacity-60" />
        </a>
        <div tabindex="0" role="button" class="avatar cursor-pointer">                
            <x-icon name="mdi-dots-vertical" class="size-5 {{ ((auth()->user()->theme ==='light')?'invert-[.94]':'') }} text-secondary-content" data-test="menu-grupo-{{ $group_id }}"/>             
        </div>
        <ul
            tabindex="-1"
            class="menu menu-xs dropdown-content bg-primary rounded-box z-1 w-42 shadow space-y-2 text-primary-content justify-center"
        >                                
            <li class="flex inline-flex hover:text-secondary-content items-left">
                <a 
                    x-data
                    @click="$dispatch('open-modal', {name: 'modal-group', title: 'Edita el grupo con tus preferencias', id: {{ $group_id }}, groupname: '{{ $groupname }}', color: '{{ $color }}', icon: '{{ $icon }}' })"
                    class="text-sm"
                    data-test="editar-grupo-{{ $group_id }}"
                >
                    <x-icon name="mdi-pencil-outline" class="size-4"/>
                    Editar grupo
                </a>
            </li>
            <li class="flex hover:text-secondary-content">  
                <a 
                    x-data 
                    @click="$dispatch('open-dialog', {name: 'confirm-dialog', id: {{ $group_id }} })" class="text-sm"
                    data-test="eliminar-grupo-{{ $group_id }}"
                >
                    <x-icon name="mdi-trash-can-outline" class="size-4"/> Eliminar grupo
                </a>
            </li>
        </ul>
    </div>
</div>