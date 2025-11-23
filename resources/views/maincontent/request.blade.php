@extends('layouts.app')

@section('title', 'Requests')

@section('content')
<div x-data="{ selectedDeliveryId: null, showDetails: false }" x-cloak class="relative">

    <!-- Page Header -->
    <header class="mb-4 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900">Ongoing Deliveries</h1>
        <p class="text-gray-600 mt-2">View all deliveries that are still in progress.</p>
    </header>

    <!-- Buttons (right) above the table -->
    <div class="flex justify-end mb-4 gap-2 max-w-7xl mx-auto">
        @include('added.delivery_history')
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
                @foreach ($deliveries->where('status', '!=', 'Delivered') as $delivery)
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
                            {{ $delivery->delivery_date_received ?? '- -' }}
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
                            <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                            <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                        </td>

                        <!-- Hover overlay for whole row -->
                        <td colspan="7" class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition-opacity duration-200 bg-sky-100 z-10">
                            <div class="w-full h-full flex">
                                <!-- Details button -->
                                <button type="button" class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                    @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                                    <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                        Details
                                    </span>
                                </button>

                                @if($delivery->status === 'In Transit')
                                    <button type="button" class="flex-1 flex items-center justify-center bg-blue-200 hover:bg-blue-300 transition-colors"
                                        @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'Out for Delivery')">
                                        <span class="text-blue-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Confirm to Delivery
                                        </span>
                                    </button>
                                @elseif($delivery->status === 'Out for Delivery')
                                    <button type="button" class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors"
                                        @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'For Stock In')">
                                        <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Confirm to Stock In
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
                    @php
                        $grandTotal = $delivery->details->sum(fn($d) => $d->quantity_product * $d->unit_cost);
                    @endphp

                    @foreach ($delivery->details as $item)
                        <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                            <td class="px-4 py-2 text-center">
                                DD{{ str_pad($item->deliverydetails_id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-2 text-center">{{ $item->product->product_name ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->quantity_product }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->unit ?? '-' }}</td>
                            <td class="px-4 py-2 text-right">₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity_product * $item->unit_cost, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">₱{{ number_format($grandTotal, 2) }}</td>
                    </tr>
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

</div>

<script>
function updateDeliveryStatus(deliveryId, status) {
    if(!confirm(`Are you sure you want to change the status to "${status}"?`)) return;

    axios.post(`/deliveries/${deliveryId}/update-status`, { status })
        .then(res => {
            if(res.data.success) {
                alert(res.data.message);
                location.reload(); // reload to reflect updated status
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error updating status.');
        });
}
</script>
@endsection
