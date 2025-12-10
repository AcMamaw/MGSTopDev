@extends('layouts.app')

@section('title', 'Stock In')

@section('content')

<div class="w-full px-4 md:px-6 lg:px-8" x-data="stockInComponent()" x-cloak>

    <header class="mb-8 max-w-full mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="h-7 w-7 text-yellow-500">
                    <path d="M12 2L2 7l10 5 10-5-10-5z" />
                    <path d="M2 17l10 5 10-5" />
                    <path d="M2 12l10 5 10-5" />
                </svg>
                Stock In 
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Manage records of incoming inventory and goods received.</p>
    </header>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-300 text-green-800 px-5 py-3 rounded-xl shadow-sm transition-opacity duration-300 max-w-full mx-auto">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-300 text-red-800 px-5 py-3 rounded-xl shadow-sm transition-opacity duration-300 max-w-full mx-auto">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 max-w-full mx-auto">
        {{-- Left: search + filter --}}
        <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full sm:w-80">
                <input type="text"
                    x-model="searchQuery"
                    @input="filterStockins()"
                    placeholder="Search by stock"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            {{-- Type filter --}}
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-700 hidden sm:block">
                    Filter by Type:
                </label>
                <select x-model="typeFilter"
                        @change="filterStockins()"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
                    <option value="all">All Types</option>
                    <option value="Ready Made">Ready Made</option>
                    <option value="Customize Item">Customize Item</option>
                </select>
            </div>
        </div>

        {{-- Right: Add New Stock button --}}
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button type="button"
                    onclick="openStockModal()"
                    class="px-5 py-2 bg-yellow-400 text-black rounded-full hover:bg-yellow-500 font-semibold text-base transition shadow-md inline-flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Add New Stock</span>
            </button>
        </div>
    </div>


    <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-full mx-auto overflow-x-auto border-t-4 border-yellow-400">
        <table class="min-w-full table-auto divide-y divide-gray-200" id="stockInTable">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Stock In ID</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Product Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Size</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Quantity</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Inputed By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Stocked Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 relative" id="stockInTableBody">
                @forelse($stockins as $si)
                <tr class="hover:bg-yellow-50/50 stock-row transition-colors"
                    data-type="{{ $si->product_type ?? '' }}"
                    data-search="SI{{ str_pad($si->stockin_id, 3, '0', STR_PAD_LEFT) }} {{ $si->product->product_name ?? '' }} {{ $si->product_type ?? '' }} {{ $si->employee->fname ?? '' }} {{ $si->employee->lname ?? '' }} {{ $si->created_at->format('Y-m-d') }}">
                    <td class="px-4 py-3 text-center text-sm font-semibold text-black-700">
                        SI{{ str_pad($si->stockin_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-2 text-left text-sm text-gray-800">
                        {{ $si->product->product_name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-700">
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $si->product_type == 'Ready Made' ? 'bg-black-100 text-black-800' : 'bg-purple-100 text-black-800' }}">
                            {{ $si->product_type ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $si->size ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-700 font-medium">{{ $si->quantity_product }}</td>
                    <td class="px-4 py-3 text-right text-sm text-gray-700">₱{{ number_format($si->unit_cost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">₱{{ number_format($si->total, 2) }}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-700">
                        {{ $si->employee->fname ?? '-' }} {{ $si->employee->lname ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $si->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr class="empty-state-stock-none">
                    <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-lg font-bold text-gray-700">No Stock-In Records Available</p>
                        <p class="text-sm mt-1 text-gray-500">Click "Add New Stock" to create your first record.</p>
                    </td>
                </tr>
                @endforelse

                @if($stockins->isNotEmpty())
                <tr class="empty-state-stock-filter" style="display:none;">
                    <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-lg font-bold text-gray-700">No Results Match Your Criteria</p>
                        <p class="text-sm mt-1 text-gray-500">Try adjusting your search or filter settings.</p>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</div>

<div class="custom-pagination mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600 max-w-full mx-auto px-4 md:px-6 lg:px-8">
    <div id="stockin-pagination-info" class="mb-2 sm:mb-0"></div>
    <ul id="stockin-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<div id="addStockModal" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 invisible transition-all duration-300 p-4">
    <div id="stockModalBox"
        class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-extrabold mb-6 text-gray-800 border-b pb-3 flex items-center gap-2">
             Log New Stock In
        </h2>

        <form id="addStockForm" method="POST" action="{{ route('instock.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Product</label>
                <div class="flex gap-3">
                    <select id="productSelect" name="product_id" required 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>

                    <button type="button"
                        onclick="openAddProductModal()"
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                        New Product
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Product Type</label>
                <select name="product_type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
                    <option value="">Select Type</option>
                    <option value="Ready Made">Ready Made</option>
                    <option value="Customize Item">Customize Item</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Size</label>
                <select name="size" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
                    <option value="">Select Size</option>
                    <option value="Extra Small">Extra Small</option>
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large">Large</option>
                    <option value="Extra Large">Extra Large</option>
                    <option value="Double XL">Double XL</option>
                    <option value="Triple XL">Triple XL</option>
                    <option value="28">28</option>
                    <option value="30">30</option>
                    <option value="32">32</option>
                    <option value="34">34</option>
                    <option value="36">36</option>
                    <option value="38">38</option>
                    <option value="40">40</option>
                    <option value="42">42</option>
                    <option value="One Size">One Size</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
                <input type="number" name="quantity_product" min="1" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm"
                    value="1">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Unit Cost (₱)</label>
                <input type="number" name="unit_cost" min="0" step="0.01" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm"
                    value="0.00">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeStockModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition shadow-sm">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-gray-900 rounded-full hover:bg-yellow-500 font-bold transition shadow-md">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<div id="addProductModal" 
    class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-70 invisible transition-all duration-300 p-4">
    <div id="addProductModalBox"
        class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-extrabold mb-6 text-gray-800 border-b pb-3 flex items-center gap-2">
             Add New Product
        </h2>

        <form id="addProductForm">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Product Name</label>
                <input type="text" name="product_name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Unit</label>
                <input type="text" name="unit" placeholder="e.g., pcs, kg, box, liter" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Category</label>
                <select name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">
                    Markup Rule (%) 
                    <span class="text-xs font-normal text-gray-500">(e.g. 30 = 30% above cost)</span>
                </label>
                <input type="number" name="markup_rule" step="0.01" min="0" value="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none text-sm">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddProductModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition shadow-sm">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-gray-900 rounded-full hover:bg-yellow-500 font-bold transition shadow-md">
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Scripts -->
<script>
    // ==================== ALPINE COMPONENT ====================
    function stockInComponent() {
        return {
            searchQuery: '',
            typeFilter: 'all',

            filterStockins() {
                const rows = document.querySelectorAll('.stock-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const type = (row.getAttribute('data-type') || '').trim();
                    const searchText = row.getAttribute('data-search').toLowerCase();
                    const q = this.searchQuery.toLowerCase();

                    const matchesType = this.typeFilter === 'all' || type === this.typeFilter;
                    const matchesSearch = !q || searchText.includes(q);

                    if (matchesType && matchesSearch) {
                        row.classList.remove('filtered-out');
                        visibleCount++;
                    } else {
                        row.classList.add('filtered-out');
                    }
                });

                const emptyFilterRow = document.querySelector('.empty-state-stock-filter');
                if (emptyFilterRow) {
                    emptyFilterRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }

                // Re-initialize pagination with filtered rows
                updateStockinPagination();
                showStockinPage(1);
            }
        }
    }

    // ==================== STOCK IN MODAL ====================
    function openStockModal() {
        const modal = document.getElementById('addStockModal');
        const box = document.getElementById('stockModalBox');

        modal.classList.remove('invisible');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            box.classList.remove('scale-90', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeStockModal() {
        const modal = document.getElementById('addStockModal');
        const box = document.getElementById('stockModalBox');

        modal.classList.add('bg-opacity-0');
        modal.classList.remove('bg-opacity-50');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-90', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('invisible');
            modal.classList.remove('flex');
        }, 300);
    }

    // ==================== ADD PRODUCT MODAL ====================
    function openAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const box = document.getElementById('addProductModalBox');

        modal.classList.remove('invisible');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            box.classList.remove('scale-90', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const box = document.getElementById('addProductModalBox');

        modal.classList.add('bg-opacity-0');
        modal.classList.remove('bg-opacity-50');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-90', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('invisible');
            modal.classList.remove('flex');
        }, 300);
    }

    // ==================== SUCCESS MESSAGE ====================
    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-[70] transform translate-x-0 transition-transform duration-300';
        successDiv.textContent = message;
        
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            successDiv.classList.add('translate-x-0');
        }, 10);
        
        setTimeout(() => {
            successDiv.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(successDiv);
            }, 300);
        }, 3000);
    }

    // ==================== PAGINATION (UPDATED) ====================
    const stockinRowsPerPage = 5;
    let stockinCurrentPage = 1;
    let visibleStockinRows = [];

    function updateStockinPagination() {
        const stockinTableBody = document.getElementById('stockInTableBody');
        const allRows = Array.from(stockinTableBody.querySelectorAll('.stock-row'));
        
        // Get only visible rows (not filtered out)
        visibleStockinRows = allRows.filter(row => !row.classList.contains('filtered-out'));
    }

    function showStockinPage(page) {
        const stockinTotalPages = Math.ceil(visibleStockinRows.length / stockinRowsPerPage);
        
        if (page < 1) page = 1;
        if (page > stockinTotalPages && stockinTotalPages > 0) page = stockinTotalPages;
        
        stockinCurrentPage = page;
        
        // Hide all stock rows first
        document.querySelectorAll('.stock-row').forEach(row => row.style.display = 'none');
        
        // Show only the visible rows for current page
        const start = (page - 1) * stockinRowsPerPage;
        const end = start + stockinRowsPerPage;
        visibleStockinRows.slice(start, end).forEach(row => row.style.display = '');
        
        renderStockinPagination();
        
        // Update pagination info
        const stockinPaginationInfo = document.getElementById('stockin-pagination-info');
        const startItem = visibleStockinRows.length ? start + 1 : 0;
        const endItem = end > visibleStockinRows.length ? visibleStockinRows.length : end;
        stockinPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${visibleStockinRows.length} results`;
    }

    function renderStockinPagination() {
        const stockinPaginationLinks = document.getElementById('stockin-pagination-links');
        const stockinTotalPages = Math.ceil(visibleStockinRows.length / stockinRowsPerPage);
        
        stockinPaginationLinks.innerHTML = '';

        // Previous button
        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = stockinCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
        if (stockinCurrentPage !== 1) {
            prev.querySelector('a').addEventListener('click', e => { 
                e.preventDefault(); 
                showStockinPage(stockinCurrentPage - 1); 
            });
        }
        stockinPaginationLinks.appendChild(prev);

        // Page numbers
        for (let i = 1; i <= stockinTotalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === stockinCurrentPage ? ' bg-yellow-400 text-black' : '');
            li.innerHTML = i === stockinCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== stockinCurrentPage) {
                li.querySelector('a').addEventListener('click', e => { 
                    e.preventDefault(); 
                    showStockinPage(i); 
                });
            }
            stockinPaginationLinks.appendChild(li);
        }

        // Next button
        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = stockinCurrentPage === stockinTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
        if (stockinCurrentPage !== stockinTotalPages) {
            next.querySelector('a').addEventListener('click', e => { 
                e.preventDefault(); 
                showStockinPage(stockinCurrentPage + 1); 
            });
        }
        stockinPaginationLinks.appendChild(next);
    }

    // ==================== AJAX FORM SUBMISSION FOR STOCK IN ====================
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize pagination
        updateStockinPagination();
        showStockinPage(1);

        // Stock In Form
        const addStockForm = document.getElementById('addStockForm');
        
        addStockForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeStockModal();
                    addStockForm.reset();
                    showSuccessMessage(data.message || 'Stock-in added successfully!');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error adding stock-in. Please try again.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });

        // Add Product Form
        const addProductForm = document.getElementById('addProductForm');
        
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Adding...';

            fetch('{{ route("instock.storeProduct") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const productSelect = document.getElementById('productSelect');
                    const newOption = new Option(data.product_name, data.product_id, true, true);
                    productSelect.add(newOption);
                    
                    closeAddProductModal();
                    addProductForm.reset();
                    showSuccessMessage('Product added successfully!');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error adding product. Please try again.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    });
</script>

@endsection