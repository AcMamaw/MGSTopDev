@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<style>[x-cloak]{display:none !important;}</style>

<div x-data="customerPage()">

    <!-- Success toast -->
    <div x-show="showToast"
         x-transition
         class="fixed top-4 left-1/2 -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm z-[9999]">
        <span x-text="toastMessage"></span>
    </div>

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage customer records including contact information and addresses.</p>
    </header>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

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
            <input type="text" id="customer-search" placeholder="Search customers"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Customer -->
        <button @click="openAddModal()"
                class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Customer</span>
        </button>
    </div>

    <!-- Customers Table -->
    <div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
        <div class="overflow-x-auto">
            <table id="customer-table" class="min-w-full table-auto">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">First Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Middle Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Last Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Address</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
                </thead>
                <tbody id="customer-table-body" class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50"
                        data-id="{{ $customer->customer_id }}"
                        data-fname="{{ $customer->fname }}"
                        data-mname="{{ $customer->mname }}"
                        data-lname="{{ $customer->lname }}"
                        data-contact_no="{{ $customer->contact_no }}"
                        data-address="{{ $customer->address }}">
                        <td class="px-4 py-3 text-center text-gray-800 font-medium">
                            C{{ str_pad($customer->customer_id,3,'0',STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $customer->fname }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $customer->mname }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $customer->lname }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $customer->contact_no }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $customer->address }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button title="Edit"
                                        @click="openEditModal($event)"
                                        class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>
                                <button title="Archive"
                                        onclick="deleteCustomerRow(this)"
                                        class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 4h18v4H3z" />
                                        <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                        <path d="M10 12h4" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="customer-empty-initial">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No customers found. Add a new customer to get started.
                        </td>
                    </tr>
                @endforelse

                <!-- Empty state for search -->
                <tr id="customer-empty-search" style="display:none;">
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
                            <p class="text-gray-700 font-semibold">No customers found</p>
                            <p class="text-gray-400 text-xs">
                                There are currently no customers matching these filters.
                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add / Edit Customer Modal -->
    <div x-show="showCustomerModal" x-cloak x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div @click.away="closeModal()"
             class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">
            <h2 class="text-2xl font-bold mb-6 text-gray-800"
                x-text="isEdit ? 'Edit Customer' : 'Add New Customer'"></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">First Name</label>
                        <input type="text" x-model="fname"
                               @focus="if(isEdit){ $el.select(); }"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                        <input type="text" x-model="mname"
                               @focus="if(isEdit){ $el.select(); }"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                        <input type="text" x-model="lname"
                               @focus="if(isEdit){ $el.select(); }"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900">
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                        <input type="text" x-model="contact_no"
                               @focus="if(isEdit){ $el.select(); }"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Address</label>
                        <input type="text" x-model="address"
                               @focus="if(isEdit){ $el.select(); }"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="closeModal()"
                        class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                    Cancel
                </button>

                <button x-show="!isEdit" @click="addCustomer()"
                        class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                    Confirm
                </button>

                <button x-show="isEdit" @click="updateCustomer()"
                        class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                    Update
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
        <div id="customer-pagination-info"></div>
        <ul id="customer-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>
</div>

