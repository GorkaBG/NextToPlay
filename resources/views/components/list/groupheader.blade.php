@props([
    'group_id',
    'groupname',
    'color' => '#152f59',
    'icon' => null,
])
<div class="flex text-center p-3 text-primary-content font-bold rounded-t-xl -mt-2 -mx-2 mb-4" style="background-color:{{ $color }};">
    <div class="flex w-[95%] items-center gap-3">
        @if( ! empty($icon) ) 
        <x-icon name="{{ $icon }}" class="size-8 text-[{{ $color }}] brightness-150" />
        @endif
        <h2 class="text-lg">{{ $slot }}</h2>
    </div>
    <div class="w-[5%] dropdown dropdown-end">
        <div tabindex="0" role="button" class="avatar cursor-pointer">                
            <x-icon name="mdi-dots-vertical" class="size-5 text-secondary-content" />             
        </div>
        <ul
            tabindex="-1"
            class="menu menu-xs dropdown-content bg-primary rounded-box z-1 w-42 shadow space-y-2 text-primary-content justify-center"
        >                                
            <li class="flex inline-flex hover:text-secondary-content items-center">
                <a 
                    class="text-sm"
                >
                    <x-icon name="mdi-pencil-outline" class="size-4"/>
                    Editar grupo
                </a>
            </li>
            <li class="flex hover:text-secondary-content">  
                <a 
                    x-data 
                    @click="$dispatch('open-modal', {name: 'confirm-modal', id: {{ $group_id }} })" class="text-sm"
                >
                    <x-icon name="mdi-trash-can-outline" class="size-4"/> Eliminar grupo
                </a>
            </li>
        </ul>
    </div>
</div>