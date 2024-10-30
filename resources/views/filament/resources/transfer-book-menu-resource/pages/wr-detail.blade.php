<x-filament-panels::page>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{ $this->form }}

                <!-- Render the action button explicitly -->
                <div class="mt-3 flex">
                    <x-filament::button type="submit" class="mx-auto">
                        Search
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::card>
    {{ $this->table }}
</x-filament-panels::page>
