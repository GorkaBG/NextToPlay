<div class="flex justify-between items-center pt-4">
    <a class="text-primary-content font-bold cursor-pointer" @click="show = false;$dispatch('close-modal')">Cerrar</a>

    <x-forms.button type="submit" data-test="btn-guardar-grupo">Guardar</x-forms.button>
</div>