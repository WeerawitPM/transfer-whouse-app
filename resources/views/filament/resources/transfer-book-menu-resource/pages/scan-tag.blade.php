<x-filament-panels::page>
    @include('filament.resources.transfer-book-menu-resource.components.book-head')
    {{ $this->form }}
    <div class="text-red-500 text-center" id="error_text" wire:ignore></div>
    <div x-data="{ activeTab: 'tab1' }">
        <x-filament::tabs label="Content tabs" class="justify-start w-[165px]">
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
                        <thead wire:ignore
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="text-start px-4 py-3">Part No</th>
                                <th class="text-start px-4 py-3 hidden">Part Code</th>
                                <th class="text-start px-4 py-3 hidden">Part Name</th>
                                <th class="text-start px-4 py-3">model</th>
                                <th class="text-start px-4 py-3">Qty</th>
                                <th class="text-start px-4 py-3">Packing Name</th>
                                <th class="text-start px-4 py-3 hidden">From whs</th>
                                <th class="text-start px-4 py-3 hidden">To whs</th>
                                <th class="text-start px-4 py-3">Tag Qty</th>
                                <th class="text-start px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 dark:text-gray-200">
                            @foreach ($tags_detail as $index => $part)
                                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition ease-in-out duration-150"
                                    id="tr_{{ $index }}" name="tr_{{ $index }}">
                                    <td class="px-4 py-3" id="part_no_{{ $index }}"
                                        name="part_no_{{ $index }}">
                                        {{ $part['part_no'] }}
                                    </td>
                                    <td class="px-4 py-3 hidden" id="part_code_{{ $index }}"
                                        name="part_code_{{ $index }}">
                                        {{ $part['part_code'] }}
                                    </td>
                                    <td class="px-4 py-3 hidden" id="part_name_{{ $index }}"
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
                                    <td class="px-4 py-3 hidden" id="from_whs_{{ $index }}"
                                        name="from_whs_{{ $index }}">
                                        {{ $part['from_whs'] }}
                                    </td>
                                    <td class="px-4 py-3 hidden" id="to_whs_{{ $index }}"
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
                                {{-- @empty
                                <tr style="height: 250px">
                                    <td colspan="10" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'tab2'">
                <div class="mt-4 overflow-x-auto border dark:border-none rounded-lg">
                    <table id="partsTable2" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                        <thead wire:ignore
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
                                    <td class="px-4 py-3" id="id_{{ $index }}" name="id_{{ $index }}">
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
                                    <td class="px-4 py-3" id="qty_{{ $index }}" name="qty_{{ $index }}">
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
    <!-- Modal Confirm Save-->
    <x-filament::modal id="confirmSaveModal" width="md" :close-by-clicking-away="false">
        <x-slot name="trigger" style="display: none;">
            <button id="openConfirmSaveModal"></button>
        </x-slot>
        <x-slot name="heading">
            ยืนยันการบันทึก
        </x-slot>
        <p>คุณต้องการบันทึกข้อมูลนี้ใช่หรือไม่?</p>
        <div class="mt-4 flex justify-end gap-2">
            <x-filament::button type="button" color="danger"
                @click="$dispatch('close-modal', {id: 'confirmSaveModal'})">
                ยกเลิก
            </x-filament::button>
            <x-filament::button type="button" color="primary"
                onclick="confirmSaveModal()">ยืนยัน</x-filament::button>
        </div>
    </x-filament::modal>
    @include('filament.resources.transfer-book-menu-resource.scripts.scan-tag')
</x-filament-panels::page>
