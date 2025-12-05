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

        <!-- Left: Search + Status filter -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full">

           <!-- Search -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>

                <input type="text" id="stockout-search" placeholder="Search stock out"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm
                            focus:ring-2 focus:ring-black focus:outline-none">
            </div>

                <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <select id="stockout-status-filter"
                        class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                    <option value="all">All Status</option>
                    <option value="Picked">Picked</option>
                    <option value="Deducted">Deducted</option>
                    <option value="Completed">Completed</option>
                 </select>
            </div>
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
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Employee</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
           <tbody id="stockout-table-body" class="divide-y divide-gray-100">
                @foreach($outstock as $so)
                    <tr class="hover:bg-gray-50 stockout-row"
                        data-status="{{ $so->status }}">
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
                        <td class="px-4 py-3 text-center flex justify-center items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-xs font-semibold text-black">{{ $so->status }}</span>
                        </td>
                    </tr>
                @endforeach

                {{-- Empty state row --}}
                <tr id="stockout-empty-row" class="{{ $outstock->count() ? 'hidden' : '' }}">
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
                                No stock-out records found
                            </p>
                            <p class="text-gray-400 text-xs">
                                There are currently no records matching these filters.
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
    <div id="stockout-pagination-info"></div>
    <ul id="stockout-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
// Pagination + status filter + search + empty state
const soRowsPerPage   = 5;
const soTableBody     = document.getElementById('stockout-table-body');
const soAllRows       = Array.from(soTableBody.querySelectorAll('.stockout-row'));
const soEmptyRow      = document.getElementById('stockout-empty-row');
const soPaginationLinks = document.getElementById('stockout-pagination-links');
const soPaginationInfo  = document.getElementById('stockout-pagination-info');

const soSearchInput  = document.getElementById('stockout-search');
const soStatusFilter = document.getElementById('stockout-status-filter');

let soCurrentPage  = 1;
let soFilteredRows = [...soAllRows];

function applySOFilters() {
    const q      = (soSearchInput.value || '').toLowerCase();
    const status = soStatusFilter ? soStatusFilter.value : 'all';

    soFilteredRows = soAllRows.filter(row => {
        const rowStatus = (row.getAttribute('data-status') || '').trim();

        if (status !== 'all' && rowStatus !== status) return false;

        if (q) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(q)) return false;
        }
        return true;
    });

    if (soFilteredRows.length === 0) {
        soAllRows.forEach(r => r.style.display = 'none');
        soEmptyRow.classList.remove('hidden');
        soPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        soPaginationLinks.innerHTML = '';
        return;
    } else {
        soEmptyRow.classList.add('hidden');
    }

    soCurrentPage = 1;
    showSOPage(1);
}

function showSOPage(page) {
    const soTotalPages = Math.ceil(soFilteredRows.length / soRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > soTotalPages) page = soTotalPages;

    soCurrentPage = page;

    soAllRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * soRowsPerPage;
    const end   = start + soRowsPerPage;
    soFilteredRows.slice(start, end).forEach(row => row.style.display = '');

    renderSOPagination(soTotalPages);

    const startItem = soFilteredRows.length ? start + 1 : 0;
    const endItem   = end > soFilteredRows.length ? soFilteredRows.length : end;
    soPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${soFilteredRows.length} results`;
}

function renderSOPagination(soTotalPages) {
    soPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = soCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (soCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSOPage(soCurrentPage - 1);
        });
    }
    soPaginationLinks.appendChild(prev);

    for (let i = 1; i <= soTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === soCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === soCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== soCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showSOPage(i);
            });
        }
        soPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = soCurrentPage === soTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (soCurrentPage !== soTotalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showSOPage(soCurrentPage + 1);
        });
    }
    soPaginationLinks.appendChild(next);
}

soSearchInput.addEventListener('input', applySOFilters);
if (soStatusFilter) soStatusFilter.addEventListener('change', applySOFilters);

// Init
applySOFilters();
</script>
@endsection
