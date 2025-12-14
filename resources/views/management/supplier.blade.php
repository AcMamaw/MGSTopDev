@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<style>[x-cloak]{display:none !important;}</style>

<div x-data="supplierPage()">

    <header class="mb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5L12 2z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                Suppliers
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Efficiently manage all supplier records, including contact information and addresses.</p>
    </header>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-lg shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-lg shadow-sm">
            <p class="font-bold mb-1">Error Submitting Form:</p>
            <ul class="list-disc list-inside ml-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

      <div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">

            {{-- Left: Search --}}
            <div class="relative w-full md:w-80">
                <input type="text" id="supplier-search" placeholder="Search suppliers"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm
                            focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>

            {{-- Right: Archive + Add --}}
            <div class="w-full md:w-auto flex items-center justify-end space-x-3">
                @include('added.archive_supplier')

                <button @click="openAddModal()"
                        class="w-full md:w-auto bg-yellow-400 text-gray-900 px-6 py-2 rounded-full font-bold
                            flex items-center justify-center space-x-2 hover:bg-yellow-500 transition
                            shadow-lg shadow-yellow-200/50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-plus">
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>
                    <span>Add New Supplier</span>
                </button>
            </div>

        </div>

    </div>

    {{-- TABLE OUTSIDE PADDED WRAPPER FOR FULL STRETCH --}}
    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="supplier-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Supplier ID</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Supplier Name</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Contact Person</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Contact No</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Address</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="supplier-table-body" class="divide-y divide-gray-100">
                 @forelse($suppliers->where('archive', '!=', 'Archived') as $supplier)
                    <tr class="hover:bg-yellow-50/50 transition-colors"
                        data-id="{{ $supplier->supplier_id }}"
                        data-name="{{ $supplier->supplier_name }}"
                        data-contact_person="{{ $supplier->contact_person }}"
                        data-contact_no="{{ $supplier->contact_no }}"
                        data-email="{{ $supplier->email }}"
                        data-address="{{ $supplier->address }}">
                        <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                            SUP{{ str_pad($supplier->supplier_id,3,'0',STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->supplier_name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_person }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_no }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->email }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate">{{ $supplier->address }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button title="Edit"
                                        @click="openEditModal($event)"
                                        class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>
                                  <button title="Archive" onclick="archiveSupplier(this)" 
                                        class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-archive">
                                        <path d="M3 4h18v4H3z" />
                                        <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                        <path d="M10 12h4" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="supplier-empty-initial">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500 italic">
                            No suppliers found. Click "Add New Supplier" to get started.
                        </td>
                    </tr>
                    @endforelse

                    <tr id="supplier-empty-search" style="display:none;">
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500 text-sm">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-16 w-16 text-gray-300"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                    <path d="M14 3v5h5" />
                                    <path d="M9 13h6" />
                                    <path d="M9 17h3" />
                                </svg>
                                <p class="text-gray-700 font-semibold">
                                    No suppliers found
                                </p>
                                <p class="text-gray-400 text-xs">
                                    There are currently no suppliers matching your search.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION BACK INSIDE PADDED WRAPPER --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
            <div id="supplier-pagination-info"></div>
            <ul id="supplier-pagination-links" class="pagination-links flex gap-2"></ul>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="showSupplierModal" x-cloak x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">

        <div @click.away="closeModal()"
            class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative">

            <h2 class="text-3xl font-extrabold mb-7 text-center text-gray-800"
                x-text="isEdit ? 'Edit Supplier Record' : 'Add New Supplier'"></h2>

            <form :action="isEdit ? `/suppliers/${supplierId}` : '/suppliers'" method="POST" @submit.prevent="isEdit ? updateSupplier($event) : addSupplier($event)">
                @csrf
                <input x-show="isEdit" type="hidden" name="_method" value="PUT">
                <input x-show="isEdit" type="hidden" name="supplier_id" :value="supplierId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" name="supplier_name"
                            x-model="supplierName"
                            placeholder="e.g. ABC Distribution Co."
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Person</label>
                        <input type="text" name="contact_person"
                            x-model="contactPerson"
                            placeholder="e.g. John Doe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contact No <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_no"
                            x-model="contactNo"
                            placeholder="e.g. +63 987 654 3210"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email"
                            x-model="email"
                            placeholder="e.g. info@supplier.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="2"
                            x-model="address"
                            placeholder="e.g. 123 Business St, Metro Manila, PH 1000"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="closeModal()"
                            class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                        Cancel
                    </button>

                    <button type="submit" 
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                        <span x-text="isEdit ? 'Save Changes' : 'Confirm Add'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>


