@props([
    'name' => 'game',
    'label' => '',
    'value' => '',
])

<label for="{{ $name }}" class="block text-lg font-semibold mb-2">
    {{ $label }}
</label>

<div 
    x-data="gameSelector('{{ $value }}')" 
    x-modelable="selectedGame"
    @close-modal.window="iconPreview = null;selectedGame=null;showPlatforms(0);results=Array();allPlatforms=Array();selectedGameName=''"
    class="flex flex-col dropdown max-w-full" 
    {{ $attributes }}
>  

    <input type="hidden" type="hidden" name="{{ $name }}" :value="selectedGame" required>
    <x-forms.field 
        name="gameSelector"
        placeholder="Escribe para buscar..."
        x-model="gameSearch"
        @input.debounce.300ms="showGames()"
        @focus="open = true"
        @click.away="open = false"
        required
    />  


    <ul class="dropdown-content flex flex-col py-2 px-3 shadow bg-secondary tex-secondary-content rounded-box w-full max-h-100 space-y-2 overflow-y-auto z-50 mt-11 border border-secondary-content scrollbar-thumb-secondary-content" x-show="open && results.length > 0">
        <template x-for="game in results" :key="game.id">
            <li class="flex p-3 bg-secondary-content/30 rounded-box w-full cursor-pointer tooltip" :data-tip="game.name" @click="selectGame(game.id, game.name)" x-show="! gamelist.includes(game.id)">
                <div class="max-w-[15%]">
                    <img class="size-15 rounded-box" :src="game.cover.url" x-show="game.cover.url.length>0" />
                </div>
                <div  class="flex flex-col max-w-[85%] justify-between text-left py-1 px-3">
                    <div  class="title text-primary-content font-bold text-base">
                        <span x-text="game.name"></span> (<span x-text="new Date(game.first_release_date * 1000).getFullYear()"></span>)
                    </div>
                    <div class="text-xs uppercase font-semibold opacity-60 text-secondary-content">
                        <span x-text="game.developers"></span>
                    </div>
                </div>
            </li>
        </template>
    </ul>

    <h4 
        x-text="selectedGameName"
        class="mt-4 font-bold text-xl text-primary-content"
    ></h4>
    <p 
        x-show="selectedGamePlatforms.length > 0"
    >
        Selecciona la(s) plataforma(s) o tienda(s) donde lo tienes para jugar
    </p>
    <div class="grid grid-cols-2 mt-4 gap-2">
        <template x-for="pl in selectedGamePlatforms" :key="pl.id">
            <div>
                <input type="checkbox" name="platforms[]" :id="pl.name" :value="pl.id"/>
                <label :for="pl.name"><span x-text="pl.name"></label>
            </div>
        </template> 
    </div>
    
</div>

<script>
    var allPlatforms = [];
    document.addEventListener('alpine:init', () => {
        Alpine.data('gameSelector', (initialValue) => ({
            gameSearch: initialValue,
            selectedGame: initialValue,
            selectedGameName: '',
            selectedGamePlatforms: [],
            open: false,
            results: [],
            

            init() {
                this.$watch('selectedGame', (newValue) => {
                    
                });
            },

            async showGames() {
                if (this.gameSearch.length < 4) {
                    this.results = [];
                    return;
                }
                const response = await fetch(`{{ route('games.search') }}?q=${encodeURIComponent(this.gameSearch)}`);

                var developers = '';
                var data = await response.json();
                for(var k in data) {
                    developers = ''
                    for(var z in data[k]['involved_companies']){
                        if(data[k]['involved_companies'][z]['developer'] == true)
                            developers += (( developers)?" | ":'')+data[k]['involved_companies'][z]['company']['name']
                    }
                    data[k]['developers']=developers;
                    allPlatforms.push( Array( data[k]['id'], data[k]['platforms'] ) )
                }
                this.results = data;
                
            },

            selectGame(game, name) {
                this.selectedGame = game;
                this.selectedGameName = name;
                this.open = false;
                this.showPlatforms(game);
            },

            async showPlatforms(game) {
                //console.table(allPlatforms);
                this.selectedGamePlatforms = [];
                var platformTemp = [];
                for (var i in allPlatforms){
                    if(allPlatforms[i][0] == game){
                        for ( var x in allPlatforms[i][1]){

                            if(allPlatforms[i][1][x]['id'] == 6){//caso especial del PC
                                platformTemp.push({id: "6|2", name: "Steam"});
                                platformTemp.push({id: "6|3", name: "Epic"});
                                platformTemp.push({id: "6|4", name: "GOG" });
                                platformTemp.push({id: "6|5", name: "Xbox Game Pass"});
                                platformTemp.push({id: "6|1", name: "CD/DVD"});
                                platformTemp.push({id: "6|6", name: "Battle.net"});
                                platformTemp.push({id: "6|7", name: "GeForce Now"});
                                platformTemp.push({id: "6|8", name: "EA App"});
                                platformTemp.push({id: "6|9", name: "Ubisoft Connect"});
                                platformTemp.push({id: "6|10", name: "Rockstar Launcher"});
                                platformTemp.push({id: "6|11", name: "Itch.io"});
                                platformTemp.push({id: "6|12", name: "Microsoft Store"});
                                platformTemp.push({id: "6|13", name: "Amazon Luna"});
                            }else{                                
                                platformTemp.push({id: allPlatforms[i][1][x]['id'], name: allPlatforms[i][1][x]['name']});
                            }
                            
                        }
                        break;
                    }
                }
               //this.selectedGamePlatforms.push(allPlatforms[i][1][x]);
                this.selectedGamePlatforms = platformTemp;
                
            }
        }));
    });
</script>