@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Stock Adjustments</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage stock adjustment records including quantity changes, reasons, and approval status.</p>
</header>

<!-- Success Message -->
@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto">
    {{ session('success') }}
</div>
@endif

<!-- Validation Errors -->
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
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="stockadjustment-search" placeholder="Search stock adjustments"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

         <!-- Add StockAdjustment -->
        <button onclick="openAdjustmentModal()"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Adjust Stocks</span>
        </button>
    </div>
</div>

<!-- Stock Adjustment Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="stockadjustment-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjustment ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjustment Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Quantity Adjusted</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Request Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Reason</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjusted By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Approved By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody id="stockadjustment-table-body" class="divide-y divide-gray-100">
               @foreach($adjustments as $sa)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        SA{{ str_pad($sa->stockadjustment_id,3,'0',STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $sa->stock->product->product_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $sa->adjustment_type }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $sa->quantity_adjusted }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $sa->request_date }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $sa->reason }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $sa->employee->fname ?? '' }} {{ $sa->employee->lname ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $sa->status === 'Approved' ? ($sa->approvedBy->fname ?? '') . ' ' . ($sa->approvedBy->lname ?? '') : '—' }}
                    </td>
                  <td class="px-4 py-3">
                    @php
                        switch($sa->status) {
                            case 'Pending':
                                $statusColor = 'bg-gray-500';
                                $statusText  = 'Pending';
                                break;
                            case 'Approved':
                                $statusColor = 'bg-green-500';
                                $statusText  = 'Approved';
                                break;
                            case 'Rejected':
                                $statusColor = 'bg-red-500';
                                $statusText  = 'Rejected';
                                break;
                            default:
                                $statusColor = 'bg-gray-500';
                                $statusText  = $sa->status;
                        }
                    @endphp

                    <div class="flex justify-center items-center space-x-2">
                        <!-- Status dot -->
                        <span class="w-3 h-3 rounded-full {{ $statusColor }}"></span>
                        <span class="text-gray-800 text-xs font-semibold">{{ $statusText }}</span>
                    </div>
                </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="stockadjustment-pagination-info">Showing 1 to 1 of {{ count($adjustments) }} results</div>
    <ul id="stockadjustment-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<!-- ADD STOCK ADJUSTMENT MODAL -->
<div id="adjustmentModal" 
    class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-0 invisible transition-all duration-300">

    <div id="adjustmentModalBox"
        class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-lg transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Adjust Stock</h2>

        <form id="adjustmentForm" method="POST" action="{{ route('stockadjustment.store') }}">
            @csrf

            <!-- Stock Selection -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Select Product Stock</label>
                <select name="stock_id" id="stockSelect" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    onchange="updateCurrentStock()">
                    <option value="">Select Stock</option>
                    @foreach($stocks as $stock)
                    <option value="{{ $stock->stock_id }}" 
                            data-current="{{ $stock->current_stock }}"
                            data-product="{{ $stock->product->product_name ?? 'Unknown' }}"
                            data-size="{{ $stock->size ?? 'N/A' }}">
                        {{ $stock->product->product_name ?? 'Unknown' }} 
                        (Size: {{ $stock->size ?? 'N/A' }}) - 
                        Current: {{ $stock->current_stock }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Current Stock Display -->
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Current Stock:</span> 
                    <span id="currentStockDisplay" class="text-blue-600 font-bold">0</span>
                </p>
            </div>

            <!-- Adjustment Type -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Adjustment Type</label>
                <select name="adjustment_type" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                    <option value="">Select Type</option>
                    <option value="Addition">Addition (+)</option>
                    <option value="Deduction">Deduction (-)</option>
                    <option value="Correction">Correction</option>
                </select>
            </div>

            <!-- Quantity Adjusted -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Quantity to Adjust</label>
                <input type="number" name="quantity_adjusted" min="1" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    placeholder="Enter quantity">
            </div>

            <!-- Reason -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Reason</label>
                <textarea name="reason" rows="3" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    placeholder="Explain the reason for this adjustment"></textarea>
            </div>

            <!-- Request Date (auto-filled) -->
            <input type="hidden" name="request_date" value="{{ date('Y-m-d') }}">

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeAdjustmentModal()"
                    class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold">
                    Submit Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ==================== MODAL FUNCTIONS ====================
function openAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    const box = document.getElementById('adjustmentModalBox');

    modal.classList.remove('invisible');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        box.classList.remove('scale-90', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    const box = document.getElementById('adjustmentModalBox');

    modal.classList.add('bg-opacity-0');
    modal.classList.remove('bg-opacity-50');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-90', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('invisible');
        modal.classList.remove('flex');
    }, 300);
}

// Update current stock display
function updateCurrentStock() {
    const select = document.getElementById('stockSelect');
    const selectedOption = select.options[select.selectedIndex];
    const currentStock = selectedOption.getAttribute('data-current') || 0;
    document.getElementById('currentStockDisplay').textContent = currentStock;
}

// ==================== AJAX FORM SUBMISSION ====================
document.addEventListener('DOMContentLoaded', function() {
    const adjustmentForm = document.getElementById('adjustmentForm');
    
    adjustmentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';

        fetch('{{ route("stockadjustment.store") }}', {
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
                closeAdjustmentModal();
                adjustmentForm.reset();
                document.getElementById('currentStockDisplay').textContent = '0';
                showSuccessMessage(data.message || 'Stock adjustment submitted successfully!');
                
                // Reload the page to show the new adjustment
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error submitting adjustment. Please try again.');
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
const saRowsPerPage = 5;
const saTableBody = document.getElementById('stockadjustment-table-body');
const saRows = Array.from(saTableBody.querySelectorAll('tr'));
const saPaginationLinks = document.getElementById('stockadjustment-pagination-links');
const saPaginationInfo = document.getElementById('stockadjustment-pagination-info');

let saCurrentPage = 1;
const saTotalPages = Math.ceil(saRows.length / saRowsPerPage);

function showSAPage(page) {
    saCurrentPage = page;
    saRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * saRowsPerPage;
    const end = start + saRowsPerPage;
    saRows.slice(start, end).forEach(row => row.style.display = '');

    renderSAPagination();

    const startItem = saRows.length ? start + 1 : 0;
    const endItem = end > saRows.length ? saRows.length : end;
    saPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${saRows.length} results`;
}

function renderSAPagination() {
    saPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = saCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (saCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showSAPage(saCurrentPage - 1); });
    saPaginationLinks.appendChild(prev);

    for (let i = 1; i <= saTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === saCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === saCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== saCurrentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showSAPage(i); });
        saPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = saCurrentPage === saTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (saCurrentPage !== saTotalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showSAPage(saCurrentPage + 1); });
    saPaginationLinks.appendChild(next);
}

// Initialize
showSAPage(1);

// ==================== SEARCH ====================
document.getElementById('stockadjustment-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    saRows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const match = cells.some(cell => cell.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endsection