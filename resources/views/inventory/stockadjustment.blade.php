@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div x-data="adjustmentManager()" x-cloak>

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
               <svg xmlns="http://www.w3.org/2000/svg" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    class="h-8 w-8 text-yellow-500">
                    <line x1="4" x2="20" y1="6" y2="6" />
                    <circle cx="12" cy="6" r="2" />
                    
                    <line x1="4" x2="20" y1="12" y2="12" />
                    <circle cx="17" cy="12" r="2" />
                    
                    <line x1="4" x2="20" y1="18" y2="18" />
                    <circle cx="7" cy="18" r="2" />
                </svg>
                Stock Adjustments
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Manage stock adjustment records including quantity changes, reasons, and approval status.</p>
    </header>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto" x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 5000)">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-7xl mx-auto" x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 10000)">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full">

            <div class="relative w-full sm:w-64">
                <input type="text" 
                    id="stockadjustment-search" 
                    placeholder="Search adjustments"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none transition">

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <label for="sa-type-filter" class="text-sm font-semibold text-gray-700 hidden sm:block">Type:</label>
                <select id="sa-type-filter"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
                    <option value="all">All Types</option>
                    <option value="Addition">Addition</option>
                    <option value="Deduction">Deduction</option>
                    <option value="Correction">Correction</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="sa-status-filter" class="text-sm font-semibold text-gray-700 hidden sm:block">Status:</label>
                <select id="sa-status-filter"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="openModal()"
                class="px-5 py-2 bg-yellow-400 text-black rounded-full hover:bg-yellow-500 font-semibold text-base transition shadow-md inline-flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-plus flex-shrink-0">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Adjust Stock</span>
            </button>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="stockadjustment-table" class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjustment ID</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Quantity Adjusted</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Request Date</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Reason</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjusted By</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody id="stockadjustment-table-body" class="divide-y divide-gray-100">
                   @foreach($adjustments as $sa)
                    <tr class="hover:bg-gray-50 sa-row"
                        data-type="{{ $sa->adjustment_type }}"
                        data-status="{{ $sa->status }}">
                        <td class="px-4 py-3 text-center text-black-800 font-medium">
                            SA{{ str_pad($sa->stockadjustment_id,3,'0',STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 font-medium">
                            {{ $sa->stock->product->product_name ?? '—' }} ({{ $sa->stock->size ?? 'N/A' }})
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 font-medium">
                            <span class="font-bold {{ $sa->adjustment_type === 'Addition' ? 'text-black-600' : ($sa->adjustment_type === 'Deduction' ? 'text-black-600' : 'text-black-600') }}">
                                {{ $sa->adjustment_type }}
                            </span>
                        </td>
                       <td class="px-4 py-3 text-center text-gray-600 font-bold">
                            @if($sa->adjustment_type === 'Addition')
                                +{{ $sa->quantity_adjusted }}
                            @elseif($sa->adjustment_type === 'Deduction')
                                −{{ $sa->quantity_adjusted }}
                            @else
                                {{ $sa->quantity_adjusted }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $sa->request_date }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate" title="{{ $sa->reason }}">{{ Str::limit($sa->reason, 30) }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $sa->employee->fname ?? '' }} {{ $sa->employee->lname ?? '' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                switch($sa->status) {
                                    case 'Pending':
                                        $statusColor = 'bg-yellow-500';
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
                                <span class="w-3 h-3 rounded-full {{ $statusColor }}"></span>
                                <span class="text-gray-800 text-xs font-semibold">{{ $statusText }}</span>
                            </div>
                        </td>
                    </tr>
                   @endforeach

                  {{-- Empty state row --}}
                    <tr id="sa-empty-row" class="{{ $adjustments->count() ? 'hidden' : '' }}">
                        <td colspan="9" class="px-4 py-10 text-center text-gray-500 text-sm">
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
                                    No stock adjustments found
                                </p>
                                <p class="text-gray-400 text-xs">
                                    There are currently no adjustments matching these filters.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto pb-8">
        <div id="stockadjustment-pagination-info"></div>
        <ul id="stockadjustment-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>

    {{-- MODAL --}}
    <div x-show="showModal" 
         x-cloak
         @click.self="closeModal()"
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl transform transition-all duration-300"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.stop>
            
            <h2 class="text-2xl font-extrabold mb-4 text-gray-800 border-b pb-2 border-gray-100">Adjust Stock Quantity</h2>

            <form id="adjustmentForm" method="POST" action="{{ route('stockadjustment.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Select Product Stock</label>
                   <select name="stock_group_key" 
                           id="stockSelect" 
                           required
                           @change="updateCurrentStock($event)"
                           class="w-full px-4 py-2 border rounded-full text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                        <option value="">Select Stock</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->stock_id }}" 
                                    data-current="{{ $stock->current_stock }}"
                                    data-product="{{ $stock->product->product_name ?? 'Unknown' }}"
                                    data-type="{{ $stock->product_type ?? 'N/A' }}"
                                    data-size="{{ $stock->size ?? 'N/A' }}"
                                    data-stock-ids='@json($stock->stock_ids)'>
                                {{ $stock->product->product_name ?? 'Unknown' }} 
                                (Type: {{ $stock->product_type ?? 'N/A' }}) 
                                (Size: {{ $stock->size ?? 'N/A' }}) - 
                                Current: {{ $stock->current_stock }}
                            </option>
                        @endforeach
                    </select>

                    <input type="hidden" name="stock_ids" id="stockIds">
                </div>

                <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-bold">Product</p>
                            <p id="productDisplay" class="text-sm text-gray-800 font-medium" x-text="productName">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-bold">Product Type</p>
                            <p id="productTypeDisplay" class="text-sm text-gray-800 font-medium" x-text="productType">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-bold">Size</p>
                            <p id="sizeDisplay" class="text-sm text-gray-800 font-medium" x-text="size">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-bold">Current Stock</p>
                            <p id="currentStockDisplay" class="text-lg text-yellow-600 font-bold" x-text="currentStock">0</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Adjustment Type</label>
                        <select name="adjustment_type" required 
                            class="w-full px-4 py-2 border rounded-full text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                            <option value="">Select Type</option>
                            <option value="Addition">Addition (Stock Count Up)</option>
                            <option value="Deduction">Deduction (Stock Count Down)</option>
                            <option value="Correction">Correction (Inventory Error)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Quantity to Adjust</label>
                        <input type="number" name="quantity_adjusted" min="1" required 
                            class="w-full px-4 py-2 border rounded-full text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                            placeholder="Enter quantity">
                    </div>
                </div>
                

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Reason</label>
                    <textarea name="reason" rows="3" required
                        class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        placeholder="Explain the reason for this adjustment (e.g., Damaged goods, inventory count error)"></textarea>
                </div>

                <input type="hidden" name="request_date" value="{{ date('Y-m-d') }}">

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" 
                            @click="closeModal()"
                            class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-6 py-2 bg-yellow-400 text-black rounded-full hover:bg-yellow-500 font-semibold transition shadow-md">
                        Submit Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function adjustmentManager() {
    return {
        showModal: false,
        productName: '-',
        productType: '-',
        size: '-',
        currentStock: '0',

        openModal() {
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            document.body.style.overflow = 'auto';
            this.resetModalData();
        },

        resetModalData() {
            this.productName = '-';
            this.productType = '-';
            this.size = '-';
            this.currentStock = '0';
            
            const form = document.getElementById('adjustmentForm');
            if (form) form.reset();
            
            document.getElementById('stockIds').value = '';
        },

        updateCurrentStock(event) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];

            this.currentStock = selectedOption.getAttribute('data-current') || '0';
            this.productName = selectedOption.getAttribute('data-product') || '-';
            this.productType = selectedOption.getAttribute('data-type') || '-';
            this.size = selectedOption.getAttribute('data-size') || '-';
            const stockIds = selectedOption.getAttribute('data-stock-ids') || '[]';

            document.getElementById('stockIds').value = stockIds;
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const adjustmentForm = document.getElementById('adjustmentForm');
    
    if (adjustmentForm) {
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
                    showSuccessMessage(data.message || 'Stock adjustment submitted successfully!');
                    setTimeout(() => { window.location.reload(); }, 1000);
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
    }
});

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-[70] transform translate-x-0 transition-transform duration-300';
    successDiv.textContent = message;
    
    document.body.appendChild(successDiv);
    
    setTimeout(() => { successDiv.classList.add('translate-x-0'); }, 10);
    setTimeout(() => {
        successDiv.classList.add('translate-x-full');
        setTimeout(() => { document.body.removeChild(successDiv); }, 300);
    }, 3000);
}

// PAGINATION + FILTERS
const saRowsPerPage   = 5;
const saTableBody     = document.getElementById('stockadjustment-table-body');
const saAllRows       = Array.from(saTableBody.querySelectorAll('.sa-row'));
const saEmptyRow      = document.getElementById('sa-empty-row');
const saPaginationLinks = document.getElementById('stockadjustment-pagination-links');
const saPaginationInfo  = document.getElementById('stockadjustment-pagination-info');

const saSearchInput  = document.getElementById('stockadjustment-search');
const saTypeFilter   = document.getElementById('sa-type-filter');
const saStatusFilter = document.getElementById('sa-status-filter');

let saCurrentPage  = 1;
let saFilteredRows = [...saAllRows];

function applySAFilters() {
    const q      = (saSearchInput.value || '').toLowerCase();
    const type   = saTypeFilter ? saTypeFilter.value : 'all';
    const status = saStatusFilter ? saStatusFilter.value : 'all';

    saFilteredRows = saAllRows.filter(row => {
        const rowType   = (row.getAttribute('data-type') || '').trim();
        const rowStatus = (row.getAttribute('data-status') || '').trim();

        if (type !== 'all' && rowType !== type) return false;
        if (status !== 'all' && rowStatus !== status) return false;

        if (q) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(q)) return false;
        }
        return true;
    });

    if (saFilteredRows.length === 0) {
        saAllRows.forEach(r => r.style.display = 'none');
        saEmptyRow.classList.remove('hidden');
        saPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        saPaginationLinks.innerHTML = '';
        return;
    } else {
        saEmptyRow.classList.add('hidden');
    }

    saCurrentPage = 1;
    showSAPage(1);
}

