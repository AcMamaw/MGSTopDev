@extends('layouts.app')

@section('title', 'Products')

@section('content')
<style>[x-cloak]{display:none !important;}</style>

<div x-data="productPage()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Products</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage product records including supplier, price, unit, and status.</p>
</header>

<!-- Dynamic message -->
<div id="message-container" class="max-w-7xl mx-auto mb-6" style="display:none;">
    <div id="message-content" class="px-4 py-3 rounded"></div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-7xl mx-auto">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
    <div class="relative w-full md:w-1/4">
        <input type="text" id="product-search" placeholder="Search products"
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
    </div>

    <button @click="openAddModal()"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="lucide lucide-plus">
            <path d="M12 5v14" />
            <path d="M5 12h14" />
        </svg>
        <span>Add New Product</span>
    </button>
</div>

<!-- Products Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="product-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Unit</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="product-table-body" class="divide-y divide-gray-100">
                @foreach ($products as $product)
                <tr class="hover:bg-gray-50 product-row"
                    data-id="{{ $product->product_id }}"
                    data-supplier_id="{{ $product->supplier_id }}"
                    data-supplier_name="{{ $product->supplier->supplier_name ?? '-' }}"
                    data-product_name="{{ $product->product_name }}"
                    data-description="{{ $product->description }}"
                    data-unit="{{ $product->unit }}">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        P{{ str_pad($product->product_id,3,'0',STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->supplier->supplier_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->product_name }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->description }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->unit }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                    @click="openEditModal($event)"
                                    class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </button>
                            <button title="Archive" onclick="deleteRow(this)"
                                    class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
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
                @endforeach

                <tr id="product-empty-row" class="{{ $products->count() ? 'hidden' : '' }}">
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500 text-sm">
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
                                No products found
                            </p>
                            <p class="text-gray-400 text-xs">
                                There are currently no products matching these filters.
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add / Edit Product Modal -->
<div x-show="showProductModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="closeModal()"
         class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative">

        <h2 class="text-2xl font-bold mb-4 text-gray-800"
            x-text="isEdit ? 'Edit Product' : 'Add Product'"></h2>

        <div class="space-y-4">
            <!-- Supplier -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Supplier</label>
                <select x-model="supplierId"
                        class="w-full px-4 py-2 border rounded-lg text-gray-500 focus:text-gray-900 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                    <option value="" disabled>Select a supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                <input type="text"
                       x-model="productName"
                       @focus="if(isEdit){ productName=''; }"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900"
                       placeholder="">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <input type="text"
                       x-model="description"
                       @focus="if(isEdit){ description=''; }"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900"
                       placeholder="">
            </div>

            <!-- Unit -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Unit</label>
                <input type="text"
                       x-model="unit"
                       @focus="if(isEdit){ unit=''; }"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-500 focus:text-gray-900"
                       placeholder="">
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="closeModal()"
                    class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                Cancel
            </button>

            <button x-show="!isEdit"
                    @click="addProduct()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>

            <button x-show="isEdit"
                    @click="updateProduct()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                Update
            </button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="product-pagination-info"></div>
    <ul id="product-pagination-links" class="pagination-links flex gap-2"></ul>
</div>


