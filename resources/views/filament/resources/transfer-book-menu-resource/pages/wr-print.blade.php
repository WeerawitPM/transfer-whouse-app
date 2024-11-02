<x-filament-panels::page>
    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>

<script>
    function disableButton() {
        const generate_document = document.getElementById('generate_document');
        generate_document.style.display = "none";
        @this.generate_document();
    }
</script>
