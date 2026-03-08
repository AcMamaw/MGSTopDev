{{-- Section 3: Printable Report --}}
        <div
            id="printableTransactions"
            class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-yellow-400"
            x-show="showPrintable && currentOrders.length > 0"
            x-transition
        >
            {{-- ── Button Bar ── --}}
            <div class="print-buttons flex justify-end items-center gap-4 mb-4">

                {{-- Download PDF link — shown only after S3 upload succeeds --}}
                <a :href="reportPdfUrl || '#'"
                   :target="reportPdfUrl ? '_blank' : '_self'"
                   x-show="reportPdfUrl"
                   x-cloak
                   class="flex items-center gap-2 px-3 py-2 rounded-xl border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 transition"
                   title="Download PDF from S3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="2" width="20" height="20" rx="2" ry="2" fill="#DC143C"></rect>
                        <polygon points="22,2 22,6 18,2" fill="#9B0E0E"></polygon>
                        <text x="12" y="16" font-size="8" font-weight="bold" fill="white" text-anchor="middle">PDF</text>
                    </svg>
                    <span class="text-sm font-medium">Download PDF</span>
                </a>

                {{-- Spinner — shown while uploading to S3 --}}
                <div x-show="generatingPdf && !reportPdfUrl"
                     x-cloak
                     class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 text-gray-400 cursor-not-allowed select-none">
                    <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm">Saving PDF…</span>
                </div>

                {{-- Save PDF button — shown before PDF is generated --}}
                <button type="button"
                        x-show="!reportPdfUrl && !generatingPdf"
                        @click="generateReportPdf()"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="2" width="20" height="20" rx="2" ry="2" fill="#DC143C"></rect>
                        <polygon points="22,2 22,6 18,2" fill="#9B0E0E"></polygon>
                        <text x="12" y="16" font-size="8" font-weight="bold" fill="white" text-anchor="middle">PDF</text>
                    </svg>
                    <span class="text-sm font-medium">Save PDF</span>
                </button>

                {{-- Excel export --}}
                <button type="button"
                        @click="exportToExcel()"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="3" width="9" height="18" rx="1" ry="1" fill="#107C41"></rect>
                        <path d="M4.5 9.5 L8 14.5 M8 9.5 L4.5 14.5" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round"></path>
                        <rect x="9" y="4" width="11" height="16" rx="1" ry="1" stroke="#107C41" stroke-width="1.5" fill="none"></rect>
                        <line x1="13" y1="5.5" x2="13" y2="18.5" stroke="#107C41" stroke-width="1"></line>
                        <line x1="17" y1="5.5" x2="17" y2="18.5" stroke="#107C41" stroke-width="1"></line>
                        <line x1="10.5" y1="9"  x2="19.5" y2="9"  stroke="#107C41" stroke-width="1"></line>
                        <line x1="10.5" y1="12.5" x2="19.5" y2="12.5" stroke="#107C41" stroke-width="1"></line>
                        <line x1="10.5" y1="16" x2="19.5" y2="16" stroke="#107C41" stroke-width="1"></line>
                    </svg>
                    <span class="text-sm font-medium">Excel</span>
                </button>
            </div>

           {{-- ORDER TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Order' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="7" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo"
                                                class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">
                                        MARIVILES GRAPHIC STUDIO
                                    </h1>
                                    <p class="text-xs mt-2">
                                        ADOPTED CO.<br>MATI CITY
                                    </p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>
                                            Type: <span x-text="reportType"></span>
                                            • Coverage: <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Customer</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Category</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Total Amount</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Order Date</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-orders-body" class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no orders to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="order in currentOrders" :key="order.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-status="order.status">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="order.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="order.customer_name"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="order.category_name"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="order.product_type"></td>
                                <td class="px-3 py-2 text-center text-gray-600">
                                    <span x-text="Number(order.total_amount || 0).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })"></span>
                                </td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="order.order_date"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':   order.status === 'Pending',
                                                'bg-yellow-500': order.status === 'In Progress',
                                                'bg-blue-500':   order.status === 'Released',
                                                'bg-green-500':  order.status === 'Completed',
                                                'bg-red-500':    order.status === 'Cancelled',
                                                'bg-gray-400':   !['Pending','In Progress','Released','Completed','Cancelled'].includes(order.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="order.status || 'Pending'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- DELIVERIES TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Deliveries' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="7" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Delivery ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Request Date</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Date Received</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No deliveries found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no deliveries to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="delivery in currentOrders" :key="delivery.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-status="delivery.status">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="delivery.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="delivery.customer_name"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="delivery.request_date ?? delivery.order_date"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="delivery.date_received ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="delivery.received_by_name ?? delivery.received_by ?? '—'"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':   delivery.status === 'Pending',
                                                'bg-yellow-500': delivery.status === 'Out for Delivery',
                                                'bg-blue-500':   delivery.status === 'For Stock In',
                                                'bg-green-500':  delivery.status === 'Delivered',
                                                'bg-gray-400':   !['Pending','Out for Delivery','For Stock In','Delivered'].includes(delivery.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="delivery.status || 'Pending'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- JOB ORDER TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Job Order' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="7" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Job Order ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Assigned To</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Estimated Time</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Start</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">End</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No job orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no job orders to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="job in currentOrders" :key="job.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-status="job.status">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="job.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="job.product_type"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="job.customer_name"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="job.estimated_time ?? 0"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="job.order_date"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="job.end_date ?? job.joborder_end ?? '—'"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':   job.status === 'Pending',
                                                'bg-yellow-500': job.status === 'In Progress',
                                                'bg-green-500':  job.status === 'Completed',
                                                'bg-red-500':    job.status === 'Cancelled',
                                                'bg-gray-400':   !['Pending','In Progress','Completed','Cancelled'].includes(job.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="job.status || 'Pending'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- INVENTORY TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Inventory' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Stock ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Size</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Type</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Total</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Current</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Stock Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                                            <path d="M6 9h12M6 13h8"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No inventory records found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no inventory items to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="stock in currentOrders" :key="stock.stock_id || stock.order_id">
                            <tr class="hover:bg-gray-50 text-xs">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="stock.stock_id || stock.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.product_name || stock.product_type || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.size ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.product_type ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.total_stock ?? stock.total_amount ?? 0"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.current_stock ?? stock.total_amount ?? 0"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="stock.received_by_name ?? stock.received_by ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-xs">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full" :class="stockLevel(stock.current_stock ?? stock.total_amount ?? 0).cls"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="stockLevel(stock.current_stock ?? stock.total_amount ?? 0).text"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- STOCK OUT TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Stock Out' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Stockout ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Employee</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Size</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Quantity Out</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Date Out</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Reason</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                                            <path d="M8 12h8M8 16h5"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No stock-out records found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no stock-out transactions to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="so in currentOrders" :key="so.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-status="so.status">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="so.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.product_type || so.product_name || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.customer_name || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.size ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.product_type ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.total_amount ?? so.quantity_out ?? 0"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.order_date"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.reason ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="so.status ?? '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- STOCK ADJUSTMENT TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Stock Adjustment' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Adjustment ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Type</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Quantity</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Request Date</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Reason</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Adjusted By</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Approved By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M7 12h10M7 16h6"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No stock adjustment records found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no stock adjustments to display for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="adj in currentOrders" :key="adj.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-adjust-type="adj.product_type">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="adj.order_id"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.product_name || adj.product_type || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.product_type ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.total_amount ?? adj.quantity_adjusted ?? 0"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.order_date"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.reason ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.status ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.adjusted_by ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="adj.approved_by ?? '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- PAYMENT TABLE --}}
            <div class="overflow-x-auto" x-show="category === 'Payment' && showPrintable">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}" alt="Company Logo" class="object-contain w-full h-full">
                                        </div>
                                    </div>
                                    <h1 class="text-base md:text-lg font-bold tracking-wide mt-2">MARIVILES GRAPHIC STUDIO</h1>
                                    <p class="text-xs mt-2">ADOPTED CO.<br>MATI CITY</p>
                                    <div class="mt-3 text-xs">
                                        <p class="font-semibold" x-text="category + ' Report'"></p>
                                        <p>Type: <span x-text="reportType"></span> • Coverage: <span x-text="coverageText || 'N/A'"></span></p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Payment ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Authorized by</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Method</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Reference #</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Date</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Amount</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Cash</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Balance</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Change</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                            <line x1="1" y1="10" x2="23" y2="10"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No payment records found</p>
                                        <p class="text-sm text-gray-400 mt-1">There are currently no payment records available for this coverage.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="pay in currentOrders" :key="pay.order_id">
                            <tr class="hover:bg-gray-50 text-xs" :data-status="pay.status">
                                <td class="px-3 py-2 text-center text-gray-800" x-text="'P' + String(pay.order_id).padStart(3,'0')"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="pay.customer_name || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="pay.product_type || pay.payment_method || '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="pay.reference_number ?? '—'"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="pay.order_date"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="Number(pay.total_amount ?? pay.amount ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="Number(pay.cash ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="Number(pay.balance ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                                <td class="px-3 py-2 text-center text-gray-600" x-text="Number(pay.change_amount ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-green-500':  (pay.status || '').trim() === 'Fully Paid' || (pay.status || '').trim() === 'Full Paid',
                                                'bg-yellow-500': (pay.status || '').trim() === 'Partial',
                                                'bg-gray-400':   true
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="pay.status || 'Partial'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-700">
                            <td colspan="5" class="px-3 py-2 text-right">Totals:</td>
                            <td class="px-3 py-2 text-center" x-text="Number(currentOrders.reduce((t,p)=>t+Number(p.total_amount??p.amount??0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                            <td class="px-3 py-2 text-center" x-text="Number(currentOrders.reduce((t,p)=>t+Number(p.cash??0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                            <td class="px-3 py-2 text-center" x-text="Number(currentOrders.reduce((t,p)=>t+Number(p.balance??0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                            <td class="px-3 py-2 text-center" x-text="Number(currentOrders.reduce((t,p)=>t+Number(p.change_amount??0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>
                            <td class="px-3 py-2"></td>
                        </tr>
                        <tr>
                            <td colspan="10" class="pt-4">
                                <div class="mt-4 flex justify-between text-xs text-gray-600">
                                    <span>Generated by: <span x-text="generatedByName"></span></span>
                                    <span x-text="coverageText || ''"></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>