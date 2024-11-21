<x-filament::section collapsible>
    <x-slot name="heading">
        Book details
    </x-slot>
    {{-- Content --}}
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                Book Name
            </label>
            <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                type="text" value="{{ $book['FCCODE'] }}" readonly>
        </div>
        <div class="w-full md:w-1/2 px-3">
            <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                Book Prefix
            </label>
            <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                type="text" value="{{ $book['FCPREFIX'] }}" readonly>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                From whouse
            </label>
            <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                type="text" value="{{ $book['from_whs']['FCCODE'] }}" readonly>
        </div>
        <div class="w-full md:w-1/2 px-3">
            <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                To whouse
            </label>
            <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                type="text" value="{{ $book['to_whs']['FCCODE'] }}" readonly>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                Section
            </label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="status" name="secion" id="section">
                    @foreach ($sections as $section)
                        @if ($section['FCSKID'] == $user->sect->FCSKID)
                            <option value="{{ $section['FCSKID'] }}" selected>
                                {{ $section['FCNAME'] }}
                            </option>
                        @endif
                        <option value="{{ $section['FCSKID'] }}">
                            {{ $section['FCNAME'] }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>
</x-filament::section>
