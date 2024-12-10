<x-filament-panels::page>
    @include('filament.resources.transfer-book-menu-resource.components.book-head')
    {{ $this->form }}
    <div class="text-red-500 text-center" id="error_text"></div>
    <div x-data="{ activeTab: 'tab1' }">
        <x-filament::tabs label="Content tabs" class="justify-start w-[165px]">
            <x-filament::tabs.item alpine-active="activeTab === 'tab1'" x-on:click="activeTab = 'tab1'">
                Tags
            </x-filament::tabs.item>
            <x-filament::tabs.item alpine-active="activeTab === 'tab2'" x-on:click="activeTab = 'tab2'">
                Tags Detail
            </x-filament::tabs.item>
        </x-filament::tabs>
        <div wire:ignore>
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
