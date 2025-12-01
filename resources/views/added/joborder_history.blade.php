<!-- This file should start directly with the Alpine data div -->
<div x-data="{ 
    showOrderHistory: false, 
    showOrderDetails2: false, 
    selectedJobOrderId: null,
    jobOrdersList: [],
    init() {
        // Load job orders directly from the joborders table
        this.jobOrdersList = @json($orders->flatMap(function($order) {
            return $order->items->flatMap(function($item) {
                return $item->jobOrders->where('status', 'Completed')->values();
            });
        }));
        console.log('Loaded job orders:', this.jobOrdersList);
    }
}">

    <!-- Button to open Job Order History -->
    <button @click="showOrderHistory = true"        
    class="bg-gray-200 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-gray-300 transition shadow-md">
       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Job Order History</span>
    </button>

    <!-- Job Order History Modal -->
    <div x-show="showOrderHistory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside.stop="showOrderHistory = false" class="bg-white w-full max-w-5xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Job Order History</h2>
                <button @click="showOrderHistory = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center gap-2 mb-4 mt-4">
                <div class="relative w-full max-w-xs">
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Search Job Order ID..."
                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

            <!-- Job Orders Table -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Job Order ID</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Order Detail ID</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Created Date</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Completed Date</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Estimated Time</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase">Made By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="jobOrder in jobOrdersList" :key="jobOrder.joborder_id">
                            <tr class="group relative hover:bg-blue-100 cursor-pointer">
                                <td class="px-4 py-3 text-center text-gray-800 group-hover:opacity-0 font-semibold" x-text="'JO' + String(jobOrder.joborder_id).padStart(3, '0')"></td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="'OD' + String(jobOrder.orderdetails_id).padStart(3, '0')"></td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="new Date(jobOrder.joborder_created).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})"></td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="jobOrder.joborder_end ? new Date(jobOrder.joborder_end).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-'"></td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="jobOrder.estimated_time + ' hrs'"></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="jobOrder.status"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="jobOrder.made_by"></td>

                                <!-- Details Button -->
                                <td colspan="6" class="absolute inset-0 flex items-center justify-center opacity-0 
                                    group-hover:opacity-100 transition-opacity duration-200 bg-blue-100">
                                    <button type="button"
                                        class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                        @click="selectedJobOrderId = jobOrder.joborder_id; showOrderDetails2 = true; showOrderHistory = false">
                                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            View Details
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="jobOrdersList.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">No completed job orders found</p>
                                <p class="text-sm mt-1">Your completed job orders will appear here</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Job Order Details Modal -->
    <div x-show="showOrderDetails2" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h2 class="text-2xl font-bold text-gray-800">
                    Job Order Details
                </h2>
                <button @click="showOrderDetails2 = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

            <!-- Job Order Info -->
            <template x-for="jobOrder in jobOrdersList.filter(jo => jo.joborder_id === selectedJobOrderId)" :key="'detail-' + jobOrder.joborder_id">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Job Order ID</p>
                            <p class="text-lg font-bold text-gray-800" x-text="'JO' + String(jobOrder.joborder_id).padStart(3, '0')"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Order Detail ID</p>
                            <p class="text-lg font-bold text-gray-800" x-text="'OD' + String(jobOrder.orderdetails_id).padStart(3, '0')"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Created Date</p>
                            <p class="text-lg font-bold text-gray-800" x-text="new Date(jobOrder.joborder_created).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Completed Date</p>
                            <p class="text-lg font-bold text-gray-800" x-text="jobOrder.joborder_end ? new Date(jobOrder.joborder_end).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Estimated Time</p>
                            <p class="text-lg font-bold text-gray-800" x-text="jobOrder.estimated_time + ' hours'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Status</p>
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="text-lg font-bold text-green-700" x-text="jobOrder.status"></span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Made By (Employee ID)</p>
                            <p class="text-lg font-bold text-gray-800" x-text="jobOrder.made_by"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Created At</p>
                            <p class="text-lg font-bold text-gray-800" x-text="new Date(jobOrder.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})"></p>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-6 flex justify-end gap-2">
                <button @click="showOrderDetails2 = false; showOrderHistory = true"
                        class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Back to History
                </button>
                <button @click="showOrderDetails2 = false"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>
