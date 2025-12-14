<div x-data="{
        showArchiveModal: false,
        searchQuery: '',
        filterArchived() {
            const q = this.searchQuery.toLowerCase().trim();
            const rows = document.querySelectorAll('#archiveSuppliersBody tr[data-supplier]');
            let hasVisible = false;

            rows.forEach(row => {
                const searchable = row.getAttribute('data-search').toLowerCase();
                if (!q || searchable.includes(q)) {
                    row.style.display = '';
                    hasVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });

            const empty = document.getElementById('archiveSuppliersEmpty');
            if (empty) empty.style.display = hasVisible ? 'none' : '';
        }
    }"
>
   
<div class="w-full flex justify-end">
        <button @click="showArchiveModal = true"
                class="bg-gray-200 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center space-x-2
                       hover:bg-gray-300 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 6v6l4 2" />
            </svg>
            <span>Archive History</span>
        </button>
    </div>

    <div x-show="showArchiveModal" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showArchiveModal = false"
             class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex items-center justify-between">
                <span>Archive Suppliers</span>
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="filterArchived()"
                    placeholder="Search archived suppliers"
                    class="ml-4 w-64 px-3 py-1.5 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-1 focus:ring-yellow-500"
                >
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Supplier Name</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="archiveSuppliersBody" class="divide-y divide-gray-100">
                        @forelse ($suppliers->where('archive', 'Archived') as $supplier)
                            <tr
                                data-supplier
                                data-id="{{ $supplier->supplier_id }}"
                                data-search="{{ $supplier->supplier_name }} {{ $supplier->contact }} {{ $supplier->email }}"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-4 py-2 text-center text-sm font-semibold text-yellow-700">
                                    S{{ str_pad($supplier->supplier_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-800">
                                    {{ $supplier->supplier_name }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-700">
                                    {{ $supplier->contact_person }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-700">
                                    {{ $supplier->email }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm text-gray-700">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Remove from Archive --}}
                                        <button
                                            title="Remove from Archive"
                                            onclick="unarchiveSupplier({{ $supplier->supplier_id }})"
                                            class="inline-flex items-center justify-center h-9 w-9 rounded-full text-green-500
                                                   hover:text-green-700 hover:bg-green-100 transition-colors duration-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 width="25" height="25"
                                                 fill="none" stroke="currentColor"
                                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <g transform="scale(0.9) translate(1.5,1.5)">
                                                    <path d="M3 4h18v4H3z" />
                                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                                    <path d="M10 12h4" />
                                                </g>
                                            </svg>
                                        </button>

                                        {{-- Permanent Delete --}}
                                        <button
                                            title="Delete Permanently"
                                            onclick="deleteSupplierPermanently({{ $supplier->supplier_id }})"
                                            class="inline-flex items-center justify-center h-9 w-9 rounded-full text-red-500
                                                   hover:text-red-700 hover:bg-red-100 transition-colors duration-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 width="24" height="24"
                                                 fill="none" stroke="currentColor"
                                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <rect x="5" y="6" width="14" height="14" rx="2" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="archiveSuppliersEmpty">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No archived suppliers.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="showArchiveModal = false"
                        class="bg-yellow-500 text-gray-900 font-bold px-8 py-2 rounded-full hover:bg-yellow-600 transition shadow-md">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
