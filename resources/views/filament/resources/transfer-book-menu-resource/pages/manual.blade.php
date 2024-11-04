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
        <table class="w-full table-auto bg-white shadow rounded-lg">
            <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-2">FCSKID</th>
                    <th class="px-4 py-2">Part No</th>
                    <th class="px-4 py-2">Part Code</th>
                    <th class="px-4 py-2">Part Name</th>
                    <th class="px-4 py-2">Model</th>
                    <th class="px-4 py-2">SModel</th>
                    <th class="px-4 py-2">Stock Qty</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($part_selected as $index => $part)
                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} border-b hover:bg-gray-100">
                        <td class="px-4 py-2">{{ $part['FCSKID'] }}</td>
                        <td class="px-4 py-2">{{ $part['CPART_NO'] }}</td>
                        <td class="px-4 py-2">{{ $part['CCODE'] }}</td>
                        <td class="px-4 py-2">{{ $part['CPART_NAME'] }}</td>
                        <td class="px-4 py-2">{{ $part['MODEL'] }}</td>
                        <td class="px-4 py-2">{{ $part['SMODEL'] }}</td>
                        <td class="px-4 py-2">{{ number_format($part['STOCKQTY'], 0) }}</td>
                        <td class="px-4 py-2">
                            <x-filament::button color="danger" size="sm"
                                onclick="deleteRow(this)">Delete</x-filament::button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-2 text-center text-gray-500">
                            No data available
                        </td>
                    </tr>
                @endforelse
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
            row.remove();
        }
    }
</script>
