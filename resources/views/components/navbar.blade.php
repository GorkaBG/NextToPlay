<div class="navbar flex flex-row content-center justify-between pl-10 pr-10 bg-primary shadow-sm">

  <div class="flex content-center" >
    <a href="{{ route('games.list') }}" class="inline-flex items-center gap-2 text-xl text-primary-content font-semibold">
        <img src="/images/NextToPlay.webp" alt="NextToPlay" class="h-10"> NextToPlay
    </a>
  </div>

  <div class="flex space-x-8">
    <a 
        x-data
        @click="$dispatch('open-modal', {name: 'modal-group', title: 'Crea un nuevo grupo para tus juegos'})"
        class="btn"
        data-test="crear-grupo"
    >
        Crear grupo
    </a>
    <div class="text-primary-content content-center text-sm">
        <form 
            action="{{ route('toggle.finished') }}" 
            method="POST"
            x-data
            >
            @csrf
            <input 
                type="checkbox" 
                name="finished"
                class="toggle text-primary-content mr-1" 
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
            class="inline-flex space-x-2"
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
        <li>
            <a href="/profile" class="text-sm text-primary-content">
                <x-icon name="mdi-account" class="size-4"/> Tu usuario
            </a>
        </li>
        <li>
            <a href="{{ route('logout') }}" class="text-sm text-primary-content">
                <x-icon name="mdi-power" class="size-4"/> Cerrar sesión
            </a>
        </li>
      </ul>
    </div>
  </div>
</div>