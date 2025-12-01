@extends('layouts.app')

@section('title', 'Stock In')

@section('content')

<div class="max-w-7xl mx-auto" x-data="stockInComponent()" x-cloak>

    <!-- Header -->
    <header class="mb-8">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">In Stocks</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage stock-in records</p>
    </header>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Validation Errors -->
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
    <div class="max-w-7xl mx-auto mb-6 flex items-center justify-between gap-4">

        <!-- Left: search and filter -->
        <div class="flex items-center gap-3">

            <!-- Search Input with Icon -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                <input type="text"
                    x-model="searchQuery"
                    @input="filterStockins()"
                    placeholder="Search by Stock ID"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none w-full md:w-80"
                    style="min-width:200px;" />
            </div>

            <!-- Product Type Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Filter:</label>
                <select x-model="typeFilter" @change="filterStockins()"
                        class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                    <option value="all">All Types</option>
                    <option value="Ready Made">Ready Made</option>
                    <option value="Customize Item">Customize Item</option>
                </select>
            </div>

        </div>

        <!-- Right: Add button -->
        <div>
            <button onclick="openStockModal()"
                class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold hover:bg-yellow-500 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Add New Stock</span>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white p-6 rounded-xl shadow">
        <table class="min-w-full table-auto" id="stockInTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock In ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Size</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Quantity</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Inputed By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stocked Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 relative" id="stockInTableBody">
                @forelse($stockins as $si)
                <tr class="hover:bg-gray-50 stock-row"
                    data-type="{{ $si->product_type ?? '' }}"
                    data-search="SI{{ str_pad($si->stockin_id, 3, '0', STR_PAD_LEFT) }} {{ $si->product->product_name ?? '' }} {{ $si->product_type ?? '' }} {{ $si->employee->fname ?? '' }} {{ $si->employee->lname ?? '' }} {{ $si->created_at->format('Y-m-d') }}">
                    <td class="px-4 py-3 text-center text-gray-600 font-medium">
                        SI{{ str_pad($si->stockin_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-2 text-center text-gray-600">
                        {{ $si->product->product_name ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->product_type ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->size ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->quantity_product }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($si->unit_cost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($si->total, 2) }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $si->employee->fname ?? '' }} {{ $si->employee->lname ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <!-- Static empty state when no stock-ins at all -->
                <tr class="empty-state-stock-none">
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-lg font-medium">No stock-in records available</p>
                        <p class="text-sm mt-1">Create a new stock-in to get started</p>
                    </td>
                </tr>
                @endforelse

                @if($stockins->isNotEmpty())
                <!-- Dynamic empty state when search/filter hides all rows -->
                <tr class="empty-state-stock-filter" style="display:none;">
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-lg font-medium">No stock-in records match your filter</p>
                        <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="stockin-pagination-info"></div>
    <ul id="stockin-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<!-- ADD STOCK IN MODAL -->
<div id="addStockModal" 
    class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-0 invisible transition-all duration-300">
    <div id="stockModalBox"
        class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Stock In</h2>

        <form id="addStockForm" method="POST" action="{{ route('instock.store') }}">
            @csrf

            <!-- Product with Add Button -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product</label>
                <div class="flex gap-2">
                    <select id="productSelect" name="product_id" required 
                        class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>

                    <button type="button"
                        onclick="openAddProductModal()"
                        class="px-4 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold flex-shrink-0">
                        Add
                    </button>
                </div>
            </div>

            <!-- Product Type -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product Type</label>
                <select name="product_type" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                    <option value="">Select Type</option>
                    <option value="Ready Made">Ready Made</option>
                    <option value="Customize Item">Customize Item</option>
                </select>
            </div>

            <!-- Size -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Size</label>
                <select name="size" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
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

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Quantity</label>
                <input type="number" name="quantity_product" min="1" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    value="1">
            </div>

            <!-- Unit Cost -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Unit Cost</label>
                <input type="number" name="unit_cost" min="0" step="0.01" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    value="0.00">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeStockModal()"
                    class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ADD PRODUCT MODAL -->
<div id="addProductModal" 
    class="fixed inset-0 z-[60] items-center justify-center bg-black bg-opacity-0 invisible transition-all duration-300">
    <div id="addProductModalBox"
        class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Product</h2>

        <form id="addProductForm">
            @csrf

            <!-- Product Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                <input type="text" name="product_name" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"></textarea>
            </div>

            <!-- Unit -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Unit</label>
                <input type="text" name="unit" placeholder="e.g., pcs, kg, box, liter" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeAddProductModal()"
                    class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold">
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    // Alpine component for search + filter + empty state
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
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const emptyFilterRow = document.querySelector('.empty-state-stock-filter');
                if (emptyFilterRow) {
                    emptyFilterRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }

                // Re-paginate after filtering
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

    // ==================== AJAX FORM SUBMISSION FOR STOCK IN ====================
    document.addEventListener('DOMContentLoaded', function() {
        const addStockForm = document.getElementById('addStockForm');
        
        addStockForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            fetch('{{ route("instock.store") }}', {
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
    });

    // ==================== AJAX FORM SUBMISSION FOR ADD PRODUCT ====================
    document.addEventListener('DOMContentLoaded', function() {
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

    // ==================== PAGINATION ====================
    const stockinRowsPerPage = 5;
    const stockinTableBody = document.getElementById('stockInTableBody');
    const stockinRows = Array.from(stockinTableBody.querySelectorAll('.stock-row'));
    const stockinPaginationLinks = document.getElementById('stockin-pagination-links');
    const stockinPaginationInfo = document.getElementById('stockin-pagination-info');

    let stockinCurrentPage = 1;
    const stockinTotalPages = Math.ceil(stockinRows.length / stockinRowsPerPage);

    function showStockinPage(page) {
        stockinCurrentPage = page;
        stockinRows.forEach(row => row.style.display = 'none');
        const start = (page - 1) * stockinRowsPerPage;
        const end = start + stockinRowsPerPage;
        stockinRows.slice(start, end).forEach(row => row.style.display = '');
        renderStockinPagination();
        const startItem = stockinRows.length ? start + 1 : 0;
        const endItem = end > stockinRows.length ? stockinRows.length : end;
        stockinPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${stockinRows.length} results`;
    }

    function renderStockinPagination() {
        stockinPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = stockinCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
        if (stockinCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showStockinPage(stockinCurrentPage - 1); });
        stockinPaginationLinks.appendChild(prev);

        for (let i = 1; i <= stockinTotalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === stockinCurrentPage ? ' bg-sky-400 text-white' : '');
            li.innerHTML = i === stockinCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== stockinCurrentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showStockinPage(i); });
            stockinPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = stockinCurrentPage === stockinTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
        if (stockinCurrentPage !== stockinTotalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showStockinPage(stockinCurrentPage + 1); });
        stockinPaginationLinks.appendChild(next);
    }

    showStockinPage(1);
</script>

@endsection
