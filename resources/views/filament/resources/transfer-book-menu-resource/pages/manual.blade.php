<x-filament-panels::page>
    <!-- Table in Modal -->
    <x-filament::modal width="5xl" :close-by-clicking-away="false">
        <x-slot name="trigger" style="width: 105px">
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

    <!-- Table -->
    <div class="mt-4 overflow-x-auto border rounded-lg">
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
                                class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-0"
                                id="packing_qty_{{ $index }}" name="packing_qty_{{ $index }}"
                                style="width: 100px" value="{{ $packing[$part['FCSKID']]->packing_qty ?? 0 }}">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0" required wire:ignore
                                class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-0"
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

<script>
    const inputSearchPart = document.getElementById('input_search_part');
    const partData = [];

    inputSearchPart.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            @this.handleSearchPart(inputSearchPart.value);
            // inputSearchPart.value = '';
        }
    });

    function deleteRow(button, index) {
        // ยืนยันการลบ
        if (confirm('คุณต้องการจะลบรายการนี้ใช่หรือไม่?')) {
            // หาแถว (tr) ที่ปุ่มอยู่
            const row = button.closest('tr');
            // ลบแถว
            row.remove();
            @this.call('removePart', index);
        }
    }

    function handleSave() {
        const table = document.getElementById('partsTable');
        const tableRows = table.querySelectorAll('tbody tr');
        // const partData = [];
        let hasDataEmptyError = false;
        let hasPackingQtyError = false;
        let hasQtyError = false;
        let hasStockQtyError = false;

        tableRows.forEach((row, index) => {
            const FCSKIDElement = row.querySelector(`#FCSKID_${index}`);
            if (!FCSKIDElement) {
                // console.warn(`Skipping row ${index} due to missing elements`);
                hasDataEmptyError = true;
                return;
            }
            const FCSKID = row.querySelector(`#FCSKID_${index}`).textContent.trim();
            const FCCODE = row.querySelector(`#FCCODE_${index}`).textContent.trim();
            const FCSNAME = row.querySelector(`#FCSNAME_${index}`).textContent.trim();
            const FCNAME = row.querySelector(`#FCNAME_${index}`).textContent.trim();
            const MODEL = row.querySelector(`#MODEL_${index}`).textContent.trim();
            const SMODEL = row.querySelector(`#SMODEL_${index}`).textContent.trim();
            const stockQty = parseInt(row.querySelector(`#STOCKQTY_${index}`).textContent.trim());
            const packingQtyInput = row.querySelector(`#packing_qty_${index}`);
            const qtyInput = row.querySelector(`#qty_${index}`);

            const packingQty = packingQtyInput.value;
            const qty = qtyInput.value;

            let isValid = true;

            // ตรวจสอบ packingQty ว่าเป็นจำนวนเต็มบวก
            if (packingQty <= 0 || !Number.isInteger(Number(packingQty))) {
                packingQtyInput.style.border = '2px solid red';
                isValid = false;
                hasPackingQtyError = true;
            } else {
                packingQtyInput.style.border = '';
            }

            // ตรวจสอบ qty ว่าเป็นจำนวนเต็มบวก
            if (qty <= 0 || !Number.isInteger(Number(qty))) {
                qtyInput.style.border = '2px solid red';
                isValid = false;
                hasQtyError = true;
            } else {
                // ตรวจสอบ qty ห้ามมากกว่า stock qty
                if (qty > stockQty) {
                    qtyInput.style.border = '2px solid red';
                    isValid = false;
                    hasStockQtyError = true;
                } else {
                    qtyInput.style.border = '';
                }
            }

            // เพิ่มข้อมูลเฉพาะกรณีที่ packingQty และ qty ถูกต้อง
            if (isValid) {
                partData.push({
                    FCSKID: FCSKID,
                    part_no: FCCODE,
                    FCSNAME: FCSNAME,
                    FCNAME: FCNAME,
                    MODEL: MODEL,
                    SMODEL: SMODEL,
                    packing_qty: parseInt(packingQty), // แปลงค่าเป็นจำนวนเต็ม
                    qty: parseInt(qty),
                });
            }
        });

        // หากไม่มีข้อผิดพลาด แสดงข้อมูลที่ผ่านการตรวจสอบ
        if (!hasDataEmptyError && !hasPackingQtyError && !hasQtyError && !hasStockQtyError) {
            // console.log(partData);
            document.getElementById('openConfirmSaveModal').click();
        } else {
            if (hasDataEmptyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "ไม่มีข้อมูลที่สามารถบันทึกได้ กรุณาตรวจสอบข้อมูลที่กรอก",
                    "danger"
                );
            }
            if (hasPackingQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Packing Qty ต้องมากกว่า 0 และต้องเป็นจำนวนเต็มบวกเท่านั้น",
                    "danger"
                );
            }
            if (hasQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Qty ต้องมากกว่า 0 และต้องเป็นจำนวนเต็มบวกเท่านั้น",
                    "danger"
                );
            }
            if (hasStockQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Qty ห้ามเกินจำนวนที่มีใน Stock",
                    "danger"
                );
            }
        }
    }

    function confirmSaveModal() {
        // console.log(partData);
        const btn_save = document.getElementById('btn_save');
        btn_save.style.display = "none";
        @this.$dispatch('close-modal', {
            id: 'confirmSaveModal'
        });
        @this.handleConfirmSave(partData);
    }
</script>
