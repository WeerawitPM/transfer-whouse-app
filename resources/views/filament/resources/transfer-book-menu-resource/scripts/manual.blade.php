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
        const section = document.getElementById('section');
        // console.log(section.value);
        // console.log(partData);
        const btn_save = document.getElementById('btn_save');
        btn_save.style.display = "none";
        @this.$dispatch('close-modal', {
            id: 'confirmSaveModal'
        });
        @this.handleConfirmSave(partData, section.value);
    }
</script>
