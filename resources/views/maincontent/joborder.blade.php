@extends('layouts.app')

@section('title', 'Job Orders')

@section('content')
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Job Orders</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage job orders including customer, description, order date, and status.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
    <div class="relative w-full md:w-1/4">
        <input type="text" id="joborder-search" placeholder="Search job orders"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
    </div>

    <a href="#" class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5v14" />
            <path d="M5 12h14" />
        </svg>
        <span>Add New Job Order</span>
    </a>
</div>

<!-- Job Orders Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="joborder-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody id="joborder-table-body" class="divide-y divide-gray-100">
                @foreach($jobOrders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">J{{ str_pad($order->joborder_id,3,'0',STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $order->order_date }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $order->description }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $order->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="joborder-pagination-info">Showing 1 to 1 of {{ $jobOrders->count() }} results</div>
    <ul id="joborder-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
const rowsPerPage = 5;
const tableBody = document.getElementById('joborder-table-body');
let rows = Array.from(tableBody.querySelectorAll('tr'));
const paginationLinks = document.getElementById('joborder-pagination-links');
const paginationInfo = document.getElementById('joborder-pagination-info');
let currentPage = 1;
const totalPages = Math.ceil(rows.length / rowsPerPage);

function showPage(page) {
    currentPage = page;
    rows.forEach(r => r.style.display = 'none');
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.slice(start, end).forEach(r => r.style.display = '');
    renderPagination();
    const startItem = rows.length ? start + 1 : 0;
    const endItem = end > rows.length ? rows.length : end;
    paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${rows.length} results`;
}

function renderPagination() {
    paginationLinks.innerHTML = '';
    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = currentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if(currentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(currentPage - 1); });
    paginationLinks.appendChild(prev);

    for(let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === currentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === currentPage ? i : `<a href="#">${i}</a>`;
        if(i !== currentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(i); });
        paginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = currentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if(currentPage !== totalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPage(currentPage + 1); });
    paginationLinks.appendChild(next);
}

// Initialize
showPage(1);

// Search
document.getElementById('joborder-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
        const match = Array.from(row.querySelectorAll('td')).some(cell => cell.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endsection
