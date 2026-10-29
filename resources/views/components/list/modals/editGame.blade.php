 <!-- modal de editar juego -->
<x-modal name="modal-game-edit">
    <form                      
        x-data="editGame();"
        @open-modal.window="
            if ($event.detail.name === name){
                gameName = $event.detail.gamename;
                itemId = $event.detail.id;
                cover = $event.detail.cover;
                finished = $event.detail.finished;
                developers = $event.detail.developers;
                platforms = $event.detail.platforms;
                showGamePlatforms($event.detail.game_id);
            }
        "
        :action="'{{ route('games.update', ['gameUser' => '__ID__']) }}'.replace('__ID__', itemId)"
        method="POST"
        class="flex flex-col w-full space-y-2 text-secondary-content">
        @csrf
        @method('PATCH')

        <!-- gamebar -->
        <div 
        class="gamecard mb-2 rounded-box border border-secondary mb-5" 
        :style="`background-image: url('/storage/${cover}'); background-position: center; background-size: cover;`">
            <div class="flex shadow-md h-[120px] py-2 px-1 bg-gradient w-full" style="background: 
            linear-gradient(
                to right,
                color-mix(in srgb, var(--color-secondary) 97%, transparent) 10%,
                color-mix(in srgb, var(--color-primary) 97%, transparent) 40%,
                color-mix(in srgb, var(--color-primary) 92%, transparent) 60%,
                color-mix(in srgb, var(--color-primary) 92%, transparent) 80%,
                color-mix(in srgb, var(--color-primary) 97%, transparent) 95%,
                var(--color-secondary) 100%
            );
                
            ">
                <div class="flex w-[6%] space-x-0 content-center "></div>

                <div class="flex w-[19%] items-center roundex-lg overflow-hidden">            
                    <img :src="'/storage/'+cover" :alt="gameName" x-show="typeof cover !== 'undefined'&& cover.length > 0" class="w-full max-w-100 rounded-box">            
                </div>
                <div class="flex flex-col w-[70%] justify-between text-left py-3 px-3">
                    <div 
                        x-text="gameName"
                        class="title text-primary-content text-lg font-bold text-base"></div>
                    <div
                        x-text="developers"
                        class="text-xs uppercase font-semibold opacity-60 text-secondary-content"></div>
                </div>

            </div>
        </div>

        <!-- custom title -->
        <x-forms.field name="name" label="Nombre del juego" class="w-full" x-model="gameName" required/>

        <!-- finished status -->
        <div class="flex gap-3 mt-3">
            ¿Finalizado?
            <label class="swap swap-rotate bg-primary p-0">
                <input type="checkbox" name="finished" x-model="finished"/>
                <x-icon name="mdi-square" class="p-0 swap-on h-6 w-6 fill-current"/>
                <x-icon name="mdi-square-outline" class="p-0 swap-off h-6 w-6 fill-current "/>
            </label>
        </div>

        <!-- Game is available on platforms... -->
        <p class="mt-3 text-primary-content">
                Selecciona la(s) plataforma(s) o tienda(s) donde lo tienes para jugar
        </p>
        <div 
            class="grid grid-cols-2 mt-2 gap-2">
            
            <template x-for="pl in selectedGamePlatforms" :key="pl.id">
                <div>
                    <input type="checkbox" name="platforms[]" :id="pl.name" :value="pl.id" :checked="platforms.includes(pl.id)"/>
                    <label :for="pl.name"><span x-text="pl.name"></label>
                </div>
            </template> 
        </div>


        <x-list.modals.buttons />
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('editGame', () => ({
                gameName: '', 
                itemId: '', 
                cover: '', 
                developers: '', 
                finished: false,        
                platforms: [],
                selectedGamePlatforms: [],

                async showGamePlatforms(game_id) {
                    const response = await fetch(`{{ route('games.game_platform') }}?q=`+game_id);
                    var data = await response.json();
                    
                    var consolas=false;
                    for ( var p of data){
                        if(p['igdb_id'] != 6)                    
                            consolas=true;
                    }   
                    if(consolas)  data.push({id:14, name: "Emulador"});

                    this.selectedGamePlatforms = data;
                },
            }));
        });            
    </script>
</x-modal>