<x-layout>
    <x-forms.loginForm action="{{ route('codeaccess.store') }}">  
        <div class="mb-4 mt-4 space-y-4 flex flex-col items-center justify-center ">
            <p>Introduce el código que se ha enviado al email que has indicado</p>
            <label class="otp otp-xl otp-success">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <input type="text" autocomplete="one-time-code" inputmode="numeric" name="code" maxlength="4" pattern="[0-9]{4}" required />
            </label>
        </div>
        <x-forms.field type="hidden" name="email" value="{{ session('emailAttempt') }}" readonly  />        

        @if( session('error') || $errors->has('email') )
            <p class="text-red-500 text-sm italic mb-4">El código no es válido o ha caducado.</p>
        @endif
        
        <div class="flex items-center justify-center">
            <x-forms.button type="submit">Aceptar</x-forms.button>
        </div>

        <div class="mt-5">
        <a href="{{ route('login') }}" class="text-xs">← Volver</a>
        </div>
    </x-forms.loginForm>

</x-layout>
