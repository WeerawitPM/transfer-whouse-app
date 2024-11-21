<x-filament-panels::page>
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
                <input
                    class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    type="text" value="{{ $book['from_whs']['FCCODE'] }}" readonly>
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2">
                    To whouse
                </label>
                <input
                    class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
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
                                    {{-- {{ $section['FCCODE'] }} - {{ $section['FCNAME'] }} --}}
                                </option>
                            @endif
                            <option value="{{ $section['FCSKID'] }}">
                                {{ $section['FCNAME'] }}
                                {{-- {{ $section['FCCODE'] }} - {{ $section['FCNAME'] }} --}}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>
    {{ $this->form }}
    <div x-data="{ activeTab: 'tab1' }">
        <x-filament::tabs label="Content tabs" class="justify-start w-[180px]">
            <x-filament::tabs.item alpine-active="activeTab === 'tab1'" x-on:click="activeTab = 'tab1'">
                Tags
            </x-filament::tabs.item>
            <x-filament::tabs.item alpine-active="activeTab === 'tab2'" x-on:click="activeTab = 'tab2'">
                Tags Detail
            </x-filament::tabs.item>
        </x-filament::tabs>
        <div>
            <div x-show="activeTab === 'tab1'">
                <div class="mt-4 overflow-x-auto border dark:border-none rounded-lg">
                    <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Part No</th>
                                <th class="px-4 py-3">Part Code</th>
                                <th class="px-4 py-3">Part Name</th>
                                <th class="px-4 py-3">model</th>
                                <th class="px-4 py-3">Qty</th>
                                <th class="px-4 py-3">Packing Name</th>
                                <th class="px-4 py-3">From whs</th>
                                <th class="px-4 py-3">To whs</th>
                                <th class="px-4 py-3">Tag Qty</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 dark:text-gray-200">
                            @forelse ($tags_detail as $index => $part)
                                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition ease-in-out duration-150"
                                    id="tr_{{ $index }}" name="tr_{{ $index }}">
                                    <td class="px-4 py-3" id="part_no_{{ $index }}"
                                        name="part_no_{{ $index }}">
                                        {{ $part['part_no'] }}
                                    </td>
                                    <td class="px-4 py-3" id="part_code_{{ $index }}"
                                        name="part_code_{{ $index }}">
                                        {{ $part['part_code'] }}
                                    </td>
                                    <td class="px-4 py-3" id="part_name_{{ $index }}"
                                        name="part_name_{{ $index }}">
                                        {{ $part['part_name'] }}
                                    </td>
                                    <td class="px-4 py-3" id="model_{{ $index }}"
                                        name="model_{{ $index }}">
                                        {{ $part['model'] }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-blue-700" id="qty_{{ $index }}"
                                        name="qty_{{ $index }}">
                                        {{ $part['qty'] }}
                                    </td>
                                    <td class="px-4 py-3" id="packing_name_{{ $index }}"
                                        name="packing_name_{{ $index }}">
                                        {{ $part['packing_name'] }}
                                    </td>
                                    <td class="px-4 py-3" id="from_whs_{{ $index }}"
                                        name="from_whs_{{ $index }}">
                                        {{ $part['from_whs'] }}
                                    </td>
                                    <td class="px-4 py-3" id="to_whs_{{ $index }}"
                                        name="to_whs_{{ $index }}">
                                        {{ $part['to_whs'] }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-blue-700" id="tag_qty_{{ $index }}"
                                        name="tag_qty_{{ $index }}">
                                        {{ $part['tag_qty'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-filament::button color="danger" size="sm"
                                            wire:click="handleDeleteTagDetail({{ $index }})">Delete</x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 250px">
                                    <td colspan="10" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'tab2'">
                <div class="mt-4 overflow-x-auto border dark:border-none rounded-lg">
                    <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Qr Code</th>
                                <th class="px-4 py-3">Part No</th>
                                <th class="px-4 py-3">Part Code</th>
                                <th class="px-4 py-3">Part Name</th>
                                <th class="px-4 py-3">model</th>
                                <th class="px-4 py-3">Qty</th>
                                <th class="px-4 py-3">Packing Name</th>
                                <th class="px-4 py-3">From whs</th>
                                <th class="px-4 py-3">To whs</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 dark:text-gray-200">
                            @forelse ($tags as $index => $part)
                                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition ease-in-out duration-150"
                                    id="tr_{{ $index }}" name="tr_{{ $index }}">
                                    <td class="px-4 py-3" id="id_{{ $index }}"
                                        name="id_{{ $index }}">
                                        {{ $part['id'] }}
                                    </td>
                                    <td class="px-4 py-3" id="qr_code_{{ $index }}"
                                        name="qr_code_{{ $index }}">
                                        {{ $part['qr_code'] }}
                                    </td>
                                    <td class="px-4 py-3" id="part_no_{{ $index }}"
                                        name="part_no_{{ $index }}">
                                        {{ $part['part_no'] }}
                                    </td>
                                    <td class="px-4 py-3" id="part_code_{{ $index }}"
                                        name="part_code_{{ $index }}">
                                        {{ $part['part_code'] }}
                                    </td>
                                    <td class="px-4 py-3" id="part_name_{{ $index }}"
                                        name="part_name_{{ $index }}">
                                        {{ $part['part_name'] }}
                                    </td>
                                    <td class="px-4 py-3" id="model_{{ $index }}"
                                        name="model_{{ $index }}">
                                        {{ $part['model'] }}
                                    </td>
                                    <td class="px-4 py-3" id="qty_{{ $index }}"
                                        name="qty_{{ $index }}">
                                        {{ $part['qty'] }}
                                    </td>
                                    <td class="px-4 py-3" id="packing_name_{{ $index }}"
                                        name="packing_name_{{ $index }}">
                                        {{ $part['packing_name'] }}
                                    </td>
                                    <td class="px-4 py-3" id="from_whs_{{ $index }}"
                                        name="from_whs_{{ $index }}">
                                        {{ $part['from_whs'] }}
                                    </td>
                                    <td class="px-4 py-3" id="to_whs_{{ $index }}"
                                        name="to_whs_{{ $index }}">
                                        {{ $part['to_whs'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-filament::button color="danger" size="sm"
                                            wire:click="handleDeleteTag({{ $index }})">Delete</x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 250px">
                                    <td colspan="11" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

<script>
    const section = document.getElementById('section');
    section.addEventListener('change', function() {
        // console.log(section.value);
        @this.handleUpdateSection(section.value);
    });

    document.addEventListener("DOMContentLoaded", function() {
        const inputQrCode = document.getElementById('input_qr_code');

        if (inputQrCode) {
            inputQrCode.focus();
            inputQrCode.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    // console.log(section.value);
                    event.preventDefault();
                    @this.handleQrCodeInput(inputQrCode.value);
                    inputQrCode.value = '';
                    inputQrCode.focus();
                }
            });

            inputQrCode.addEventListener('keydown', function(event) {
                if (event.keyCode == 9) { //tab pressed
                    event.preventDefault();
                    @this.handleQrCodeInput(inputQrCode.value);
                    inputQrCode.value = '';
                    inputQrCode.focus();
                }
            });
        }
    });
</script>