</div>

<script>
function supplierPage() {
    return {
        showSupplierModal: false,
        isEdit: false,
        editingId: null,

        supplierName: '',
        contactPerson: '',
        contactNo: '',
        email: '',
        address: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.supplierName = '';
            this.contactPerson = '';
            this.contactNo = '';
            this.email = '';
            this.address = '';
            this.showSupplierModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            this.isEdit = true;
            this.editingId = row.dataset.id;
            this.supplierName = row.dataset.name;
            this.contactPerson = row.dataset.contact_person;
            this.contactNo = row.dataset.contact_no;
            this.email = row.dataset.email;
            this.address = row.dataset.address;
            this.showSupplierModal = true;
        },

        closeModal() {
            this.showSupplierModal = false;
        },

        addSupplier() {
            if (!this.supplierName.trim()) {
                alert('Supplier Name is required!');
                return;
            }

            fetch('{{ route("suppliers.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    supplier_name: this.supplierName,
                    contact_person: this.contactPerson,
                    contact_no: this.contactNo,
                    email: this.email,
                    address: this.address
                })
            })
            .then(res => res.json())
            .then(s => {
                const tbody = document.getElementById('supplier-table-body');
                const emptyInitial = document.getElementById('supplier-empty-initial');
                if (emptyInitial) emptyInitial.style.display = 'none';

                const row = document.createElement('tr');
                row.className = 'hover:bg-yellow-50/50 transition-colors';
                row.dataset.id = s.supplier_id;
                row.dataset.name = s.supplier_name;
                row.dataset.contact_person = s.contact_person ?? '';
                row.dataset.contact_no = s.contact_no ?? '';
                row.dataset.email = s.email ?? '';
                row.dataset.address = s.address ?? '';

                row.innerHTML = `
                    <td class="px-4 py-3 text-center text-gray-800 font-semibold">SUP${String(s.supplier_id).padStart(3,'0')}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.supplier_name}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.contact_person ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.contact_no ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.email ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate">${s.address ?? ''}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                onclick="window.__supplierOpenEditFromRow(event)"
                                class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive"
                                onclick="deleteRow(this)"
                                class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 4h18v4H3z"/>
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                    <path d="M10 12h4"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                tbody.insertBefore(row, document.getElementById('supplier-empty-search'));

                refreshSupplierRows();
                showSupplierPage(1);

                this.closeModal();
                alert('Supplier added successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while saving the supplier.');
            });
        },

        updateSupplier() {
            if (!this.editingId) return;

            fetch('{{ route("suppliers.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    supplier_name: this.supplierName,
                    contact_person: this.contactPerson,
                    contact_no: this.contactNo,
                    email: this.email,
                    address: this.address
                })
            })
            .then(res => res.json())
            .then(s => {
                const tbody = document.getElementById('supplier-table-body');
                const row = Array.from(tbody.querySelectorAll('tr')).find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.name = s.supplier_name;
                row.dataset.contact_person = s.contact_person ?? '';
                row.dataset.contact_no = s.contact_no ?? '';
                row.dataset.email = s.email ?? '';
                row.dataset.address = s.address ?? '';

                row.children[0].textContent = 'SUP' + String(s.supplier_id).padStart(3,'0');
                row.children[1].textContent = s.supplier_name;
                row.children[2].textContent = s.contact_person ?? '';
                row.children[3].textContent = s.contact_no ?? '';
                row.children[4].textContent = s.email ?? '';
                row.children[5].textContent = s.address ?? '';

                this.closeModal();
                alert('Supplier updated successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while updating the supplier.');
            });
        }
    }
}

// expose helper so dynamically-added rows can open the edit modal
window.__supplierOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="supplierPage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

// Pagination + search logic
const supRowsPerPage = 5;
const supTableBody   = document.getElementById('supplier-table-body');
const emptyInitialRow= document.getElementById('supplier-empty-initial');
const emptySearchRow = document.getElementById('supplier-empty-search');

let allRows = Array.from(supTableBody.querySelectorAll('tr'))
    .filter(r => ![emptyInitialRow, emptySearchRow].includes(r));

let filteredRows = [...allRows];

const supPaginationLinks = document.getElementById('supplier-pagination-links');
const supPaginationInfo  = document.getElementById('supplier-pagination-info');
let supCurrentPage = 1;

function refreshSupplierRows() {
    allRows = Array.from(supTableBody.querySelectorAll('tr'))
        .filter(r => ![emptyInitialRow, emptySearchRow].includes(r));
    filteredRows = [...allRows];
}

function showSupplierPage(page) {
    const total = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(total / supRowsPerPage));

    if (total === 0) {
        if (emptyInitialRow) emptyInitialRow.style.display = '';
        if (emptySearchRow && document.getElementById('supplier-search').value.trim()) {
            emptyInitialRow.style.display = 'none';
            emptySearchRow.style.display = '';
        }
        supPaginationLinks.innerHTML = '';
        supPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        return;
    }

    if (emptyInitialRow) emptyInitialRow.style.display = 'none';
    if (emptySearchRow)  emptySearchRow.style.display  = 'none';

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    supCurrentPage = page;

    allRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * supRowsPerPage;
    const end   = start + supRowsPerPage;
    filteredRows.slice(start, end).forEach(r => r.style.display = '');

    renderSupplierPagination(totalPages);

    const startItem = total ? start + 1 : 0;
    const endItem   = Math.min(end, total);
    supPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${total} results`;
}

function renderSupplierPagination(totalPages) {
    supPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = supCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
    if (supCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSupplierPage(supCurrentPage - 1);
        });
    }
    supPaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === supCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === supCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== supCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showSupplierPage(i);
            });
        }
        supPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = supCurrentPage === totalPages ? 'Next »' : '<a href="#">Next »</a>';
    if (supCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSupplierPage(supCurrentPage + 1);
        });
    }
    supPaginationLinks.appendChild(next);
}


