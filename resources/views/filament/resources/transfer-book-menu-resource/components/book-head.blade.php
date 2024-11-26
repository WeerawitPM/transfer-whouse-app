{{-- <x-filament::section collapsible>
    <x-slot name="heading">
        Book details
    </x-slot>
</x-filament::section> --}}

<x-filament::card>
    <style>
        .hidden_section {
            display: none;
        }

        .show_section {
            display: block;
        }
    </style>
    {{-- Content --}}
    <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Book Name
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['FCCODE'] }}" id="book_name" name="book_name" readonly>
        </div>
        <div class="w-full md:w-1/2 px-3">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Book Prefix
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['FCPREFIX'] }}" id="book_prefix" name="book_prefix" readonly>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                From whouse
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['from_whs']['FCCODE'] }}" id="from_whs" name="from_whs" readonly>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-2">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                To whouse
            </label>
            <input
                class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm"
                type="text" value="{{ $book['to_whs']['FCCODE'] }}" id="to_whs" name="to_whs" readonly>
        </div>
        <div class="w-full md:w-1/3 px-3">
            <label class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                Section
            </label>
            <div class="relative w-full">
                <div class="dropdown">
                    <button id="dropdownButton" wire:ignore
                        class="w-full dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-2 px-4 rounded-lg border dark:border-gray-600 text-sm">
                        {{ $user->sect->FCNAME ?? 'Select Section' }}
                    </button>
                    <input type="hidden" name="section" id="section" value="{{ $user->sect->FCSKID ?? '' }}" wire:ignore />
                    <div id="dropdownMenu"
                        class="hidden_section absolute bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg w-full max-h-60 overflow-y-auto z-10">
                        <div class="p-2">
                            <input type="text" id="searchInput" placeholder="Search..."
                                class="w-full bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 text-gray-700 dark:text-gray-200 text-sm">
                        </div>
                        <ul id="dropdownList" class="list-none p-2">
                            @foreach ($sections as $section)
                                <li data-value="{{ $section['FCSKID'] }}"
                                    class="py-1 px-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                    {{ $section['FCNAME'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::card>

<script>
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const searchInput = document.getElementById('searchInput');
    const dropdownList = document.getElementById('dropdownList');
    const section = document.getElementById('section');

    // เปิด-ปิด dropdown เมื่อกดปุ่ม
    dropdownButton.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show_section');
    });

    // ซ่อน dropdown เมื่อคลิกข้างนอก
    document.addEventListener('click', function(event) {
        if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show_section');
        }
    });

    // ฟังก์ชันการค้นหา
    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();
        const items = dropdownList.querySelectorAll('li');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // กำหนดค่าที่เลือก
    dropdownList.addEventListener('click', function(event) {
        if (event.target.tagName === 'LI') {
            const selectedValue = event.target.dataset.value;
            const selectedText = event.target.textContent;

            // แสดงค่าที่เลือกในปุ่ม
            dropdownButton.textContent = selectedText;
            section.value = selectedValue;

            // ซ่อน dropdown
            dropdownMenu.classList.remove('show_section');

            // console.log('Selected Value:', section.value);
        }
    });
</script>
