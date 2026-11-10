<div class="navbar flex flex-col md:flex-row content-center justify-between pl-6 pr-6 sm:pl-10 sm:pr-10 bg-primary shadow-sm">

    <div class="flex content-center py-2 md:py-0" >
        <a href="{{ route('games.list') }}" class="inline-flex items-center gap-2 text-xl text-primary-content font-semibold">
            <img src="/images/NextToPlay.webp" alt="NextToPlay" class="h-15 md:h-10"> NextToPlay
        </a>
    </div>

    <div class="flex flex-col sm:flex-row w-full md:w-auto justify-between gap-5 md:gap-10 mt-5 md:mt-0">
        <div class="flex w-full justify-center sm:w-auto">
            <a 
                x-data
                @click="$dispatch('open-modal', {name: 'modal-group', title: 'Crea un nuevo grupo para tus juegos'})"
                class="btn"
                data-test="crear-grupo"
            >
                Crear grupo
            </a>
        </div>

        <div class="flex w-full sm:w-auto justify-between gap-5 md:gap-10 ">
            <div class="text-primary-content content-center text-sm {{ (( ! session('show_finished'))?'opacity-50':'') }}">
                <form 
                    action="{{ route('games.show.finished') }}" 
                    method="POST"
                    x-data
                    >
                    @csrf
                    <input 
                        type="checkbox" 
                        name="finished"
                        class="toggle text-primary-content mr-1 " 
                        autocomplete="off" 
                        {{ ((session('show_finished'))?'checked':'') }} 
                        data-test="btn-toggle-finished"
                        @change="$el.form.submit()"
                        />
                    Mostrar completados
                </form>
            </div>
            <div class="text-primary-content content-center">
                <form 
                    action="{{ route('toggle.theme') }}" 
                    method="POST" 
                    class="inline-flex space-x-2 items-center mt-1"
                    x-data
                    >
                    @csrf
                    <x-icon name="heroicon-c-sun" class="size-5" />
                    <input 
                        type="checkbox" 
                        name="theme" 
                        {{ ((Illuminate\Support\Facades\Auth::user()->theme ==='dark')?'checked':'') }}
                        class="toggle text-primary-content" autocomplete="off"
                        @change="$el.form.submit()"
                        data-test="btn-toggle-theme"
                        />
                    <x-icon name="heroicon-s-moon" class="size-5" />   
                </form>  
                
            </div>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="avatar cursor-pointer">
                    <div class="w-10 rounded-full text-primary-content content-center">
                        <x-icon name="mdi-account-circle" class="size-8" />
                    </div>
                </div>
                <ul
                tabindex="-1"
                class="menu menu-xs dropdown-content bg-secondary rounded-box z-1 mt-3 w-52 p-2 shadow ">
                    <!--li>
                        <a href="/profile" class="text-sm text-primary-content">
                            <x-icon name="mdi-account" class="size-4"/> Tu usuario
                        </a>
                    </li-->
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex text-sm cursor-pointer text-primary-content items-center gap-2">
                                <x-icon name="mdi-power" class="size-4"/> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>