// Search
document.getElementById('supplier-search').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();

    if (!query) {
        filteredRows = [...allRows];
    } else {
        filteredRows = allRows.filter(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            return cells.some(c => c.textContent.toLowerCase().includes(query));
        });
    }

    if (filteredRows.length === 0) {
        allRows.forEach(r => r.style.display = 'none');
        if (emptyInitialRow) emptyInitialRow.style.display = 'none';
        if (emptySearchRow)  emptySearchRow.style.display  = '';
        supPaginationLinks.innerHTML = '';
        supPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
    } else {
        if (emptySearchRow) emptySearchRow.style.display = 'none';
        showSupplierPage(1);
    }
});

// Initialize
showSupplierPage(1);
</script>

<script>
function archiveSupplier(button) {
    if (!confirm('Are you sure you want to archive this supplier?')) return;

    const row = button.closest('tr');
    if (!row) return;

    const id = row.dataset.id;

    fetch('{{ route("suppliers.archive", ":id") }}'.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ archive: 'Archived' })
    })
    .then(res => res.json())
    .then(() => {
        row.classList.add('opacity-50');
        alert('Supplier archived successfully!');
        location.reload();
    })
    .catch(() => {
        alert('Failed to archive supplier. Please try again.');
    });
}

function unarchiveSupplier(id) {
    if (!confirm('Remove this supplier from archive?')) return;

    fetch('{{ route("suppliers.unarchive", ":id") }}'.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ archive: null })
    })
    .then(res => res.json())
    .then(() => {
        window.location.reload();
    });
}

function deleteSupplierPermanently(id) {
    if (!confirm('Permanently delete this supplier? This cannot be undone.')) return;

    fetch('{{ route("suppliers.destroy", ":id") }}'.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        window.location.reload();
    });
}
</script>
@endsection