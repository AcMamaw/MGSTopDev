@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 0.5in 0.5in;
    }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        background: white;
    }

    body * {
        visibility: hidden;
    }

    #printableTransactions,
    #printableTransactions * {
        visibility: visible;
    }

    #printableTransactions {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        background: white;
    }

    #printableTransactions .print-buttons {
        display: none !important;
    }

    #printableTransactions table {
        width: 100%;
        border-collapse: collapse !important;
        border: 1px solid #333 !important;
        font-size: 8px;
    }

    #printableTransactions thead {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printableTransactions thead th {
        border: 1px solid #333 !important;
        padding: 3px 2px !important;
        text-align: center !important;
        font-size: 7px;
        background: #f0f0f0 !important;
        color: #333 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printableTransactions tbody td {
        border: 1px solid #333 !important;
        padding: 2px !important;
        text-align: center !important;
        font-size: 7px;
        color: #333 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printableTransactions tbody tr:nth-child(odd) {
        background: #fafafa !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printableTransactions .w-3.h-3.rounded-full {
        width: 6px !important;
        height: 6px !important;
        border-radius: 50%;
        display: inline-block;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printableTransactions .bg-gray-500 { background: #9ca3af !important; -webkit-print-color-adjust: exact !important; }
    #printableTransactions .bg-yellow-500 { background: #eab308 !important; -webkit-print-color-adjust: exact !important; }
    #printableTransactions .bg-blue-500 { background: #3b82f6 !important; -webkit-print-color-adjust: exact !important; }
    #printableTransactions .bg-green-500 { background: #22c55e !important; -webkit-print-color-adjust: exact !important; }
    #printableTransactions .bg-red-500 { background: #ef4444 !important; -webkit-print-color-adjust: exact !important; }

    #printableTransactions tfoot .mt-4 {
        margin-top: 10px !important;
        padding: 0 10px !important;
        font-size: 8px !important;
    }
}
</style>

 
<script>
    function reportManager() {
        return {
            category: '{{ old('category', 'Order') }}',
            reportType: '',
            dateFrom: '',
            dateTo: '',
            coverageText: '',
            submitting: false,
            reports: @json($reports->items() ?? []),
            currentOrders: [],
            showPrintable: false,
            generatedByName: '{{ auth()->user()->employee->fname ?? "" }} {{ auth()->user()->employee->lname ?? "" }}' || 'System',

            // ---------- helpers for headers / fields ----------
            getTableHeaders() {
                const headers = {
                    'Order': ['Order ID', 'Customer', 'Category', 'Product Type', 'Total Amount', 'Order Date', 'Status'],
                    'Deliveries': ['Delivery ID', 'Supplier', 'Employee', 'Request Date', 'Date Received', 'Received By', 'Status'],
                    'Job Order': ['Job Order ID', 'Product', 'Assigned To', 'Estimated Time', 'Start', 'End', 'Status'],
                    'Inventory': ['Stock ID', 'Product', 'Size', 'Type', 'Total', 'Current', 'Received By', 'Stock Level'],
                    'Stock Out': ['Stockout ID', 'Product', 'Employee', 'Size', 'Product Type', 'Quantity Out', 'Date Out', 'Reason', 'Status'],
                    'Stock Adjustment': ['Adjustment ID', 'Product', 'Employee', 'Type', 'Quantity', 'Request Date', 'Reason', 'Status', 'Adjusted By', 'Approved By'],
                    'Payment': ['Payment ID', 'Authorized By', 'Method', 'Reference #', 'Date', 'Amount', 'Cash', 'Balance', 'Change', 'Status']
                };
                return headers[this.category] || headers['Order'];
            },

            getDataFields() {
                const fields = {
                    'Order': ['order_id', 'customer_name', 'category_name', 'product_type', 'total_amount', 'order_date', 'status'],
                    'Deliveries': ['delivery_id', 'supplier_name', 'employee_name', 'request_date', 'delivery_received_date', 'received_by', 'status'],
                    'Job Order': ['job_order_id', 'product_name', 'assigned_to', 'estimated_time', 'start_date', 'end_date', 'status'],
                    'Inventory': ['stock_id', 'product_name', 'size', 'product_type', 'total_quantity', 'current_quantity', 'received_by', 'stock_level'],
                    'Stock Out': ['stockout_id', 'product_name', 'employee_name', 'size', 'product_type', 'quantity_out', 'date_out', 'reason', 'status'],
                    'Stock Adjustment': ['adjustment_id', 'product_name', 'employee_name', 'adjustment_type', 'quantity', 'request_date', 'reason', 'status', 'adjusted_by', 'approved_by'],
                    'Payment': ['payment_id', 'authorized_by', 'payment_method', 'reference_number', 'date', 'amount', 'cash', 'balance', 'change', 'status']
                };
                return fields[this.category] || fields['Order'];
            },

            // ---------- stock level helper (used by Inventory table) ----------
            stockLevel(qtyRaw) {
                const qty = Number(qtyRaw ?? 0);

                if (qty <= 0)   return { cls: 'bg-gray-400',  text: 'Out of Stock' };
                if (qty <= 30)  return { cls: 'bg-red-500',   text: 'Low Stock' };
                if (qty <= 60)  return { cls: 'bg-yellow-500',text: 'Medium Stock' };
                return { cls: 'bg-green-500', text: 'High Stock' };
            },

            // ---------- date / type / coverage ----------
            computeTypeAndCoverage() {
                if (!this.dateFrom || !this.dateTo) {
                    this.reportType = '';
                    this.coverageText = '';
                    return;
                }

                const start = new Date(this.dateFrom);
                const end   = new Date(this.dateTo);

                if (isNaN(start) || isNaN(end) || end < start) {
                    this.reportType = 'Invalid Range';
                    this.coverageText = '';
                    return;
                }

                const diffMs   = end - start;
                const diffDays = diffMs / (1000 * 60 * 60 * 24) + 1;
                const y1 = start.getFullYear(), m1 = start.getMonth(), d1 = start.getDate();
                const y2 = end.getFullYear(),   m2 = end.getMonth(), d2 = end.getDate();

                this.reportType = 'Custom';

                if (y1 === y2 && m1 === 0 && d1 === 1 && m2 === 11 && d2 === 31) {
                    this.reportType = 'Yearly';
                } else if (y1 === y2 && m1 === m2 && d1 === 1) {
                    const lastDay = new Date(y1, m1 + 1, 0).getDate();
                    if (d2 === lastDay) this.reportType = 'Monthly';
                } else if (diffDays === 7) {
                    this.reportType = 'Weekly';
                } else if (diffDays <= 4) {
                    this.reportType = 'Daily';
                }

                // Coverage text used in header and sent to backend
                this.coverageText = `${this.dateFrom} to ${this.dateTo}`;
            },

            // ---------- history table row helper ----------
            addReportRowToTable(report) {
                const tableBody = document.getElementById('report-table-body');
                const emptyRow  = document.getElementById('report-empty-row');

                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors report-row';

                let badgeClass = 'bg-gray-100 text-gray-800';
                if (report.report_type === 'Monthly') badgeClass = 'bg-blue-100 text-blue-800';
                else if (report.report_type === 'Yearly') badgeClass = 'bg-green-100 text-green-800';
                else if (report.report_type === 'Weekly') badgeClass = 'bg-black-100 text-black-800';
                else if (report.report_type === 'Daily')  badgeClass = 'bg-black-100 text-black-800';

                tr.innerHTML = `
                    <td class="px-4 py-3 text-gray-900 font-medium">${report.report_id}</td>
                    <td class="px-4 py-3 text-gray-700 capitalize">${report.category}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                            ${report.report_type}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">${report.coverage ?? 'N/A'}</td>
                    <td class="px-4 py-3 text-gray-600">${this.generatedByName}</td>
                    <td class="px-4 py-3 text-gray-500">${report.created_at_formatted}</td>
                `;

                tableBody.insertBefore(tr, tableBody.firstChild);
            },

            // ---------- generate report (POST -> controller) ----------
            generate() {
                this.computeTypeAndCoverage();

                if (!this.dateFrom || !this.dateTo || this.reportType === 'Invalid Range') {
                    alert('Please select a valid date range.');
                    return;
                }

                this.submitting = true;

                fetch('{{ route('reports.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        category: this.category,
                        report_type: this.reportType,
                        coverage: this.coverageText,
                        date_from: this.dateFrom,
                        date_to: this.dateTo,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error('fail');

                    const createdDate = new Date(data.report.created_at);
                    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    const formatted =
                        `${months[createdDate.getMonth()]} ${createdDate.getDate()}, ${createdDate.getFullYear()} ` +
                        `${createdDate.getHours() % 12 || 12}:${String(createdDate.getMinutes()).padStart(2,'0')} ` +
                        `${createdDate.getHours() >= 12 ? 'PM' : 'AM'}`;
                    data.report.created_at_formatted = formatted;

                    this.reports.unshift(data.report);
                    this.addReportRowToTable(data.report);

                    if (typeof updateReportPagination === 'function') {
                        updateReportPagination();
                    }

                    // data.orders is already date‑filtered by the controller
                    if (data.orders) {
                        this.currentOrders = data.orders;
                        this.showPrintable = true;

                        if (data.generated_by_name) {
                            this.generatedByName = data.generated_by_name;
                        }

                        setTimeout(() => {
                            document.getElementById('printableTransactions')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 300);
                    }

                    alert('Report successfully generated and stored.');
                })
                .catch(() => alert('Failed to save report. Please check server logs.'))
                .finally(() => this.submitting = false);
            },

            // ---------- actions ----------
            printReport() {
                window.print();
            },

            exportToExcel() {
                let csvContent = this.getTableHeaders().join(',') + '\n';
                const fields = this.getDataFields();

                this.currentOrders.forEach(order => {
                    const row = fields.map(field => {
                        const val = order[field] || '';
                        return `"${val}"`;
                    }).join(',');
                    csvContent += row + '\n';
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url  = URL.createObjectURL(blob);

                link.setAttribute('href', url);
                link.setAttribute('download', `${this.category.toLowerCase()}_report_${this.coverageText.replace(/\s+/g, '_')}.csv`);
                link.style.visibility = 'hidden';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            // ---------- misc helpers ----------
            getStatusColor(status) {
                const colors = {
                    'Pending': 'bg-gray-500',
                    'In Progress': 'bg-yellow-500',
                    'Released': 'bg-blue-500',
                    'Completed': 'bg-green-500',
                    'Cancelled': 'bg-red-500',
                    'In Stock': 'bg-green-500'
                };
                return colors[status] || 'bg-gray-400';
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                try {
                    return new Date(dateString).toLocaleDateString('en-US', options);
                } catch (e) {
                    return dateString;
                }
            }
        }
    }
</script>

<div x-data="reportManager()" x-cloak>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-8 max-w-7xl mx-auto">
            <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-5h6v5m-6 0h6m-6 0v2m6-2v2M4 7h16M4 11h16M4 15h16M4 19h16M4 3h16a2 2 0 012 2v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                    Reports 
                </h1>
            </div>
            <p class="text-gray-600 mt-2 text-md">Generate and archive customized reports based on date range and category.</p>
        </header>

        {{-- Section 1: Report Generation Form --}}
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8 border-t-4 border-yellow-400">
            <h2 class="text-xl font-bold text-gray-800 mb-4">⚙️ Generate New Report</h2>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Category</label>
                    <select x-model="category"
                            @change="computeTypeAndCoverage()"
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm p-2 text-sm">
                        <option value="Order">Order</option>
                        <option value="Deliveries">Deliveries</option>
                        <option value="Job Order">Job Order</option>
                        <option value="Inventory">Inventory</option>
                        <option value="Stock Out">Stock Out</option>
                        <option value="Stock Adjustment">Stock Adjustment</option>
                        <option value="Payment">Payment</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date"
                           x-model="dateFrom"
                           @change="computeTypeAndCoverage()"
                           class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm p-2 text-sm">
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date"
                           x-model="dateTo"
                           @change="computeTypeAndCoverage()"
                           class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm p-2 text-sm">
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calculated Type</label>
                    <input type="text"
                           x-model="reportType"
                           readonly
                           :class="{
                               'bg-gray-100 border-gray-300 text-gray-800': reportType !== 'Invalid Range',
                               'bg-red-50 border-red-400 text-red-600 font-semibold': reportType === 'Invalid Range'
                           }"
                           class="w-full rounded-md p-2 text-sm">
                </div>

                <div class="col-span-1">
                    <button type="button"
                            @click="generate()"
                            :disabled="submitting || !dateFrom || !dateTo || reportType === 'Invalid Range'"
                            class="w-full py-2.5 rounded-md text-sm font-semibold transition duration-150"
                            :class="submitting || !dateFrom || !dateTo || reportType === 'Invalid Range'
                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                : 'bg-yellow-400 text-black hover:bg-yellow-500 shadow-md'">
                        <span x-show="!submitting">Generate</span>
                        <span x-show="submitting" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Section 2: Report History --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-yellow-400 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📄 Report History</h2>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 w-16">ID</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 w-32">Category</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 w-32">Type</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 flex-1">Coverage</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Generated By</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 w-40">Created At</th>
                        </tr>
                    </thead>
                    <tbody id="report-table-body" class="divide-y divide-gray-100">
                        @forelse($reports as $rep)
                            <tr class="hover:bg-gray-50 transition-colors report-row">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $rep->report_id }}</td>
                                <td class="px-4 py-3 text-gray-700 capitalize">{{ $rep->category }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($rep->report_type === 'Monthly') bg-blue-100 text-blue-800
                                        @elseif($rep->report_type === 'Yearly') bg-black-100 text-black-800
                                        @elseif($rep->report_type === 'Weekly') bg-black-100 text-black-800
                                        @elseif($rep->report_type === 'Daily') bg-black-100 text-black-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $rep->report_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $rep->coverage ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ optional($rep->generatedBy)->fname }} {{ optional($rep->generatedBy)->lname ?? 'System' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ \Carbon\Carbon::parse($rep->created_at)->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr id="report-empty-row">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                    No reports stored yet. Generate your first report above!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto pb-8">
            <div id="report-pagination-info"></div>
            <ul id="report-pagination-links" class="pagination-links flex gap-2"></ul>
        </div>
        <br>

        {{-- Section 3: Printable Report --}}
        <div
            id="printableTransactions"
            class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-yellow-400"
            x-show="showPrintable && currentOrders.length > 0"
            x-transition
        >
            <div class="print-buttons flex justify-end items-center gap-4 mb-4">
                <button type="button"
                        @click="printReport()"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="2" width="20" height="20" rx="2" ry="2" fill="#DC143C"></rect>
                        <polygon points="22,2 22,6 18,2" fill="#DC143C"></polygon>
                        <text x="12" y="16" font-size="10" font-weight="bold" fill="white" text-anchor="middle">PDF</text>
                    </svg>
                    <span class="text-sm font-medium">PDF</span>
                </button>
                
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
                        {{-- common logo + info --}}
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
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
                        {{-- empty state when no records in the selected coverage --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no orders to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- rows from date‑filtered currentOrders coming from the controller --}}
                        <template x-for="order in currentOrders" :key="order.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-status="order.status">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="order.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="order.customer_name"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="order.category_name"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="order.product_type"></td>

                                <td class="px-3 py-2 text-center text-gray-600">
                                    <span
                                        x-text="Number(order.total_amount || 0).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })">
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="order.order_date"></td>

                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':  order.status === 'Pending',
                                                'bg-yellow-500': order.status === 'In Progress',
                                                'bg-blue-500':  order.status === 'Released',
                                                'bg-green-500': order.status === 'Completed',
                                                'bg-red-500':   order.status === 'Cancelled',
                                                'bg-gray-400':  !['Pending','In Progress','Released','Completed','Cancelled'].includes(order.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold"
                                            x-text="order.status || 'Pending'"></span>
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
                        {{-- Logo + info header --}}
                        <tr>
                            <th colspan="7" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>

                        {{-- Column headers --}}
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Delivery ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Request Date</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Date Received</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>

                    <tbody id="transactions-deliveries-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No deliveries found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no deliveries to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Deliveries) --}}
                        <template x-for="delivery in currentOrders" :key="delivery.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-status="delivery.status">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="delivery.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="delivery.customer_name"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="delivery.request_date ?? delivery.order_date"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="delivery.date_received ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="delivery.received_by_name ?? delivery.received_by ?? '—'"></td>

                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':  delivery.status === 'Pending',
                                                'bg-yellow-500': delivery.status === 'Out for Delivery',
                                                'bg-blue-500':  delivery.status === 'For Stock In',
                                                'bg-green-500': delivery.status === 'Delivered',
                                                'bg-gray-400':  !['Pending','Out for Delivery','For Stock In','Delivered'].includes(delivery.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold"
                                            x-text="delivery.status || 'Pending'"></span>
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
                        {{-- Logo + info header --}}
                        <tr>
                            <th colspan="7" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>

                        {{-- Column headers --}}
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

                    <tbody id="transactions-jobs-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No job orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no job orders to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Job Orders) --}}
                        <template x-for="job in currentOrders" :key="job.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-status="job.status">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="job.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="job.product_type"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="job.customer_name"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="job.estimated_time ?? 0"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="job.order_date"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="job.end_date ?? job.joborder_end ?? '—'"></td>

                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-gray-500':  job.status === 'Pending',
                                                'bg-yellow-500': job.status === 'In Progress',
                                                'bg-green-500': job.status === 'Completed',
                                                'bg-red-500':   job.status === 'Cancelled',
                                                'bg-gray-400':  !['Pending','In Progress','Completed','Cancelled'].includes(job.status)
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold"
                                            x-text="job.status || 'Pending'"></span>
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
                        {{-- Logo + info header --}}
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>

                        {{-- Column headers --}}
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Stock ID</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Size</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Type</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Total</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Current</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Received by</th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase text-gray-500">Stock Level</th>
                        </tr>
                    </thead>

                    <tbody id="inventory-table-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                            <path d="M6 9h12M6 13h8" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No inventory records found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no inventory items to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Inventory) --}}
                        <template x-for="stock in currentOrders" :key="stock.stock_id || stock.order_id">
                            @php
                                // just to avoid Blade errors; logic is in Alpine
                            @endphp
                            <tr class="hover:bg-gray-50 text-xs">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="stock.stock_id || stock.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.product_name || stock.product_type || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.size ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.product_type ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.total_stock ?? stock.total_amount ?? 0"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.current_stock ?? stock.total_amount ?? 0"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="stock.received_by_name ?? stock.received_by ?? '—'"></td>

                               <td class="px-3 py-2 text-center text-xs">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="stockLevel(stock.current_stock ?? stock.total_amount ?? 0).cls"></span>
                                        <span class="text-gray-800 text-xs font-semibold"
                                            x-text="stockLevel(stock.current_stock ?? stock.total_amount ?? 0).text"></span>
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
                        {{-- Logo + info header --}}
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>

                        {{-- Column headers --}}
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

                    <tbody id="stockout-table-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                            <path d="M8 12h8M8 16h5" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No stock-out records found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no stock-out transactions to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Stock Out) --}}
                        <template x-for="so in currentOrders" :key="so.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-status="so.status"
                                :data-product-type="so.product_type">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="so.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.product_type || so.product_name || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.customer_name || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.size ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.product_type ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.total_amount ?? so.quantity_out ?? 0"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.order_date"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.reason ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="so.status ?? '—'"></td>
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
                        {{-- Logo + info header --}}
                        <tr>
                            <th colspan="10" class="px-4 pt-2 pb-4">
                                <div class="text-center">
                                    <div class="flex justify-center mb-3 mt-2">
                                        <div class="w-32 h-12 md:w-40 md:h-16">
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
                                        <p>Generated at: {{ now()->format('m-d-Y') }}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>

                        {{-- column headers --}}
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

                    <tbody id="adjustment-table-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2" />
                                            <path d="M7 12h10M7 16h6" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No stock adjustment records found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no stock adjustments to display for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Stock Adjustment) --}}
                        <template x-for="adj in currentOrders" :key="adj.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-adjust-type="adj.product_type">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="adj.order_id"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.product_name || adj.product_type || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.product_type ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.total_amount ?? adj.quantity_adjusted ?? 0"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.order_date"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.reason ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.status ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.adjusted_by ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="adj.approved_by ?? '—'"></td>
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
                                            <img src="{{ asset('images/ace.jpg') }}"
                                                alt="Company Logo"
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
                                            • Coverage:
                                            <span x-text="coverageText || 'N/A'"></span>
                                        </p>
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

                    <tbody id="payments-table-body" class="divide-y divide-gray-100">
                        {{-- Empty state --}}
                        <template x-if="currentOrders.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-300 mb-4">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                            <line x1="1" y1="10" x2="23" y2="10"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No payment records found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            There are currently no payment records available for this coverage.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Rows from date‑filtered currentOrders (Payments) --}}
                         <template x-for="pay in currentOrders" :key="pay.order_id">
                            <tr class="hover:bg-gray-50 text-xs"
                                :data-status="pay.status">
                                <td class="px-3 py-2 text-center text-gray-800"
                                    x-text="'P' + String(pay.order_id).padStart(3,'0')"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="pay.customer_name || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="pay.product_type || pay.payment_method || '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="pay.reference_number ?? '—'"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="pay.order_date"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="Number(pay.total_amount ?? pay.amount ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="Number(pay.cash ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="Number(pay.balance ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>

                                <td class="px-3 py-2 text-center text-gray-600"
                                    x-text="Number(pay.change_amount ?? 0).toLocaleString('en-PH',{style:'currency',currency:'PHP'})"></td>

                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full"
                                            :class="{
                                                'bg-green-500': (pay.status || '').trim() === 'Fully Paid' || (pay.status || '').trim() === 'Full Paid',
                                                'bg-yellow-500': (pay.status || '').trim() === 'Partial',
                                                'bg-gray-400': true
                                            }"></span>
                                        <span class="text-gray-800 text-xs font-semibold"
                                            x-text="pay.status || 'Partial'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    {{-- Totals row --}}
                    <tfoot>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-700">
                            <td colspan="5" class="px-3 py-2 text-right">Totals:</td>

                            <td class="px-3 py-2 text-center"
                                x-text="Number(currentOrders.reduce((t,p)=>t + Number(p.total_amount ?? p.amount ?? 0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})">
                            </td>

                            <td class="px-3 py-2 text-center"
                                x-text="Number(currentOrders.reduce((t,p)=>t + Number(p.cash ?? 0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})">
                            </td>

                            <td class="px-3 py-2 text-center"
                                x-text="Number(currentOrders.reduce((t,p)=>t + Number(p.balance ?? 0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})">
                            </td>

                            <td class="px-3 py-2 text-center"
                                x-text="Number(currentOrders.reduce((t,p)=>t + Number(p.change_amount ?? 0),0)).toLocaleString('en-PH',{style:'currency',currency:'PHP'})">
                            </td>

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
    </div>
