<x-filament-panels::page>
    <x-filament::modal width="5xl" :close-by-clicking-away="false">
        <x-slot name="trigger">
            <x-filament::button>
                Search Part
            </x-filament::button>
        </x-slot>
        <x-slot name="heading">
            Search Part
        </x-slot>

        <div id="modal">
            <div>
                {{ $this->form }}
            </div>
            <div class="mt-2">
                {{ $this->table }}
            </div>
        </div>
    </x-filament::modal>
</x-filament-panels::page>

<script>
    const inputSearchPart = document.getElementById('input_search_part');

    inputSearchPart.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            @this.handleSearchPart(inputSearchPart.value);
            // inputSearchPart.value = '';
        }
    });
</script>
