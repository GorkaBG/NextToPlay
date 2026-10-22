<x-layout>
Creación de nuevo grupo

<form action="{{ route('groups.store') }}" method="POST" class="max-w-xl">
    <x-forms.field name="name" label="nombre" required/>
    <x-forms.field name="color" type="color" label="Selecciona un color" />
    
    
<label>
    <input type="radio" name="icon" value="mdi-gamepad">
    <x-icon name="mdi-controller" class="size-8 text-secondary-content" />
</label>

<label>
    <input type="radio" name="icon" value="heroicon-o-star">
    <x-icon name="heroicon-o-star" class="size-8" />
</label>

<label>
    <input type="radio" name="icon" value="heroicon-o-bookmark">
    <x-icon name="heroicon-o-bookmark"  class="size-8" />
</label>

<x-forms.button type="submit" data-test="btn-guardar-grupo">Guardar</x-forms.button>

</form>
</x-layout>