<script>
function productPage() {
    return {
        showProductModal: false,
        isEdit: false,
        editingId: null,

        supplierId: '',
        productName: '',
        description: '',
        unit: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.supplierId = '';
            this.productName = '';
            this.description = '';
            this.unit = '';
            this.showProductModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            if (!row) return;

            this.isEdit = true;
            this.editingId = row.dataset.id;

            this.supplierId  = row.dataset.supplier_id || '';
            this.productName = row.dataset.product_name || '';
            this.description = row.dataset.description || '';
            this.unit        = row.dataset.unit || '';

            this.showProductModal = true;
        },

        closeModal() {
            this.showProductModal = false;
        },

        addProduct() {
            if (!this.productName || !this.supplierId || !this.unit) {
                alert('Please fill all required fields.');
                return;
            }

            fetch('{{ route('products.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    supplier_id: this.supplierId,
                    product_name: this.productName,
                    description: this.description,
                    unit: this.unit
                })
            })
            .then(res => res.json())
            .then(data => {
                const tbody    = document.getElementById('product-table-body');
                const emptyRow = document.getElementById('product-empty-row');
                if (!tbody) return;

                const row   = document.createElement('tr');
                row.className = 'hover:bg-gray-50 product-row';
                row.dataset.id = data.product_id;
                row.dataset.supplier_id = data.supplier_id;
                row.dataset.supplier_name = data.supplier_name;
                row.dataset.product_name = data.product_name;
                row.dataset.description = data.description ?? '';
                row.dataset.unit = data.unit;

                row.innerHTML = `
                    <td class='px-4 py-3 text-center font-medium text-gray-800'>
                        P${String(data.product_id).padStart(3,'0')}
                    </td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.supplier_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.product_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.unit}</td>
                    <td class='px-4 py-3 text-center'>
                        <div class='flex items-center justify-center space-x-2'>
                            <button title='Edit'
                                onclick='window.__productOpenEditFromRow(event)'
                                class='p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                    <path d='M12 20h9' />
                                    <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z' />
                                </svg>
                            </button>
                            <button title='Archive' onclick='deleteRow(this)' class='p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='22' height='25' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                    <path d='M3 4h18v4H3z' />
                                    <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8' />
                                    <path d='M10 12h4' />
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                if (emptyRow) tbody.insertBefore(row, emptyRow); else tbody.appendChild(row);

                this.supplierId = '';
                this.productName = '';
                this.description = '';
                this.unit = '';
                this.showProductModal = false;

                updateProductPagination();
                alert('Product added successfully!');
            })
            .catch(console.error);
        },

        updateProduct() {
            if (!this.editingId) return;

            const payload = {};
            if (this.supplierId)  payload.supplier_id  = this.supplierId;
            if (this.productName) payload.product_name = this.productName;
            if (this.description) payload.description  = this.description;
            if (this.unit)        payload.unit         = this.unit;

            if (Object.keys(payload).length === 0) {
                alert('Nothing to update.');
                return;
            }

            fetch('{{ route("products.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(p => {
                const tbody = document.getElementById('product-table-body');
                if (!tbody) return;

                const row = Array.from(tbody.querySelectorAll('tr')).find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.supplier_id   = p.supplier_id;
                row.dataset.supplier_name = p.supplier_name;
                row.dataset.product_name  = p.product_name;
                row.dataset.description   = p.description ?? '';
                row.dataset.unit          = p.unit;

                row.children[0].textContent = 'P' + String(p.product_id).padStart(3,'0');
                row.children[1].textContent = p.supplier_name;
                row.children[2].textContent = p.product_name;
                row.children[3].textContent = p.description ?? '';
                row.children[4].textContent = p.unit;

                this.showProductModal = false;
                updateProductPagination();
                alert('Product updated successfully!');
            })
            .catch(console.error);
        }
    }
}

// helper for dynamically added rows
window.__productOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="productPage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

// pagination + search + delete
const productRowsPerPage   = 5;
const productTableBody     = document.getElementById('product-table-body');
const productEmptyRow      = document.getElementById('product-empty-row');
const productPaginationLinks = document.getElementById('product-pagination-links');
const productPaginationInfo  = document.getElementById('product-pagination-info');
const productSearchInput   = document.getElementById('product-search');

let allProductRows = Array.from(productTableBody.querySelectorAll('.product-row'));
let productCurrentPage = 1;
let visibleRows        = [...allProductRows];

function applyProductSearch() {
    const q = (productSearchInput.value || '').toLowerCase();

    visibleRows = allProductRows.filter(row => {
        if (!q) return true;
        return row.textContent.toLowerCase().includes(q);
    });

    if (visibleRows.length === 0) {
        allProductRows.forEach(r => r.style.display = 'none');
        productEmptyRow.classList.remove('hidden');
        productPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        productPaginationLinks.innerHTML = '';
        return;
    } else {
        productEmptyRow.classList.add('hidden');
    }

    productCurrentPage = 1;
    showProductPage(1);
}

function showProductPage(page) {
    const totalPages = Math.ceil(visibleRows.length / productRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    productCurrentPage = page;

    allProductRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * productRowsPerPage;
    const end   = start + productRowsPerPage;

    visibleRows.slice(start, end).forEach(r => r.style.display = '');

    renderProductPagination(totalPages);

    const startItem = visibleRows.length ? start + 1 : 0;
    const endItem   = end > visibleRows.length ? visibleRows.length : end;
    productPaginationInfo.textContent =
        `Showing ${startItem} to ${endItem} of ${visibleRows.length} results`;
}

function renderProductPagination(totalPages) {
    productPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = productCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (productCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showProductPage(productCurrentPage - 1);
        });
    }
    productPaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' +
                       (i === productCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === productCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== productCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showProductPage(i);
            });
        }
        productPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = productCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (productCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showProductPage(productCurrentPage + 1);
        });
    }
    productPaginationLinks.appendChild(next);
}

function deleteRow(button) {
    const row = button.closest('tr');
    if (!row) return;

    if (!confirm('Are you sure you want to remove this row from the table?')) return;

    row.remove();

    const index = allProductRows.indexOf(row);
    if (index !== -1) allProductRows.splice(index, 1);

    applyProductSearch();
}

function updateProductPagination() {
    allProductRows = Array.from(productTableBody.querySelectorAll('.product-row'));
    applyProductSearch();
}

productSearchInput.addEventListener('input', applyProductSearch);
applyProductSearch();
</script>

@endsection
