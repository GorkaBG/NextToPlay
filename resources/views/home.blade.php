<x-layout>
    <x-forms.loginForm action="{{ route('index.store') }}">  
        <div class="mb-4 mt-4">
            <x-forms.field type="email" name="email" label="Tu email" />
        </div>

        @if ($errors->has('email'))
            <p class="text-red-500 text-sm italic mb-4">Introduce un email válido.</p>
        @endif
        
        <div class="flex items-center justify-center">
            <x-forms.button type="submit" data-test="btn-entrar">¡Entrar!</x-forms.button>
        </div>
    </x-forms.loginForm>

</x-layout>