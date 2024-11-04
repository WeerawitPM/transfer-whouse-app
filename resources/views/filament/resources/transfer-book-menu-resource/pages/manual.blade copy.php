<x-filament-panels::page>
    <x-filament::modal width="5xl" :close-by-clicking-away="false">
        <x-slot name="trigger">
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

    <div class="mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>FCSKID</th>
                    <th>CPART_NO</th>
                    <th>CCODE</th>
                    <th>CPART_NAME</th>
                    <th>MODEL</th>
                    <th>SMODEL</th>
                    <th>STOCKQTY</th>
                </tr>
            </thead>
            <tbody id="partsTableBody">
                @foreach (session('part_selected', []) as $part)
                    <tr>
                        <td>{{ $part['FCSKID'] }}</td>
                        <td>{{ $part['CPART_NO'] }}</td>
                        <td>{{ $part['CCODE'] }}</td>
                        <td>{{ $part['CPART_NAME'] }}</td>
                        <td>{{ $part['MODEL'] }}</td>
                        <td>{{ $part['SMODEL'] }}</td>
                        <td>{{ number_format($part['STOCKQTY'], 2) }}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>

<script>
    const inputSearchPart = document.getElementById('input_search_part');

    inputSearchPart.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            @this.handleSearchPart(inputSearchPart.value);
            // inputSearchPart.value = '';
        }
    });

    function deleteRow(button) {
        // ยืนยันการลบ
        if (confirm('Are you sure you want to delete this item?')) {
            // หาแถว (tr) ที่ปุ่มอยู่
            const row = button.closest('tr');
            // ลบแถว
            row.remove()
        }
    }
</script>