function showSAPage(page) {
    const saTotalPages = Math.ceil(saFilteredRows.length / saRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > saTotalPages) page = saTotalPages;

    saCurrentPage = page;

    saAllRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * saRowsPerPage;
    const end   = start + saRowsPerPage;
    saFilteredRows.slice(start, end).forEach(row => row.style.display = '');

    renderSAPagination(saTotalPages);

    const startItem = saFilteredRows.length ? start + 1 : 0;
    const endItem   = end > saFilteredRows.length ? saFilteredRows.length : end;
    saPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${saFilteredRows.length} results`;
}

function renderSAPagination(saTotalPages) {
    saPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = saCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (saCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSAPage(saCurrentPage - 1);
        });
    }
    saPaginationLinks.appendChild(prev);

    for (let i = 1; i <= saTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === saCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === saCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== saCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showSAPage(i);
            });
        }
        saPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = saCurrentPage === saTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (saCurrentPage !== saTotalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSAPage(saCurrentPage + 1);
        });
    }
    saPaginationLinks.appendChild(next);
}

saSearchInput.addEventListener('input', applySAFilters);
if (saTypeFilter)   saTypeFilter.addEventListener('change', applySAFilters);
if (saStatusFilter) saStatusFilter.addEventListener('change', applySAFilters);

applySAFilters();
</script>

@endsection