<script>
function customerPage() {
    return {
        showCustomerModal: false,
        isEdit: false,
        editingId: null,

        fname: '',
        mname: '',
        lname: '',
        contact_no: '',
        address: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.fname = '';
            this.mname = '';
            this.lname = '';
            this.contact_no = '';
            this.address = '';
            this.showCustomerModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            this.isEdit = true;
            this.editingId = row.dataset.id;
            this.fname = row.dataset.fname;
            this.mname = row.dataset.mname ?? '';
            this.lname = row.dataset.lname;
            this.contact_no = row.dataset.contact_no ?? '';
            this.address = row.dataset.address ?? '';
            this.showCustomerModal = true;
        },

        closeModal() {
            this.showCustomerModal = false;
        },

        addCustomer() {
            if (!this.fname.trim() || !this.lname.trim() || !this.contact_no.trim()) {
                alert('First Name, Last Name, and Contact No are required!');
                return;
            }

            fetch('{{ route("customers.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    fname: this.fname,
                    mname: this.mname,
                    lname: this.lname,
                    contact_no: this.contact_no,
                    address: this.address
                })
            })
            .then(res => res.json())
            .then(c => {
                const tbody = document.getElementById('customer-table-body');
                const emptyInitial = document.getElementById('customer-empty-initial');
                if (emptyInitial) emptyInitial.style.display = 'none';

                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.dataset.id = c.customer_id;
                row.dataset.fname = c.fname;
                row.dataset.mname = c.mname ?? '';
                row.dataset.lname = c.lname;
                row.dataset.contact_no = c.contact_no ?? '';
                row.dataset.address = c.address ?? '';

                row.innerHTML = `
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">C${String(c.customer_id).padStart(3,'0')}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${c.fname}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${c.mname ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${c.lname}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${c.contact_no ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">${c.address ?? ''}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                onclick="window.__customerOpenEditFromRow(event)"
                                class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive"
                                onclick="deleteCustomerRow(this)"
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

                const emptySearch = document.getElementById('customer-empty-search');
                tbody.insertBefore(row, emptySearch);

                refreshCustomerRows();
                showCustomerPage(1);

                this.closeModal();
                alert('Customer added successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while saving the customer.');
            });
        },

        updateCustomer() {
            if (!this.editingId) return;

            fetch('{{ route("customers.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    fname: this.fname,
                    mname: this.mname,
                    lname: this.lname,
                    contact_no: this.contact_no,
                    address: this.address
                })
            })
            .then(res => res.json())
            .then(c => {
                const tbody = document.getElementById('customer-table-body');
                const row = Array.from(tbody.querySelectorAll('tr')).find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.fname = c.fname;
                row.dataset.mname = c.mname ?? '';
                row.dataset.lname = c.lname;
                row.dataset.contact_no = c.contact_no ?? '';
                row.dataset.address = c.address ?? '';

                row.children[0].textContent = 'C' + String(c.customer_id).padStart(3,'0');
                row.children[1].textContent = c.fname;
                row.children[2].textContent = c.mname ?? '';
                row.children[3].textContent = c.lname;
                row.children[4].textContent = c.contact_no ?? '';
                row.children[5].textContent = c.address ?? '';

                this.closeModal();
                alert('Customer updated successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while updating the customer.');
            });
        }
    }
}

// expose helper so dynamically-added rows can open the edit modal
window.__customerOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="customerPage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

// pagination + search
const custRowsPerPage = 5;
const custTableBody   = document.getElementById('customer-table-body');
const custEmptyInitial= document.getElementById('customer-empty-initial');
const custEmptySearch = document.getElementById('customer-empty-search');

let custAllRows = Array.from(custTableBody.querySelectorAll('tr'))
    .filter(r => ![custEmptyInitial, custEmptySearch].includes(r));
let custFilteredRows = [...custAllRows];

const custPagLinks = document.getElementById('customer-pagination-links');
const custPagInfo  = document.getElementById('customer-pagination-info');
let custCurrentPage = 1;

function refreshCustomerRows() {
    custAllRows = Array.from(custTableBody.querySelectorAll('tr'))
        .filter(r => ![custEmptyInitial, custEmptySearch].includes(r));
    custFilteredRows = [...custAllRows];
}

function showCustomerPage(page) {
    const total = custFilteredRows.length;
    const totalPages = Math.max(1, Math.ceil(total / custRowsPerPage));

    if (total === 0) {
        if (custEmptyInitial) custEmptyInitial.style.display = '';
        if (custEmptySearch && document.getElementById('customer-search').value.trim()) {
            custEmptyInitial.style.display = 'none';
            custEmptySearch.style.display = '';
        }
        custPagLinks.innerHTML = '';
        custPagInfo.textContent = 'Showing 0 to 0 of 0 results';
        return;
    }

    if (custEmptyInitial) custEmptyInitial.style.display = 'none';
    if (custEmptySearch)  custEmptySearch.style.display  = 'none';

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    custCurrentPage = page;

    custAllRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * custRowsPerPage;
    const end   = start + custRowsPerPage;
    custFilteredRows.slice(start, end).forEach(r => r.style.display = '');

    renderCustomerPagination(totalPages);

    const startItem = total ? start + 1 : 0;
    const endItem   = Math.min(end, total);
    custPagInfo.textContent = `Showing ${startItem} to ${endItem} of ${total} results`;
}

function renderCustomerPagination(totalPages) {
    custPagLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = custCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
    if (custCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCustomerPage(custCurrentPage - 1);
        });
    }
    custPagLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === custCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === custCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== custCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showCustomerPage(i);
            });
        }
        custPagLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = custCurrentPage === totalPages ? 'Next »' : '<a href="#">Next »</a>';
    if (custCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCustomerPage(custCurrentPage + 1);
        });
    }
    custPagLinks.appendChild(next);
}

function deleteCustomerRow(button) {
    if (!confirm('Are you sure you want to archive this customer?')) return;

    const row = button.closest('tr');
    // optional: call backend delete using row.dataset.id
    row.remove();

    refreshCustomerRows();
    if (custFilteredRows.length === 0) {
        showCustomerPage(1);
    } else {
        showCustomerPage(custCurrentPage);
    }
}

// Search
document.getElementById('customer-search').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();

    if (!query) {
        custFilteredRows = [...custAllRows];
    } else {
        custFilteredRows = custAllRows.filter(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            return cells.some(c => c.textContent.toLowerCase().includes(query));
        });
    }

    if (custFilteredRows.length === 0) {
        custAllRows.forEach(r => r.style.display = 'none');
        if (custEmptyInitial) custEmptyInitial.style.display = 'none';
        if (custEmptySearch)  custEmptySearch.style.display  = '';
        custPagLinks.innerHTML = '';
        custPagInfo.textContent = 'Showing 0 to 0 of 0 results';
    } else {
        if (custEmptySearch) custEmptySearch.style.display = 'none';
        showCustomerPage(1);
    }
});

// init
showCustomerPage(1);
</script>
@endsection
