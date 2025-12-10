@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showAnalyticsInfo: false }">
    <header class="mb-8">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900">Dashboard Overview</h1>
        </div>
        <p class="text-gray-600 mt-2 text-lg">
            Welcome back, <span class="font-bold text-gray-800">{{ auth()->user()->employee->fname ?? 'User' }}</span>!
            Here's what’s happening in your MGS system today.
        </p>
    </header>

    {{-- KICKER CARDS / KEY METRICS + REORDER --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 w-full mb-8">
        {{-- LEFT (40%) : three stacked cards, full height --}}
        <div class="lg:col-span-2 flex flex-col gap-3 h-full">
            {{-- Total Sales --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-1">
                <div class="flex items-center gap-4 p-4 flex-1">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-50 text-green-500 flex-shrink-0">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <!-- Outer note rectangle -->
                            <rect x="3" y="7" width="18" height="10" rx="2" ry="2"
                                  stroke-width="2" />
                            <!-- Inner circle (coin / value) -->
                            <circle cx="12" cy="12" r="3" stroke-width="2" />
                            <!-- Left accent -->
                            <path d="M6 10.5v3" stroke-width="2" stroke-linecap="round" />
                            <!-- Right accent -->
                            <path d="M18 10.5v3" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-green-600 uppercase">Total Sales</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">
                            ₱{{ number_format($salesToday, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Revenue generated today</p>
                    </div>
                </div>
                <div class="w-16 bg-gradient-to-b from-green-100 to-green-200 rounded-l-[40px]"></div>
            </div>

            {{-- Total Inventory --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-1">
                <div class="flex items-center gap-4 p-4 flex-1">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-50 text-blue-500 flex-shrink-0">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7l9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4M12 21V11"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-blue-600 uppercase">Total Inventory</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">
                            {{ $totalAvailableStock ?? '0' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Total items in stock</p>
                    </div>
                </div>
                <div class="w-16 bg-gradient-to-b from-blue-100 to-blue-200 rounded-l-[40px]"></div>
            </div>

            {{-- Low Stock Alerts --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-1">
                <div class="flex items-center gap-4 p-4 flex-1">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-50 text-red-500 flex-shrink-0">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M5 19h14L12 5 5 19z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-red-600 uppercase">Low Stock Alerts</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">
                            {{ $lowStockTotal }}
                        </p>
                        <p class="text-xs text-red-500 mt-1">Products needing reorder</p>
                    </div>
                </div>
                <div class="w-16 bg-gradient-to-b from-red-100 to-red-200 rounded-l-[40px]"></div>
            </div>
        </div>

        {{-- RIGHT (60%) : Products to Reorder, full height --}}
        <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-md flex flex-col">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex justify-between items-center">
                ⚠️ Products to Reorder

                @php
                    $role = auth()->user()->employee->role->role_name ?? null;
                @endphp


                @if($role === 'Admin' || $role === 'All Around Staff')
                    <a href="{{ route('stock') }}" class="text-sm text-red-600 hover:text-red-800 font-normal">
                        View All Alerts →
                    </a>
                @endif
            </h2>

            <div class="divide-y divide-gray-100 flex-1 max-h-80 overflow-y-auto">
                @forelse($lowStockProducts as $stock)
                    <div class="group flex items-center justify-between py-3 px-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0 pr-4">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ $stock->product->product_name ?? 'Product #'.$stock->product_id }} ({{ $stock->size }})
                            </p>
                            <p class="text-xs text-gray-500">
                                Type: {{ $stock->product_type }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-sm text-red-600 font-extrabold">
                                {{ $stock->current_stock }} pcs
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm py-4">
                        🎉 All stock levels are currently sufficient. No immediate reorders needed.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TOP AVAILABLE PRODUCTS SNAPSHOT --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Product Available Snapshot</h2>
        <div class="flex space-x-6 overflow-x-auto py-2">
            @forelse($availableProducts as $item)
                <div class="flex-shrink-0 w-72 flex items-start gap-4 p-3 border border-gray-200 rounded-lg shadow-sm bg-gray-50 hover:bg-white transition-colors">
                    @php
                        $image = $item->product->image_path ?? null;
                    @endphp
                    <img src="{{ $image ? asset($image) : 'https://via.placeholder.com/80' }}"
                         class="w-16 h-16 rounded object-cover shadow" alt="Product Image" />

                    <div class="flex flex-col justify-start text-left">
                        <p class="text-gray-900 text-base font-semibold truncate max-w-[140px]">
                            {{ $item->product->product_name ?? 'Product #'.$item->product_id }}
                        </p>

                        <p class="text-gray-600 text-xs mt-0.5">
                            Type: {{ $item->product_type ?? 'N/A' }}
                            @if(!empty($item->sizes))
                                • Sizes: {{ $item->sizes }}
                            @endif
                        </p>

                        <p class="text-green-600 text-lg font-extrabold mt-1">
                            {{ $item->total_stock }}
                            <span class="text-xs font-normal text-gray-500">in stock</span>
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No products with stock available.</p>
            @endforelse
        </div>
     @if($role === 'Admin' || $role === 'All Around Staff')
        <div class="text-right mt-4">
            <a href="{{ route('stock') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                View Full Inventory →
            </a>
        </div>
    @endif
    </div>

    {{-- SALES CHART (WIDE) + MONTHLY STATS --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6" @click.outside="showAnalyticsInfo = false">
        {{-- SALES CHART – aligned height with Monthly Statistics --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-lg p-6" style="height: 400px;">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Sales Chart</h2>

                <button
                    type="button"
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm underline-offset-2 hover:underline"
                    @click.stop="showAnalyticsInfo = !showAnalyticsInfo"
                >
                    <span x-show="!showAnalyticsInfo">Show Analytics Info →</span>
                    <span x-show="showAnalyticsInfo">Hide Analytics Info →</span>
                </button>
            </div>

            <div class="w-full h-full">
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>
        </div>

       {{-- MONTHLY STATISTICS --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Monthly Statistics</h2>
            
        @if($role === 'Admin' || $role === 'Cashier')
                <a href="{{ route('reports') }}"
                 class="text-sm text-blue-600 hover:text-blue-800 font-normal">
                    Go to Reports →
                </a>
            </div>
        @endif

            <div class="space-y-4">
                <div class="border-b pb-3">
                    <p class="text-sm font-medium text-gray-500">Total Sales (This Month)</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">
                        ₱{{ number_format($monthlySales ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        vs. Last Month: <span class="text-green-500">+4.5%</span>
                    </p>
                </div>

                <div class="border-b pb-3">
                    <p class="text-sm font-medium text-gray-500">Net Profit (This Month)</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">
                        ₱{{ number_format($monthlyProfit ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        vs. Last Month: <span class="text-red-500">-1.2%</span>
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Open Orders</p>
                    <p class="text-3xl font-extrabold text-purple-600 mt-1">
                        {{ $openOrders ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Total pending and in-progress orders
                    </p>
                </div>
            </div>
        </div>
    </div>

        <div
        class="mt-6 bg-white rounded-2xl shadow-2xl p-8 border border-gray-100"
        x-show="showAnalyticsInfo"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
    >
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
            <h2 class="text-2xl font-extrabold text-indigo-800">
                Analytics Overview – Mariviles Graphic Studio
            </h2>
        </div>

        <div class="space-y-4">
            <p class="text-base text-gray-700 leading-relaxed border-b pb-4 border-gray-100">
                Mariviles Graphic Studio is a creative studio focused on <strong>branding, print, and digital design</strong> services for local businesses. We help clients clearly communicate their message through visual design, ranging from <strong>logos and marketing collaterals to social media graphics.</strong>
            </p>

            <h3 class="text-lg font-semibold text-gray-800 pt-2">
                Key Dashboard Metrics
            </h3>
            <p class="text-sm text-gray-600 mb-3">
                This dashboard aggregates essential performance indicators (KPIs) from our MGS system for a holistic view of studio health:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-4 text-sm text-gray-700">
                <li><strong>Financial Performance:</strong> Total Sales and Net Profit, generated from invoicing data.</li>
                <li><strong>Operational Status:</strong> Real-time Inventory levels and the count of Open Orders.</li>
                <li><strong>Data Source:</strong> All metrics are derived directly from our order, stock, and invoicing data.</li>
            </ul>

            <div class="bg-indigo-50 p-4 rounded-lg mt-5 border-l-4 border-indigo-400">
                <p class="text-sm text-indigo-800 font-medium">
                    <strong>Informed Decision Making:</strong> By actively tracking these KPIs, you can quickly identify in-demand services, spot stock issues, and clearly understand cash flow, ensuring the growth plans are driven by <strong>real data, not guesswork.</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('salesChart');
    if (!canvas) return;

    const ctx    = canvas.getContext('2d');
    const labels = @json($chartLabels);
    const data   = @json($chartData);

    // Check if there is at least one non-zero value
    const hasSales = data.some(v => v > 0);

    const getGradient = (chart) => {
        const { ctx, chartArea } = chart;
        if (!chartArea) return null;
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.4)');
        return gradient;
    };

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (₱)',
                data: data,
                backgroundColor: context => getGradient(context.chart),
                borderColor: '#2563eb',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#2563eb',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { left: 10, right: 20, top: 10, bottom: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 7,
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: hasSales ? Math.max(...data) * 1.2 : 1,
                    grid: { color: 'rgba(200, 200, 200, 0.2)', drawBorder: false },
                    ticks: {
                        callback: function (value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
