<x-filament-panels::page>
    {{ $this->form }}
    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>

<script>
    const inputQrCode = document.getElementById('input_qr_code');

    inputQrCode.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            @this.handleQrCodeInput(inputQrCode.value);
            inputQrCode.value = '';
        }
    });
</script>
