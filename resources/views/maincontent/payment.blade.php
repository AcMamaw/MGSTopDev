@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Payments</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage all recorded payments from orders and employees.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="payment-search" placeholder="Search payments"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="payment-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Payment ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Payment Date</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Cash</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Change</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Method</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Reference No.</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Issued By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody id="payment-table-body" class="divide-y divide-gray-100">
                @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center font-medium text-gray-800">P{{ str_pad($payment->payment_id,3,'0',STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">O{{ str_pad($payment->order_id,3,'0',STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $payment->payment_date }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($payment->cash, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($payment->change_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $payment->payment_method }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $payment->reference_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $payment->employee->fname ?? '' }} {{ $payment->employee->lname ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Edit -->
                                <button title="Edit"
                                    class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>

                                <!-- Archive Button -->
                                <button title="Archive"
                                    class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200"
                                    onclick="deleteRow(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="25" fill="none" stroke="currentColor"
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
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="payment-pagination-info">Showing 1 to 3 of 3 results</div>
    <ul id="payment-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
const paymentRowsPerPage = 5;
const paymentTableBody = document.getElementById('payment-table-body');
const paymentRows = Array.from(paymentTableBody.querySelectorAll('tr'));
const paymentPaginationLinks = document.getElementById('payment-pagination-links');
const paymentPaginationInfo = document.getElementById('payment-pagination-info');

let paymentCurrentPage = 1;
const paymentTotalPages = Math.ceil(paymentRows.length / paymentRowsPerPage);

function deleteRow(button) {
    if (confirm("Are you sure you want to remove this row from the table?")) {
        button.closest('tr').remove();
    }
}

function showPaymentPage(page) {
    paymentCurrentPage = page;
    paymentRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * paymentRowsPerPage;
    const end = start + paymentRowsPerPage;
    paymentRows.slice(start, end).forEach(row => row.style.display = '');

    renderPaymentPagination();

    const startItem = paymentRows.length ? start + 1 : 0;
    const endItem = end > paymentRows.length ? paymentRows.length : end;
    paymentPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${paymentRows.length} results`;
}

function renderPaymentPagination() {
    paymentPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = paymentCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (paymentCurrentPage !== 1)
        prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPaymentPage(paymentCurrentPage - 1); });
    paymentPaginationLinks.appendChild(prev);

    for (let i = 1; i <= paymentTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === paymentCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === paymentCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== paymentCurrentPage)
            li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPaymentPage(i); });
        paymentPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = paymentCurrentPage === paymentTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (paymentCurrentPage !== paymentTotalPages)
        next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showPaymentPage(paymentCurrentPage + 1); });
    paymentPaginationLinks.appendChild(next);
}

showPaymentPage(1);
</script>

@endsection
