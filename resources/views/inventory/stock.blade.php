@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div x-data="{
        showAddDelivery: false,
        successMessage: '',
        // selected stock row data
        selectedSupplierId: '',
        selectedSupplierName: '',
        selectedProductId: '',
        selectedProductName: '',
        selectedProductType: '',
        selectedSize: '',
        openAddDelivery(stock) {
            this.selectedSupplierId   = stock.supplier_id;
            this.selectedSupplierName = stock.supplier_name;
            this.selectedProductId    = stock.product_id;
            this.selectedProductName  = stock.product_name;
            this.selectedProductType  = stock.product_type;
            this.selectedSize         = stock.size;
            this.showAddDelivery = true;
        },
        closeAddDelivery() { this.showAddDelivery = false },
        saveDelivery() {
            this.showAddDelivery = false;
            this.successMessage = 'Delivery and delivery details added successfully.';
            setTimeout(() => this.successMessage = '', 4000);
        }
    }"
>
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Stocks</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage inventory records, stock levels, and product details.</p>
</header>

<!-- Success message -->
<div x-show="successMessage"
     x-transition
     class="max-w-7xl mx-auto mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    <span x-text="successMessage"></span>
</div>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Left: search + filters -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full">

            <!-- Search -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>

                <input type="text" id="inventory-search" placeholder="Search inventory"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm
                              focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <!-- Product Type Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Product Type:</label>
                <select id="inventory-type-filter"
                        class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                    <option value="all">All Types</option>
                    @foreach($groupedStocks->pluck('product_type')->unique() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Level Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Stock Level:</label>
                <select id="inventory-level-filter"
                        class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                    <option value="all">All Levels</option>
                    <option value="out">Out of Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="medium">Medium Stock</option>
                    <option value="high">High Stock</option>
                </select>
            </div>

        </div>
    </div>
</div>

<!-- Inventory Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="inventory-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Available Size</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Total Stock</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Available Stock</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock Level</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="inventory-table-body" class="divide-y divide-gray-100">
                @foreach ($groupedStocks as $item)
                    @php
                        if ($item->current_stock <= 0) {
                            $stockColor = 'bg-gray-400';
                            $stockText  = 'Out of Stock';
                            $stockLevel = 'out';
                        } elseif ($item->current_stock <= 30) {
                            $stockColor = 'bg-red-500';
                            $stockText  = 'Low Stock';
                            $stockLevel = 'low';
                        } elseif ($item->current_stock <= 50) {
                            $stockColor = 'bg-yellow-500';
                            $stockText  = 'Medium Stock';
                            $stockLevel = 'medium';
                        } else {
                            $stockColor = 'bg-green-500';
                            $stockText  = 'High Stock';
                            $stockLevel = 'high';
                        }
                    @endphp

                    <tr class="hover:bg-gray-50 inventory-row"
                        data-product-type="{{ $item->product_type }}"
                        data-stock-level="{{ $stockLevel }}"
                        data-supplier-name="{{ $item->product->supplier->supplier_name ?? '' }}"
                        data-product-name="{{ $item->product->product_name }}">
                        <td class="px-4 py-3 text-center text-gray-800 font-medium">
                            {{ $item->product->product_name }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-800 font-medium">
                            {{ $item->product_type }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-800 font-medium">
                            {{ $item->sizes }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            {{ $item->total_stock }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            {{ $item->current_stock }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center items-center space-x-2">
                                <span class="w-3 h-3 rounded-full {{ $stockColor }}"></span>
                                <span class="text-gray-800 text-xs font-semibold">{{ $stockText }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                           <button 
                                type="button"
                                title="Re Order"
                                @click="openAddDelivery({
                                    supplier_id: {{ $item->supplier_id ?? 'null' }},
                                    supplier_name: '{{ str_replace("'", "\\'", $item->supplier_name ?? '') }}',
                                    product_id: {{ $item->product_id ?? 'null' }},
                                    product_name: '{{ str_replace("'", "\\'", $item->product_name ?? '') }}',
                                    product_type: '{{ str_replace("'", "\\'", $item->product_type ?? '') }}',
                                    size: '{{ str_replace("'", "\\'", $item->sizes ?? '') }}'
                                }); console.log('Clicked:', {{ $item->supplier_id }}, '{{ $item->supplier_name }}', {{ $item->product_id }}, '{{ $item->product_name }}', '{{ $item->product_type }}', '{{ $item->sizes }}')"
                                class="p-2 rounded-full text-blue-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="26" height="26" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 11a8 8 0 0 0-14.31-4.9" />
                                    <polyline points="6 4 6 9 11 9" />
                                    <path d="M4 13a8 8 0 0 0 14.31 4.9" />
                                    <polyline points="18 20 18 15 13 15" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach

                {{-- Empty state row --}}
                <tr id="inventory-empty-row" class="{{ $groupedStocks->count() ? 'hidden' : '' }}">
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
                                No inventory records found
                            </p>
                            <p class="text-gray-400 text-xs">
                                There are currently no stocks matching these filters.
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="inventory-pagination-info"></div>
    <ul id="inventory-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<!-- Add Delivery Modal -->
<div x-show="showAddDelivery" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     @click.self="closeAddDelivery()">

    <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl relative
                overflow-y-auto max-h-[calc(100vh-3rem)] p-6"
         x-data="{
            item: {
                product_id: 0,
                quantity_product: 1,
                unit: 'pcs',
                unit_cost: 0,
                total: 0,
                size: '',
                product_type: ''
            },
            init() {
                // Watch for changes in parent component
                this.$watch('$root.selectedProductId', (value) => {
                    this.item.product_id = value;
                });
                this.$watch('$root.selectedSize', (value) => {
                    this.item.size = value;
                });
                this.$watch('$root.selectedProductType', (value) => {
                    this.item.product_type = value;
                });
            },
            updateTotal() {
                this.item.total = (this.item.quantity_product * this.item.unit_cost).toFixed(2);
            },
            grandTotal() {
                return Number(this.item.total || 0).toFixed(2);
            }
         }"
    >
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex justify-between items-center">
            <span>Restock Selected Product</span>
        </h2>

      <form id="add-delivery-form" method="POST" action="{{ route('deliveries.store') }}">
            @csrf

            <!-- Hidden IDs - FIXED to access parent scope -->
            <input type="hidden" name="supplier_id" :value="selectedSupplierId">
            <input type="hidden" name="products[0][product_id]" :value="selectedProductId">
            <input type="hidden" name="products[0][product_type]" :value="selectedProductType">
            <input type="hidden" name="products[0][size]" :value="selectedSize">

            <!-- Supplier (readonly) -->
            <div class="mb-3">
                <label class="block text-gray-700 font-medium mb-1">Supplier</label>
                <input type="text" readonly
                    class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                    :value="selectedSupplierName">
            </div>

            <!-- Product info (readonly) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Product</label>
                    <input type="text" readonly
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                        :value="selectedProductName">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Product Type</label>
                    <input type="text" readonly
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                        :value="selectedProductType">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Size</label>
                    <input type="text" readonly
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                        :value="selectedSize">
                </div>
            </div>

            <!-- Date Requested -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Date Requested</label>
                <input type="date" name="delivery_date_request" required
                    value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
            </div>

            <!-- Restock fields -->
            <div class="mb-4 overflow-x-auto">
                <label class="block text-gray-700 font-medium mb-2">Restock Details</label>
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 border w-20">Qty</th>
                        <th class="px-4 py-2 border w-32">Cost</th>
                        <th class="px-4 py-2 border w-24">Unit</th>
                        <th class="px-4 py-2 border w-32">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <!-- Qty -->
                        <td class="px-2 py-2 border">
                            <input type="number" min="1"
                                name="products[0][quantity_product]"
                                x-model="item.quantity_product"
                                @input="updateTotal()"
                                required
                                class="w-full px-2 py-1 border rounded text-right">
                        </td>
                        <!-- Cost -->
                        <td class="px-2 py-2 border">
                            <input type="number" min="0" step="0.01"
                                name="products[0][unit_cost]"
                                x-model="item.unit_cost"
                                @input="updateTotal()"
                                required
                                class="w-full px-2 py-1 border rounded text-right">
                        </td>
                        <!-- Unit -->
                        <td class="px-2 py-2 border">
                            <input type="text" readonly
                                name="products[0][unit]"
                                x-model="item.unit"
                                class="w-full px-2 py-1 border rounded bg-gray-100 text-center text-sm">
                        </td>
                        <!-- Total -->
                        <td class="px-2 py-2 border">
                            <input type="number" readonly
                                name="products[0][total]"
                                x-model="item.total"
                                class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">
                <div class="text-lg font-semibold">
                    Requested by: {{ auth()->user()->employee->fname ?? '-' }} {{ auth()->user()->employee->lname ?? '-' }}
                </div>
                <div class="text-xl font-bold text-right">
                    Grand Total: ₱ <span x-text="grandTotal()"></span>
                    <input type="hidden" name="total_amount" :value="grandTotal()">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-wrap justify-end gap-2 mt-4">
                <button type="button" @click="closeAddDelivery()"
                        class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-yellow-500 text-black px-5 py-2 rounded-lg text-lg font-semibold hover:bg-yellow-600 transition">
                    Save Delivery
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const rowsPerPage     = 5;
const tableBody       = document.getElementById('inventory-table-body');
const allRows         = Array.from(tableBody.querySelectorAll('.inventory-row'));
const emptyRow        = document.getElementById('inventory-empty-row');
const paginationLinks = document.getElementById('inventory-pagination-links');
const paginationInfo  = document.getElementById('inventory-pagination-info');

const searchInput = document.getElementById('inventory-search');
const typeFilter  = document.getElementById('inventory-type-filter');
const levelFilter = document.getElementById('inventory-level-filter');

let currentPage  = 1;
let filteredRows = [...allRows];

const stockLevelOrder = { out: 1, low: 2, medium: 3, high: 4 };

function sortRowsByStockLevel(rows) {
    return rows.sort((a, b) => {
        const levelA = a.getAttribute('data-stock-level') || '';
        const levelB = b.getAttribute('data-stock-level') || '';
        const orderA = stockLevelOrder[levelA] || 99;
        const orderB = stockLevelOrder[levelB] || 99;
        return orderA - orderB;
    });
}

function applyFilters() {
    const q     = (searchInput.value || '').toLowerCase();
    const type  = typeFilter ? typeFilter.value : 'all';
    const level = levelFilter ? levelFilter.value : 'all';

    filteredRows = allRows.filter(row => {
        const rowType  = (row.getAttribute('data-product-type') || '').trim();
        const rowLevel = row.getAttribute('data-stock-level') || '';

        if (type !== 'all' && rowType !== type) return false;
        if (level !== 'all' && rowLevel !== level) return false;

        if (q) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(q)) return false;
        }
        return true;
    });

    filteredRows = sortRowsByStockLevel(filteredRows);

    if (filteredRows.length === 0) {
        allRows.forEach(r => r.style.display = 'none');
        emptyRow.classList.remove('hidden');
        paginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        paginationLinks.innerHTML = '';
        return;
    } else {
        emptyRow.classList.add('hidden');
    }

    filteredRows.forEach(row => tableBody.appendChild(row));

    allRows.forEach(r => r.style.display = 'none');
    filteredRows.forEach(r => r.style.display = '');

    currentPage = 1;
    showPage(1);
}

function showPage(page) {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    currentPage = page;

    const start = (page - 1) * rowsPerPage;
    const end   = start + rowsPerPage;

    allRows.forEach(r => r.style.display = 'none');
    filteredRows.slice(start, end).forEach(r => r.style.display = '');

    renderPagination(totalPages);

    const startItem = filteredRows.length ? start + 1 : 0;
    const endItem   = end > filteredRows.length ? filteredRows.length : end;
    paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${filteredRows.length} results`;
}

function renderPagination(totalPages) {
    paginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = currentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (currentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showPage(currentPage - 1);
        });
    }
    paginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === currentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === currentPage ? i : `<a href="#">${i}</a>`;
        if (i !== currentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showPage(i);
            });
        }
        paginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = currentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (currentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showPage(currentPage + 1);
        });
    }
    paginationLinks.appendChild(next);
}

searchInput.addEventListener('input', applyFilters);
if (typeFilter)  typeFilter.addEventListener('change', applyFilters);
if (levelFilter) levelFilter.addEventListener('change', applyFilters);

applyFilters();
</script>
</div>
@endsection
