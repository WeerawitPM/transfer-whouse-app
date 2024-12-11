<script>
    const tableBody1 = document.querySelector("#partsTable tbody");
    const tableBody2 = document.querySelector("#partsTable2 tbody");
    let tags = JSON.parse(localStorage.getItem('tags')) || [];
    let tagsDetail = JSON.parse(localStorage.getItem('tagsDetail')) || [];

    document.addEventListener("DOMContentLoaded", function() {
        const inputQrCode = document.getElementById('input_qr_code');
        const errorText = document.getElementById('error_text');

        const idHeader = document.querySelector("#partsTable2 thead th:first-child");
        if (idHeader) {
            idHeader.style.cursor = "pointer"; // Indicate it's clickable
            idHeader.addEventListener("click", function() {
                sortTableById();
            });
        }

        if (inputQrCode) {
            inputQrCode.focus();
            inputQrCode.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    // console.log(section.value);
                    event.preventDefault();
                    input_qr_code();
                }
            });

            inputQrCode.addEventListener('keydown', function(event) {
                if (event.keyCode == 9) { //tab pressed
                    event.preventDefault();
                    input_qr_code();
                }
            });

            function input_qr_code() {
                @this.handleQrCodeInput(inputQrCode.value).then((value) => {
                    if (typeof value == 'string') {
                        errorText.textContent = value;
                    } else {
                        // ตรวจสอบว่า qr_code มีอยู่ใน tagsDetail หรือไม่
                        const exists = tagsDetail.some(tag => tag.qr_code === value
                            .qr_code);
                        if (exists) {
                            errorText.textContent = "ข้อมูลนี้มีอยู่ในตารางแล้ว!";
                        } else {
                            tagsDetail.push(value);
                            updateTableBody2(value);
                            updateTags(); // อัปเดต tags หลังเพิ่มข้อมูลใน tagsDetail
                            saveToLocalStorage();
                            // console.log(tags);
                        }
                    }
                });
                inputQrCode.value = '';
                inputQrCode.focus();
            }
        }

        const focus_btn = document.getElementsByClassName('fi-input-wrp-icon');
        // console.log(focus_btn);
        if (focus_btn) {
            focus_btn[0].addEventListener('click', function() {
                inputQrCode.focus();
            });
        }

        // โหลดข้อมูลจาก localStorage ไปที่ตาราง
        loadTableFromStorage();
    });

    // ฟังก์ชันสำหรับบันทึกข้อมูลใน localStorage
    function saveToLocalStorage() {
        localStorage.setItem('tags', JSON.stringify(tags));
        localStorage.setItem('tagsDetail', JSON.stringify(tagsDetail));
    }

    // ฟังก์ชันสำหรับโหลดข้อมูลจาก localStorage และอัปเดตตาราง
    function loadTableFromStorage() {
        tagsDetail.forEach(tag => {
            updateTableBody2(tag);
        });
        updateTags(); // อัปเดต tags หลังจากโหลดข้อมูล
    }

    function updateTags() {
        // รวม qty ของ part_no ที่ซ้ำกันใน tagsDetail
        tags = tagsDetail.reduce((acc, current) => {
            const existing = acc.find(item => item.part_no === current.part_no);
            if (existing) {
                existing.qty += current.qty; // รวม qty
                existing.tag_qty++; // เพิ่ม tag_qty
            } else {
                acc.push({
                    part_no: current.part_no,
                    part_code: current.part_code,
                    part_name: current.part_name,
                    model: current.model,
                    qty: current.qty,
                    packing_name: current.packing_name,
                    from_whs: current.from_whs,
                    to_whs: current.to_whs,
                    tag_qty: 1
                }); // เพิ่มข้อมูลใหม่
            }
            return acc;
        }, []);

        // อัปเดตข้อมูลใน tableBody1
        updateTableBody1();
    }

    function updateTableBody1() {
        // ล้างข้อมูลเดิมใน tableBody1
        tableBody1.innerHTML = '';

        // เพิ่มข้อมูลจาก tags ไปใน tableBody1
        tags.forEach((tag, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td class="px-4 py-3">${tag.part_no}</td>
            <td class="px-4 py-3 hidden">${tag.part_code}</td>
            <td class="px-4 py-3 hidden">${tag.part_name}</td>
            <td class="px-4 py-3">${tag.model}</td>
            <td class="px-4 py-3">${tag.qty}</td>
            <td class="px-4 py-3">${tag.packing_name}</td>
            <td class="px-4 py-3 hidden">${tag.from_whs}</td>
            <td class="px-4 py-3 hidden">${tag.to_whs}</td>
            <td class="px-4 py-3">${tag.tag_qty}</td>
            <td class="px-4 py-3">
                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="deleteTag(${index})">Delete</button>
            </td>
        `;
            tableBody1.appendChild(row);
        });
    }

    function updateTableBody2(value) {
        const row = document.createElement("tr");
        const index = tagsDetail.indexOf(value);
        row.innerHTML = `
            <td class="px-4 py-3">${value.id}</td>
            <td class="px-4 py-3">${value.qr_code}</td>
            <td class="px-4 py-3">${value.part_no}</td>
            <td class="px-4 py-3">${value.part_code}</td>
            <td class="px-4 py-3">${value.part_name}</td>
            <td class="px-4 py-3">${value.model}</td>
            <td class="px-4 py-3">${value.qty}</td>
            <td class="px-4 py-3">${value.packing_name}</td>
            <td class="px-4 py-3">${value.from_whs}</td>
            <td class="px-4 py-3">${value.to_whs}</td>
            <td class="px-4 py-3">
                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="deleteTagDetail(${index})">Delete</button>
            </td>
        `;
        tableBody2.appendChild(row);
    }

    function sortTableById() {
        // Sort tagsDetail by id in descending order
        tagsDetail.sort((a, b) => b.id - a.id); // For ascending, use `a.id - b.id`

        // Clear the table body and re-render it
        tableBody2.innerHTML = '';
        tagsDetail.forEach(tag => {
            updateTableBody2(tag);
        });
    }

    function deleteTag(index) {
        // ลบข้อมูลออกจาก tags
        const removedTag = tags.splice(index, 1)[0];

        // อัปเดต tagsDetail โดยลบข้อมูลที่เกี่ยวข้องกับ tag ที่ถูกลบ
        tagsDetail = tagsDetail.filter(tagDetail => tagDetail.part_no !== removedTag.part_no);

        // บันทึกข้อมูลที่อัปเดตลงใน localStorage
        saveToLocalStorage();

        // อัปเดตตาราง
        updateTableBody1();
        tableBody2.innerHTML = '';
        tagsDetail.forEach(tag => {
            updateTableBody2(tag);
        });
    }

    function deleteTagDetail(index) {
        // ลบข้อมูลออกจาก tagsDetail
        tagsDetail.splice(index, 1);

        // บันทึกข้อมูลที่อัปเดตลงใน localStorage
        saveToLocalStorage();

        tableBody2.innerHTML = '';
        tagsDetail.forEach(tag => {
            updateTableBody2(tag);
        });
        updateTags();
    }

    function handleSave() {
        const table = document.getElementById('partsTable');
        const tableRows = table.querySelectorAll('tbody tr'); // ค้นหาแถวทั้งหมดใน partsTable
        if (tableRows.length === 0) {
            // @this.handleNotification(
            //     "เกิดข้อผิดพลาด",
            //     "ไม่มีข้อมูลในตาราง กรุณาเพิ่มข้อมูลก่อนบันทึก",
            //     "warning"
            // );
            return; // ยุติการทำงานของฟังก์ชัน
        }
        document.getElementById('openConfirmSaveModal').click();
    }

    function confirmSaveModal() {
        const section = document.getElementById('section');
        // console.log(section.value);
        const btn_save = document.getElementById('btn_save');
        btn_save.style.display = "none";

        // ปิด Modal
        @this.$dispatch('close-modal', {
            id: 'confirmSaveModal'
        });

        // ส่งข้อมูลไปยัง Backend
        @this.handleConfirmSave(section.value, tagsDetail, tags);

        // ลบข้อมูลใน localStorage
        localStorage.removeItem('tags');
        localStorage.removeItem('tagsDetail');
    }
</script>
