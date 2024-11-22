{{-- <x-filament::section collapsible>
    <x-slot name="heading">
        Book details
    </x-slot>
</x-filament::section> --}}

<x-filament::card>
    {{-- Content --}}
    <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Book Name
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['FCCODE'] }}" readonly>
        </div>
        <div class="w-full md:w-1/2 px-3">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Book Prefix
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['FCPREFIX'] }}" readonly>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                From whouse
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['from_whs']['FCCODE'] }}" readonly>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-2">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                To whouse
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['to_whs']['FCCODE'] }}" readonly>
        </div>
        <div class="w-full md:w-1/3 px-3">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Section
            </label>
            <select id="section"
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                name="section">
                @foreach ($sections as $section)
                    @if ($section['FCSKID'] == $user->sect->FCSKID)
                        <option value="{{ $section['FCSKID'] }}" selected>
                            {{ $section['FCNAME'] }}
                        </option>
                    @endif
                    <option value="{{ $section['FCSKID'] }}">
                        {{ $section['FCNAME'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</x-filament::card>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect("#section", {
            placeholder: "Select Section",
            allowEmptyOption: true,
            maxOptions: 500, // กำหนดจำนวนรายการสูงสุดใน dropdown
            create: false, // ถ้าต้องการให้ผู้ใช้สร้างรายการใหม่ได้เปลี่ยนเป็น true
        });
    });
</script>
