<div x-show="showHistoryModal" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    
    <!-- Modal content -->
    <div @click.away="showHistoryModal = false" 
         class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-5xl max-h-[90vh] overflow-y-auto relative">
        
        <!-- Close button -->
        <button @click="showHistoryModal = false"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>
        
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Delivered Deliveries</h2>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-2 text-center">ID</th>
                    <th class="px-4 py-2 text-center">Supplier</th>
                    <th class="px-4 py-2 text-center">Requested By</th>
                    <th class="px-4 py-2 text-center">Received By</th>
                    <th class="px-4 py-2 text-center">Request Date</th>
                    <th class="px-4 py-2 text-center">Received Date</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($deliveries->where('status', 'Delivered') as $delivery)
                    <tr>
                        <td class="px-4 py-2 text-center">D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->supplier->supplier_name ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->employee->fname ?? '-' }} {{ $delivery->employee->lname ?? '' }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->delivery_date_request }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->delivery_date_received ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $delivery->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
