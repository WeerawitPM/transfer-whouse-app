<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputQrCode = document.getElementById('input_qr_code');

        if (inputQrCode) {
            inputQrCode.setAttribute('readonly', true); // ปิดคีย์บอร์ด
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

        const focus_btn = document.getElementsByClassName('fi-input-wrp-icon');
        // console.log(focus_btn);
        if (focus_btn) {
            focus_btn[0].addEventListener('click', function() {
                inputQrCode.focus();
            });
        }
    });

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
        @this.$dispatch('close-modal', {
            id: 'confirmSaveModal'
        });
        @this.handleConfirmSave(section.value);
    }
</script>
