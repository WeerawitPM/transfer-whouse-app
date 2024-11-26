<script>
    const inputSearchPart = document.getElementById('input_search_part');
    const product_type = document.getElementById('product_type');
    const partData = [];

    inputSearchPart.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            @this.handleSearchPart(inputSearchPart.value, product_type.value);
            // inputSearchPart.value = '';
        }
    });

    function addRowToPartsTable(index) {
        // ค้นหาแถวที่มี data-index ตรงกับ index
        const whouse = document.getElementById('from_whs').value;
        // console.log(whouse);
        const row = document.querySelector(`tr[data-index="${index}"]`);

        if (!row) {
            console.error(`Row with index ${index} not found.`);
            return;
        }

        const FCSKID = row.querySelector('input[data-key="FCSKID"]').value;
        const FCCODE = row.querySelector('td[data-key="FCCODE"]').textContent.trim();
        const FCSNAME = row.querySelector('td[data-key="FCSNAME"]').textContent.trim();
        const FCNAME = row.querySelector('td[data-key="FCNAME"]').textContent.trim();
        const MODEL = row.querySelector('input[data-key="MODEL"]').value;
        const SMODEL = row.querySelector('input[data-key="SMODEL"]').value;
        const STOCKQTY = row.querySelector('td[data-key="STOCKQTY"]').textContent.trim();
        // อ่านค่าของ packing_qty และ qty
        const packingQty = row.querySelector('input[data-key="packing_qty"]').value;
        const qty = row.querySelector('input[data-key="qty"]').value;

        // ตรวจสอบว่า STOCKQTY เป็น 0 หรือไม่
        if (whouse == 'YYY' || whouse == 'XXX') {
            true;
        } else {
            if (parseInt(STOCKQTY) <= 0 || !Number.isInteger(Number(STOCKQTY))) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก Stock มีค่าน้อยกว่าหรือเท่ากับ 0",
                    "warning"
                );
                return;
            }
        }

        // ตรวจสอบ row ที่ซ้ำกันใน partsTable
        const existingRow = document.querySelector(`#partsTable tbody tr[data-fcskid="${FCSKID}"]`);
        if (existingRow) {
            @this.handleNotification(
                "เกิดข้อผิดพลาด",
                "ข้อมูลนี้ถูกเพิ่มในตารางแล้ว",
                "danger"
            );
            return;
        }

        // เพิ่มแถวใหม่ใน partsTable
        const partsTableBody = document.querySelector("#partsTable tbody");
        const newRow = document.createElement("tr");
        newRow.setAttribute("data-fcskid", FCSKID);

        const stockQtyCell = (whouse === 'YYY' || whouse === 'XXX') ? '' : `
            <td class="px-4 py-3 text-center" data-key="STOCKQTY">${STOCKQTY}</td>
        `;
        newRow.innerHTML = `
        <td class="px-4 py-3 text-center" data-key="FCSKID">${FCSKID}</td>
        <td class="px-4 py-3 text-center" data-key="FCCODE">${FCCODE}</td>
        <td class="px-4 py-3 text-center" data-key="FCSNAME">${FCSNAME}</td>
        <td class="px-4 py-3 text-center" data-key="FCNAME">${FCNAME}</td>
        <td class="px-4 py-3 text-center" data-key="MODEL">${MODEL}</td>
        <td class="px-4 py-3 text-center" data-key="SMODEL">${SMODEL}</td>
        ${stockQtyCell}
        <td class="px-4 py-3 text-center">
            <input type="number" min="0" required class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0" style="width: 100px" value="${packingQty}" data-key="packing_qty">
        </td>
        <td class="px-4 py-3 text-center">
            <input type="number" min="0" required class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-gray-200 dark:border-0" style="width: 100px" value="${qty}" data-key="qty">
        </td>
        <td class="px-4 py-3 text-center">
            <button class="bg-red-500 text-white rounded px-2 py-1" onclick="deleteRow(this, '${FCSKID}')">Remove</button>
        </td>
    `;

        partsTableBody.appendChild(newRow);
        @this.handleNotification(
            "สำเร็จ",
            "ข้อมูลนี้ถูกเพิ่มในตารางแล้ว",
            "success"
        );
        return;
    }

    function deleteRow(button, index) {
        // ยืนยันการลบ
        if (confirm('คุณต้องการจะลบรายการนี้ใช่หรือไม่?')) {
            // หาแถว (tr) ที่ปุ่มอยู่
            const row = button.closest('tr');
            // ลบแถว
            row.remove();
        }
    }

    function handleSave() {
        const whouse = document.getElementById('from_whs').value; // อ่านค่าของ whouse
        const table = document.getElementById('partsTable');
        const tableRows = table.querySelectorAll('tbody tr'); // ค้นหาแถวทั้งหมดใน partsTable
        // console.log(tableRows);
        let hasPackingQtyError = false;
        let hasQtyError = false;
        let hasStockQtyError = false;

        // ตรวจสอบว่ามีแถวใน partsTable หรือไม่
        if (tableRows.length === 0) {
            @this.handleNotification(
                "เกิดข้อผิดพลาด",
                "ไม่มีข้อมูลในตาราง กรุณาเพิ่มข้อมูลก่อนบันทึก",
                "warning"
            );
            return; // ยุติการทำงานของฟังก์ชัน
        }

        tableRows.forEach((row) => {
            const FCSKID = row.querySelector('td[data-key="FCSKID"]').textContent.trim();
            const FCCODE = row.querySelector('td[data-key="FCCODE"]').textContent.trim();
            const FCSNAME = row.querySelector('td[data-key="FCSNAME"]').textContent.trim();
            const FCNAME = row.querySelector('td[data-key="FCNAME"]').textContent.trim();
            const MODEL = row.querySelector('td[data-key="MODEL"]').textContent.trim();
            const SMODEL = row.querySelector('td[data-key="SMODEL"]').textContent.trim();
            const packingQtyInput = row.querySelector('input[data-key="packing_qty"]');
            const qtyInput = row.querySelector('input[data-key="qty"]');

            const packingQty = packingQtyInput.value;
            const qty = qtyInput.value;

            let isValid = true;

            // ดึงค่า stockQty เฉพาะกรณีที่ whouse ไม่ใช่ YYY หรือ XXX
            let stockQty = null;
            if (whouse !== 'YYY' && whouse !== 'XXX') {
                stockQty = parseInt(row.querySelector('td[data-key="STOCKQTY"]').textContent.trim());
            }

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
                if (stockQty !== null && qty > stockQty) {
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
        if (!hasPackingQtyError && !hasQtyError && !hasStockQtyError) {
            console.log(partData);
            document.getElementById('openConfirmSaveModal').click();
        } else {
            if (hasPackingQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Packing Qty ต้องมากกว่า 0 และต้องเป็นจำนวนเต็มบวกเท่านั้น",
                    "danger"
                );
                return;
            }
            if (hasQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Qty ต้องมากกว่า 0 และต้องเป็นจำนวนเต็มบวกเท่านั้น",
                    "danger"
                );
                return;
            }
            if (hasStockQtyError) {
                @this.handleNotification(
                    "เกิดข้อผิดพลาด",
                    "Qty ห้ามเกินจำนวนที่มีใน Stock",
                    "danger"
                );
                return;
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
