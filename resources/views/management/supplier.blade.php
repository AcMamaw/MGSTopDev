@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<style>[x-cloak]{display:none !important;}</style>

<div x-data="supplierPage()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage supplier records including contact details and addresses.</p>
</header>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
    <!-- Search -->
    <div class="relative w-full md:w-1/4">
        <input type="text" id="supplier-search" placeholder="Search suppliers"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
        </svg>
    </div>

    <!-- Add Supplier -->
    <button @click="openAddModal()"
        class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>
        <span>Add New Supplier</span>
    </button>
</div>

<!-- Supplier Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
    <table id="supplier-table" class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier Name</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact Person</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Email</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Address</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
            </tr>
        </thead>
        <tbody id="supplier-table-body" class="divide-y divide-gray-100">
            @forelse($suppliers as $supplier)
            <tr class="hover:bg-gray-50"
                data-id="{{ $supplier->supplier_id }}"
                data-name="{{ $supplier->supplier_name }}"
                data-contact_person="{{ $supplier->contact_person }}"
                data-contact_no="{{ $supplier->contact_no }}"
                data-email="{{ $supplier->email }}"
                data-address="{{ $supplier->address }}">
                <td class="px-4 py-3 text-center text-gray-800 font-medium">
                    SUP{{ str_pad($supplier->supplier_id,3,'0',STR_PAD_LEFT) }}
                </td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->supplier_name }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_person }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_no }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->email }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->address }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <button title="Edit"
                                @click="openEditModal($event)"
                                class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                             <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor'
                                        stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-square-pen'>
                                        <path d='M12 20h9'/>
                                        <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z'/>
                                    </svg>
                        </button>
                        <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 4h18v4H3z"/>
                                <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                <path d="M10 12h4"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="supplier-empty-initial">
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                    No suppliers found. Add a new supplier to get started.
                </td>
            </tr>
            @endforelse

          <!-- empty state for search -->
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
                            There are currently no suppliers matching these filters.
                        </p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Add / Edit Supplier Modal (shared) -->
<div x-show="showSupplierModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div @click.away="closeModal()"
         class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative text-center">

        <h2 class="text-2xl font-bold mb-6 text-gray-800"
            x-text="isEdit ? 'Edit Supplier' : 'Add New Supplier'"></h2>

        <div class="space-y-4 flex flex-col items-center">
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Supplier Name</label>
                <input type="text"
                       x-model="supplierName"
                       @focus="supplierName = ''"
                       class="w-full px-4 py-2 border rounded-lg text-center text-gray-500
                              focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Contact Person</label>
                <input type="text"
                       x-model="contactPerson"
                       @focus="contactPerson = ''"
                       class="w-full px-4 py-2 border rounded-lg text-center text-gray-500
                              focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                <input type="text"
                       x-model="contactNo"
                       @focus="contactNo = ''"
                       class="w-full px-4 py-2 border rounded-lg text-center text-gray-500
                              focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email"
                       x-model="email"
                       @focus="email = ''"
                       class="w-full px-4 py-2 border rounded-lg text-center text-gray-500
                              focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Address</label>
                <input type="text"
                       x-model="address"
                       @focus="address = ''"
                       class="w-full px-4 py-2 border rounded-lg text-center text-gray-500
                              focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>

        <div class="mt-6 flex justify-center gap-3">
            <button @click="closeModal()"
                    class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold
                           bg-transparent hover:bg-yellow-100 transition">
                Cancel
            </button>

            <button x-show="!isEdit" @click="addSupplier()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>

            <button x-show="isEdit" @click="updateSupplier()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Update
            </button>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="supplier-pagination-info"></div>
    <ul id="supplier-pagination-links" class="pagination-links flex gap-2"></ul>
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
                row.className = 'hover:bg-gray-50';
                row.dataset.id = s.supplier_id;
                row.dataset.name = s.supplier_name;
                row.dataset.contact_person = s.contact_person ?? '';
                row.dataset.contact_no = s.contact_no ?? '';
                row.dataset.email = s.email ?? '';
                row.dataset.address = s.address ?? '';

                row.innerHTML = `
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">SUP${String(s.supplier_id).padStart(3,'0')}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.supplier_name}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.contact_person ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.contact_no ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.email ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${s.address ?? ''}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                onclick="window.__supplierOpenEditFromRow(event)"
                                class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive"
                                onclick="deleteRow(this)"
                                class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
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

// DELETE FUNCTION (used by onclick="deleteRow(this)")
function deleteRow(button) {
    if (!confirm('Are you sure you want to archive this supplier?')) return;

    const row = button.closest('tr');
    // optional: call backend here using row.dataset.id

    row.remove();

    refreshSupplierRows();

    if (filteredRows.length === 0) {
        showSupplierPage(1);
    } else {
        showSupplierPage(supCurrentPage);
    }
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
@endsection
