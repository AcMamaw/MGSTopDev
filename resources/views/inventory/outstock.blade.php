@extends('layouts.app')

@section('title', 'Stock Out')

@section('content')
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Out Stocks</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage stock-out records including quantity, reasons, and approval status.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="stockout-search" placeholder="Search stock out"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>
    </div>
</div>

<!-- Stock Out Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="stockout-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock Out ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Quantity Out</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Out</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Reason</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Employee ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
           <tbody id="stockout-table-body" class="divide-y divide-gray-100">
                @foreach($outstock as $so)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center text-gray-800 font-medium">
                            SO{{ str_pad($so->stockout_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            S{{ str_pad($so->stock->stock_id ?? 0, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $so->quantity_out }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $so->date_out->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $so->reason }}</td>
                         <td class="px-4 py-3 text-center text-gray-600">
                            {{ $so->employee->fname ?? '' }} {{ $so->employee->lname ?? '' }}
                        </td>
                      <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                        <!-- Green dot -->
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>

                        <!-- Status text in black -->
                        <span class="text-xs font-semibold text-black">{{ $so->status }}</span>
                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="stockout-pagination-info">Showing 1 to 1 of 3 results</div>
    <ul id="stockout-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
// Pagination
const rowsPerPage = 5;
const tableBody = document.getElementById('stockout-table-body');
const rows = Array.from(tableBody.querySelectorAll('tr'));
const paginationLinks = document.getElementById('stockout-pagination-links');
const paginationInfo = document.getElementById('stockout-pagination-info');

let currentPage = 1;
const totalPages = Math.ceil(rows.length / rowsPerPage);

function showPage(page) {
    currentPage = page;

    rows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.slice(start, end).forEach(row => row.style.display = '');

    renderPagination();

    const startItem = rows.length ? start + 1 : 0;
    const endItem = end > rows.length ? rows.length : end;
    saPaginationInfo.textContent = `Showing ${startItem} to ${saCurrentPage} of ${saRows.length} results`;
}

function renderPagination() {
    paginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = currentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (currentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(currentPage - 1); });
    paginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === currentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === currentPage ? i : `<a href="#">${i}</a>`;
        if (i !== currentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(i); });
        paginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = currentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (currentPage !== totalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(currentPage + 1); });
    paginationLinks.appendChild(next);
}

// Initialize
showPage(1);

// Search
document.getElementById('stockout-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const match = cells.some(cell => cell.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endsection
