@props([
    'name' => 'icon',
    'label' => '',
    'value' => '',
    'data_test' => '',
])

<label for="{{ $name }}" class="block text-lg font-semibold mb-2">
    {{ $label }}
</label>

<div 
    x-data="iconSelector('{{ $value }}')" 
    x-modelable="selectedIcon"
    @close-modal.window="iconPreview = null;selectedIcon=null"
    class="flex dropdown max-w-xs" 
    {{ $attributes }}
>        
    <input type="hidden" name="{{ $name }}" :value="selectedIcon" data-test="{{ $data_test }}">     
    <x-forms.field 
        name=""
        placeholder="Escribe para buscar..."
        x-model="search"
        @input.debounce.300ms="showIcons()"
        @focus="open = true"
        @click.away="open = false"
        data-test="{{ $data_test }}-search"
    />
    
    <div class="flex items-center ml-2 pr-3 pointer-events-none text-secondary-content" >
        <span class="w-7 h-7 " 
            x-html="iconPreview" 
        ></span>
    </div>
        
    <ul id="icon-list" class="dropdown-content menu p-2 shadow bg-secondary tex-secondary-content rounded-box w-full max-h-80 overflow-y-auto z-50 mt-11 border border-secondary-content" x-show="open && results.length > 0">
        <template x-for="icon in results" :key="icon">
            <li>
                <button 
                    type="button" 
                    @click="selectIcon(icon)"
                    class="flex flex-cols-3 items-center gap-3 active:bg-primary py-2 px-3  rounded-lg"
                >
                    
                    <div 
                        x-data="{ 
                            svgContent: '',
                            async init() {
                                try {
                                    const res = await fetch(`/icons/svg?name=${icon}`);
                                    if (res.ok) this.svgContent = await res.text();
                                } catch(e) {}
                            }
                        }"
                        class="w-6 h-6 text-secondary-content hover:text-primary-content"
                        x-html="svgContent || '<span class=\'loading loading-spinner loading-xs\'></span>'"
                    >
                    </div>
                    
                </button>
            </li>
        </template>
    </ul>
    

</div>

{{-- Script de soporte Alpine.js --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('iconSelector', (initialValue) => ({
        search: initialValue,
        selectedIcon: initialValue,
        open: false,
        results: [],
        iconPreview: '',

        init() {
            this.$watch('selectedIcon', (newValue) => {
                if (newValue) {
                    this.updatePreview(newValue);
                } else {
                    this.iconPreview = '';
                }
            });
        },

        async showIcons() {
            if (this.search.length < 4) {
                this.results = [];
                return;
            }
            const response = await fetch(`{{ route('icons.search') }}?q=${encodeURIComponent(this.search)}`);
            this.results = await response.json();
        },

        selectIcon(icon) {
            this.selectedIcon = icon;
            this.open = false;
            this.updatePreview(icon);
        },

        async updatePreview(icon) {
            try {
                const res = await fetch(`/icons/svg?name=${icon}`);
                if (res.ok) {
                    this.iconPreview = await res.text();
                }
            } catch (e) {
                this.iconPreview = '';
            }
        }
    }));
});
</script>