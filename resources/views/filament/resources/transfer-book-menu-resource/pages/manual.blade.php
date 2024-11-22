<x-filament-panels::page>
    @include('filament.resources.transfer-book-menu-resource.components.book-head')
    <!-- Table in Modal -->
    <x-filament::modal width="5xl" :close-by-clicking-away="false">
        <div class="flex justify-center w-full">
            <x-slot name="trigger" class="mx-auto" style="width: 105px">
                <x-filament::button>
                    Search Part
                </x-filament::button>
            </x-slot>
        </div>
        <x-slot name="heading">
            Search Part
        </x-slot>

        <div id="modal">
            <div>
                {{ $this->form }}
            </div>
            <div class="mt-3">
                {{ $this->table }}
            </div>
        </div>
    </x-filament::modal>

    <!-- Table -->
    <div class="mt-2 overflow-x-auto border dark:border-none rounded-lg">
        <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <thead
                class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">FCSKID</th>
                    <th class="px-4 py-3">Part No</th>
                    <th class="px-4 py-3">Part Code</th>
                    <th class="px-4 py-3">Part Name</th>
                    <th class="px-4 py-3">Model</th>
                    <th class="px-4 py-3">SModel</th>
                    <th class="px-4 py-3">Stock Qty</th>
                    <th class="px-4 py-3">Packing Qty</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-gray-200">
                @forelse ($part_selected as $index => $part)
                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition ease-in-out duration-150"
                        id="tr_{{ $index }}" name="tr_{{ $index }}">
                        <td class="px-4 py-3" id="FCSKID_{{ $index }}" name="FCSKID_{{ $index }}">
                            {{ $part['FCSKID'] }}
                        </td>
                        <td class="px-4 py-3" id="FCCODE_{{ $index }}" name="FCCODE_{{ $index }}">
                            {{ $part['CPART_NO'] }}
                        </td>
                        <td class="px-4 py-3" id="FCSNAME_{{ $index }}" name="FCSNAME_{{ $index }}">
                            {{ $part['CCODE'] }}
                        </td>
                        <td class="px-4 py-3" id="FCNAME_{{ $index }}" name="FCNAME_{{ $index }}">
                            {{ $part['CPART_NAME'] }}
                        </td>
                        <td class="px-4 py-3" id="MODEL_{{ $index }}" name="MODEL_{{ $index }}">
                            {{ $part['MODEL'] }}
                        </td>
                        <td class="px-4 py-3" id="SMODEL_{{ $index }}" name="SMODEL_{{ $index }}">
                            {{ $part['SMODEL'] }}
                        </td>
                        <td class="px-4 py-3" id="STOCKQTY_{{ $index }}" name="STOCKQTY_{{ $index }}">
                            {{ number_format($part['STOCKQTY'], 0) }}
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0" required wire:ignore
                                class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0"
                                id="packing_qty_{{ $index }}" name="packing_qty_{{ $index }}"
                                style="width: 100px" value="{{ $packing[$part['FCSKID']]->packing_qty ?? 0 }}">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0" required wire:ignore
                                class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0"
                                id="qty_{{ $index }}" name="qty_{{ $index }}" style="width: 100px"
                                value="0">
                        </td>
                        <td class="px-4 py-3">
                            <x-filament::button color="danger" size="sm"
                                onclick="deleteRow(this, {{ $index }})">Delete</x-filament::button>
                        </td>
                    </tr>
                @empty
                    <tr style="height: 250px">
                        <td colspan="9" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
            <x-filament::button type="button" color="primary" onclick="confirmSaveModal()">ยืนยัน</x-filament::button>
        </div>
    </x-filament::modal>
</x-filament-panels::page>

@include('filament.resources.transfer-book-menu-resource.scripts.manual')
