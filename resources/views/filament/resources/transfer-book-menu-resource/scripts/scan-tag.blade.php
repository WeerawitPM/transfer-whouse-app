<script>
    const section = document.getElementById('section');
    section.addEventListener('change', function() {
        // console.log(section.value);
        @this.handleUpdateSection(section.value);
    });

    document.addEventListener("DOMContentLoaded", function() {
        const inputQrCode = document.getElementById('input_qr_code');

        if (inputQrCode) {
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
    });
</script>
