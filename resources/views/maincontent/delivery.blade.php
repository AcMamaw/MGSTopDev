@extends('layouts.app')

@section('title', 'Delivery')

@section('content')
<div 
    x-data="{
        showDetails: false,
        selectedDeliveryId: null,
        showAddDelivery: false,
        showHistoryModal: false
    }" 
    x-cloak 
    class="relative"
>

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Delivery</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage delivery records and item details for each transaction.</p>
    </header>

    <!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4">

    <!-- Search Input with Icon -->
    <div class="relative w-full md:w-1/4">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>

        <input type="text" placeholder="Search deliveries"
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm
                      focus:ring-2 focus:ring-black focus:outline-none" />
    </div>

    <!-- Buttons (right) -->
    <div class="flex gap-2">

        <!-- History Button -->
        <button @click="showHistoryModal = true"
            class="bg-gray-200 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2
                   hover:bg-gray-300 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 6v6l4 2" />
            </svg>
            <span>History</span>
        </button>

        <!-- Include Delivery History Modal -->
        @include('added.delivery_history')

        <!-- Add New Delivery Button -->
        <a href="#" @click.prevent="showAddDelivery = true"
           class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2
                  hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Delivery</span>
        </a>
    </div>
</div>


    <!-- Delivery Table -->
    <div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
        <table id="delivery-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Delivery ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Requested By</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Requested</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Received</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
            </tr>
            </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach ($deliveries as $delivery)
            <tr class="group relative hover:bg-sky-200 cursor-pointer">
                <!-- Normal row content -->
                <td class="px-4 py-3 text-center font-medium text-gray-800 group-hover:opacity-0">
                    D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }}
                </td>
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $delivery->supplier->supplier_name ?? '-' }}
                </td>
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }}
                </td>
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $delivery->delivery_date_request }}
                </td>
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $delivery->status === 'Delivered' ? ($delivery->delivery_date_received ?? '--') : '--' }}
                </td>
               <td class="px-4 py-3 text-center text-gray-600">
                    {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                </td>
               <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                    @php
                        $dotColor = match($delivery->status) {
                            'In Transit' => 'bg-gray-500',
                            'Out for Delivery' => 'bg-yellow-500',
                            'For Stock In' => 'bg-blue-500',
                            'Delivered' => 'bg-green-500',
                            default => 'bg-gray-400'
                        };
                    @endphp

                    <!-- Colored dot -->
                    <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>

                    <!-- Status text in default color -->
                    <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                </td>


                <!-- Hover overlay for whole row -->
                <td colspan="7" class="absolute inset-0 flex items-center justify-center opacity-0 
                    group-hover:opacity-100 transition-opacity duration-200 bg-sky-100">

                    @if($delivery->status === 'For Stock In')
                        <div class="w-full h-full flex">
                            <!-- Details button -->
                            <button type="button" class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                            @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                                <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                    Details
                                </span>
                            </button>

                            <!-- Stock In button -->
                        <button type="button"
                                @click="stockInDelivery({{ $delivery->delivery_id }})"
                                class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors">
                                <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                    Stock In
                                </span>
                            </button>
                        </div>
                    @else
                        <!-- Single Details button for other statuses -->
                        <button type="button" class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                            @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                            <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                Details
                            </span>
                        </button>
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>

        </table>
    </div>

<script>
    async function stockInDelivery(deliveryId) {
        if (!confirm("Are you sure you want to stock in this delivery? This will mark it as Delivered.")) {
            return;
        }

        try {
            const response = await axios.post(`/deliveries/${deliveryId}/stock-in`);
            
            if (response.data.success) {
                alert(response.data.message);
                location.reload(); // Refresh table to reflect status change
            }
        } catch (error) {
            console.error(error);
            alert('Error stocking in delivery.');
        }
    }
</script>

  <!-- Delivery Details Modal -->
<div x-show="showDetails" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">

    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative">

        <!-- Header -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            Delivery Details - ID: <span x-text="selectedDeliveryId"></span>
        </h2>

        <!-- Table -->
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

            @foreach ($deliveries as $delivery)
                @foreach ($delivery->details as $item)
                    <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                        <td class="px-4 py-2 text-center">
                            DD{{ str_pad($item->deliverydetails_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ $item->product->product_name ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ $item->quantity_product }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ $item->unit ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            ₱{{ number_format($item->unit_cost, 2) }}
                        </td>
                        <td class="px-4 py-2 text-right font-semibold">
                            ₱{{ number_format($item->quantity_product * $item->unit_cost, 2) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach

            </tbody>
        </table>


        <!-- Close Button -->
        <div class="mt-6 flex justify-end">
            <button @click="showDetails = false"
                    class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                Close
            </button>
        </div>

    </div>

</div>
 
    <!-- Include Add Delivery Modal -->
    @include('added.add_delivery')

</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="delivery-pagination-info">Showing 1 to 1 of 3 results</div>
    <ul id="delivery-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
const deliveryRowsPerPage = 5;
const deliveryTableBody = document.getElementById('delivery-table-body');
const deliveryRows = Array.from(deliveryTableBody.querySelectorAll('tr'));
const deliveryPaginationLinks = document.getElementById('delivery-pagination-links');
const deliveryPaginationInfo = document.getElementById('delivery-pagination-info');

let deliveryCurrentPage = 1;
const deliveryTotalPages = Math.ceil(deliveryRows.length / deliveryRowsPerPage);

function showDeliveryPage(page) {
    deliveryCurrentPage = page;
    deliveryRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * deliveryRowsPerPage;
    const end = start + deliveryRowsPerPage;
    deliveryRows.slice(start, end).forEach(row => row.style.display = '');

    renderDeliveryPagination();

    const startItem = deliveryRows.length ? start + 1 : 0;
    const endItem = end > deliveryRows.length ? deliveryRows.length : end;
    deliveryPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${deliveryRows.length} results`;
}

function renderDeliveryPagination() {
    deliveryPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = deliveryCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (deliveryCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage - 1); });
    deliveryPaginationLinks.appendChild(prev);

    for (let i = 1; i <= deliveryTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === deliveryCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === deliveryCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== deliveryCurrentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(i); });
        deliveryPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = deliveryCurrentPage === deliveryTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (deliveryCurrentPage !== deliveryTotalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage + 1); });
    deliveryPaginationLinks.appendChild(next);
}

showDeliveryPage(1);
</script>
@endsection