</div>

<script>
const reportRowsPerPage    = 5;
const reportTableBody      = document.getElementById('report-table-body');
const reportEmptyRow       = document.getElementById('report-empty-row');
const reportPaginationLinks= document.getElementById('report-pagination-links');
const reportPaginationInfo = document.getElementById('report-pagination-info');

let allReportRows = Array.from(reportTableBody.querySelectorAll('.report-row'));
let reportCurrentPage = 1;
let reportVisibleRows = [...allReportRows];

function showReportPage(page) {
    const totalPages = Math.ceil(reportVisibleRows.length / reportRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    reportCurrentPage = page;

    allReportRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * reportRowsPerPage;
    const end   = start + reportRowsPerPage;

    reportVisibleRows.slice(start, end).forEach(r => r.style.display = '');

    renderReportPagination(totalPages);

    const startItem = reportVisibleRows.length ? start + 1 : 0;
    const endItem   = end > reportVisibleRows.length ? reportVisibleRows.length : end;
    reportPaginationInfo.textContent =
        `Showing ${startItem} to ${endItem} of ${reportVisibleRows.length} results`;
}

function renderReportPagination(totalPages) {
    reportPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = reportCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (reportCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showReportPage(reportCurrentPage - 1);
        });
    }
    reportPaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' +
                       (i === reportCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === reportCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== reportCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showReportPage(i);
            });
        }
        reportPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = reportCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (reportCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showReportPage(reportCurrentPage + 1);
        });
    }
    reportPaginationLinks.appendChild(next);
}

function updateReportPagination() {
    allReportRows = Array.from(reportTableBody.querySelectorAll('.report-row'));
    reportVisibleRows = [...allReportRows];

    if (allReportRows.length === 0 && reportEmptyRow) {
        reportEmptyRow.style.display = '';
        reportPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        reportPaginationLinks.innerHTML = '';
        return;
    } else if (reportEmptyRow) {
        reportEmptyRow.style.display = 'none';
    }

    showReportPage(1);
}

updateReportPagination();
</script>
@endsection