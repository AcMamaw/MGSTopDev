@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Stock Adjustments</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage stock adjustment records including quantity changes, reasons, and approval status.</p>
</header>

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
        <a href="#"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Adjust Stocks</span>
        </a>
    </div>
</div>

<!-- Stock Adjustment Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="stockadjustment-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Adjustment ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock ID</th>
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
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($sa->status === 'Pending')
                                bg-gray-100 text-gray-700
                            @elseif($sa->status === 'Approved')
                                bg-green-100 text-green-700
                            @elseif($sa->status === 'Rejected')
                                bg-red-100 text-red-700
                            @else
                                bg-gray-100 text-gray-700
                            @endif
                        ">
                            {{ $sa->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="stockadjustment-pagination-info">Showing 1 to 1 of 3 results</div>
    <ul id="stockadjustment-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
// Pagination
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
    saPaginationInfo.textContent = `Showing ${startItem} to ${saCurrentPage} of ${saRows.length} results`;
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

// Search
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
