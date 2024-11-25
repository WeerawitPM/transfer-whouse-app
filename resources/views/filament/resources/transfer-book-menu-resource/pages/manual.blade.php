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
            <div class="mt-2 overflow-x-auto border dark:border-none rounded-lg">
                <table id="searhProducts" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                    <thead
                        class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase">
                        <tr>
                            <th class="py-3">Part No</th>
                            <th class="py-3">Part Code</th>
                            <th class="py-3">Part Name</th>
                            <th class="py-3">Stock Qty</th>
                            <th class="py-3">Packing Qty</th>
                            <th class="py-3">Qty</th>
                            <th class="py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 dark:text-gray-200">
                        @forelse ($products as $index => $product)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition ease-in-out duration-150"
                                data-index="{{ $index }}">
                                <input type="hidden" data-key="FCSKID" value="{{ $product['FCSKID'] }}" />
                                <input type="hidden" data-key="MODEL" value="{{ $product['MODEL'] }}" />
                                <input type="hidden" data-key="SMODEL" value="{{ $product['SMODEL'] }}" />
                                <td class="py-3 text-center" data-key="FCCODE">{{ $product['CPART_NO'] }}</td>
                                <td class="py-3 text-center" data-key="FCSNAME">{{ $product['CCODE'] }}</td>
                                <td class="py-3 text-center" data-key="FCNAME">{{ $product['CPART_NAME'] }}</td>
                                <td class="py-3 text-center" data-key="STOCKQTY">
                                    {{ number_format($product['STOCKQTY'], 0) }}</td>
                                <td class="py-3 text-center">
                                    <input type="number" min="0" required wire:ignore
                                        class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0"
                                        data-key="packing_qty" style="width: 100px"
                                        value="{{ $packing[$product['FCSKID']]->packing_qty ?? 0 }}">
                                </td>
                                <td class="py-3 text-center">
                                    <input type="number" min="0" required wire:ignore
                                        class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0"
                                        data-key="qty" style="width: 100px" value="0">
                                </td>
                                <td class="py-3 text-center">
                                    <x-filament::button color="success" size="sm"
                                        onclick="addRowToPartsTable({{ $index }})">Add</x-filament::button>
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
        </div>
    </x-filament::modal>

    <!-- Table -->
    <div class="mt-2 overflow-x-auto border dark:border-none rounded-lg">
        <table id="partsTable" class="w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg" wire:ignore>
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
