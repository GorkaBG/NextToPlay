@props([
    'name',
])

<div 
    x-data="{ show: false, name: '{{ $name }}', title: '', itemId: null, groupname: '', color: '', icon: ''}"
    x-show="show"
    @open-modal.window="
        if ($event.detail.name === name){ 
            show = true;
            title = $event.detail.title;
            if($event.detail.id){
                itemId = $event.detail.id;
                groupname = $event.detail.groupname;
                color = $event.detail.color;
                icon = $event.detail.icon;
            }
        }
    "
    class="flex fixed inset-0 z-50  items-center justify-center bg-base-100/70 backdrop-blur-[2px]" 
>
    <div class="p-10 min-w-2xl bg-secondary border border-secondary rounded-lg">

        <x-icon name="mdi-close" class="size-7 absolute right-3 top-3 cursor-pointer text-primary-content" @click="show = false;$dispatch('close-modal')" />

        <h3 class="font-bold text-lg uppercase mb-5 text-primary-content" x-text="title"></h3> 

        {{ $slot }}
    </div>
</div>    