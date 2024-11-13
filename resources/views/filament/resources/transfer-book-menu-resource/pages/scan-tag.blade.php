<x-filament-panels::page>
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
                <div class="mt-4 overflow-x-auto">
                    <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-white uppercase text-sm">
                            <tr>
                                <th class="px-4 py-2">Part No</th>
                                <th class="px-4 py-2">Part Code</th>
                                <th class="px-4 py-2">Part Name</th>
                                <th class="px-4 py-2">model</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Packing Name</th>
                                <th class="px-4 py-2">From whs</th>
                                <th class="px-4 py-2">To whs</th>
                                <th class="px-4 py-2">Tag Qty</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-white">
                            @forelse ($tags_detail as $index => $part)
                                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    id="tr_{{ $index }}" name="tr_{{ $index }}">
                                    <td class="px-4 py-2" id="part_no_{{ $index }}"
                                        name="part_no_{{ $index }}">
                                        {{ $part['part_no'] }}
                                    </td>
                                    <td class="px-4 py-2" id="part_code_{{ $index }}"
                                        name="part_code_{{ $index }}">
                                        {{ $part['part_code'] }}
                                    </td>
                                    <td class="px-4 py-2" id="part_name_{{ $index }}"
                                        name="part_name_{{ $index }}">
                                        {{ $part['part_name'] }}
                                    </td>
                                    <td class="px-4 py-2" id="model_{{ $index }}"
                                        name="model_{{ $index }}">
                                        {{ $part['model'] }}
                                    </td>
                                    <td class="px-4 py-2" id="qty_{{ $index }}" name="qty_{{ $index }}">
                                        {{ $part['qty'] }}
                                    </td>
                                    <td class="px-4 py-2" id="packing_name_{{ $index }}"
                                        name="packing_name_{{ $index }}">
                                        {{ $part['packing_name'] }}
                                    </td>
                                    <td class="px-4 py-2" id="from_whs_{{ $index }}"
                                        name="from_whs_{{ $index }}">
                                        {{ $part['from_whs'] }}
                                    </td>
                                    <td class="px-4 py-2" id="to_whs_{{ $index }}"
                                        name="to_whs_{{ $index }}">
                                        {{ $part['to_whs'] }}
                                    </td>
                                    <td class="px-4 py-2" id="tag_qty_{{ $index }}"
                                        name="tag_qty_{{ $index }}">
                                        {{ $part['tag_qty'] }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <x-filament::button color="danger" size="sm"
                                            wire:click="handleDeleteTagDetail({{ $index }})">Delete</x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 250px">
                                    <td colspan="9" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'tab2'">
                <div class="mt-4 overflow-x-auto">
                    <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-white uppercase text-sm">
                            <tr>
                                <th class="px-4 py-2">Qr Code</th>
                                <th class="px-4 py-2">Part No</th>
                                <th class="px-4 py-2">Part Code</th>
                                <th class="px-4 py-2">Part Name</th>
                                <th class="px-4 py-2">model</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Packing Name</th>
                                <th class="px-4 py-2">From whs</th>
                                <th class="px-4 py-2">To whs</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-white">
                            @forelse ($tags as $index => $part)
                                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    id="tr_{{ $index }}" name="tr_{{ $index }}">
                                    <td class="px-4 py-2" id="qr_code_{{ $index }}"
                                        name="qr_code_{{ $index }}">
                                        {{ $part['qr_code'] }}
                                    </td>
                                    <td class="px-4 py-2" id="part_no_{{ $index }}"
                                        name="part_no_{{ $index }}">
                                        {{ $part['part_no'] }}
                                    </td>
                                    <td class="px-4 py-2" id="part_code_{{ $index }}"
                                        name="part_code_{{ $index }}">
                                        {{ $part['part_code'] }}
                                    </td>
                                    <td class="px-4 py-2" id="part_name_{{ $index }}"
                                        name="part_name_{{ $index }}">
                                        {{ $part['part_name'] }}
                                    </td>
                                    <td class="px-4 py-2" id="model_{{ $index }}"
                                        name="model_{{ $index }}">
                                        {{ $part['model'] }}
                                    </td>
                                    <td class="px-4 py-2" id="qty_{{ $index }}" name="qty_{{ $index }}">
                                        {{ $part['qty'] }}
                                    </td>
                                    <td class="px-4 py-2" id="packing_name_{{ $index }}"
                                        name="packing_name_{{ $index }}">
                                        {{ $part['packing_name'] }}
                                    </td>
                                    <td class="px-4 py-2" id="from_whs_{{ $index }}"
                                        name="from_whs_{{ $index }}">
                                        {{ $part['from_whs'] }}
                                    </td>
                                    <td class="px-4 py-2" id="to_whs_{{ $index }}"
                                        name="to_whs_{{ $index }}">
                                        {{ $part['to_whs'] }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <x-filament::button color="danger" size="sm"
                                            wire:click="handleDeleteTag({{ $index }})">Delete</x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 250px">
                                    <td colspan="9" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
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
    document.addEventListener("DOMContentLoaded", function() {
        const inputQrCode = document.getElementById('input_qr_code');

        if (inputQrCode) {
            inputQrCode.focus();
            inputQrCode.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
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
