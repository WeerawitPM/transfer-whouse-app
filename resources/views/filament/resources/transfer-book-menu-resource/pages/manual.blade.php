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
        <table class="w-full table-auto bg-white dark:bg-gray-800 shadow rounded-lg">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-white uppercase text-sm">
                <tr>
                    <th class="px-4 py-2">FCSKID</th>
                    <th class="px-4 py-2">Part No</th>
                    <th class="px-4 py-2">Part Code</th>
                    <th class="px-4 py-2">Part Name</th>
                    <th class="px-4 py-2">Model</th>
                    <th class="px-4 py-2">SModel</th>
                    <th class="px-4 py-2">Stock Qty</th>
                    <th class="px-4 py-2">Qty</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-white">
                @forelse ($part_selected as $index => $part)
                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                        id="tr_{{ $index }}" name="tr_{{ $index }}">
                        <td class="px-4 py-2">{{ $part['FCSKID'] }}</td>
                        <td class="px-4 py-2">{{ $part['CPART_NO'] }}</td>
                        <td class="px-4 py-2">{{ $part['CCODE'] }}</td>
                        <td class="px-4 py-2">{{ $part['CPART_NAME'] }}</td>
                        <td class="px-4 py-2">{{ $part['MODEL'] }}</td>
                        <td class="px-4 py-2">{{ $part['SMODEL'] }}</td>
                        <td class="px-4 py-2">{{ number_format($part['STOCKQTY'], 0) }}</td>
                        <td class="px-4 py-2">
                            <input type="number" min="0" required
                                class="dark:bg-gray-700 text-gray-900 dark:text-white rounded p-1 border-0"
                                id="qty_{{ $index }}" name="qty_{{ $index }}">
                        </td>
                        <td class="px-4 py-2">
                            <x-filament::button color="danger" size="sm"
                                onclick="deleteRow(this)">Delete</x-filament::button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
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
