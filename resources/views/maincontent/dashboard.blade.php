@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Dashboard Header -->
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    </div>
    <p class="text-gray-600 mt-2">
        Welcome back, {{ auth()->user()->employee->fname ?? '' }}! Here's what’s happening in your MGS system today.
    </p>
</header>

<div class="grid grid-cols-4 gap-4 w-full mb-6">

    <!-- PRODUCT CARD (spans columns 1 + 2) -->
    <div class="col-span-2 w-full">
        <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
            <h2 class="text-gray-700 text-lg font-semibold mb-4">Product Available</h2>
            <div class="flex space-x-6 overflow-x-auto py-2">
                <!-- Product 1 -->
                <div class="flex-shrink-0 w-60 flex items-start gap-4 p-3 border border-gray-200 rounded-lg shadow-sm bg-white">
                    <img src="https://via.placeholder.com/80" class="w-20 h-20 rounded-lg object-cover shadow" />
                    <div class="flex flex-col justify-start">
                        <p class="text-gray-700 text-sm font-medium">Plain T-Shirt</p>
                        <p class="text-gray-600 text-sm">100% Cotton</p>
                        <p class="text-gray-600 text-sm">Multiple sizes</p>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="flex-shrink-0 w-60 flex items-start gap-4 p-3 border border-gray-200 rounded-lg shadow-sm bg-white">
                    <img src="https://via.placeholder.com/80" class="w-20 h-20 rounded-lg object-cover shadow" />
                    <div class="flex flex-col justify-start">
                        <p class="text-gray-700 text-sm font-medium">Slings</p>
                        <p class="text-gray-600 text-sm">Durable material</p>
                        <p class="text-gray-600 text-sm">Lightweight</p>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="flex-shrink-0 w-60 flex items-start gap-4 p-3 border border-gray-200 rounded-lg shadow-sm bg-white">
                    <img src="https://via.placeholder.com/80" class="w-20 h-20 rounded-lg object-cover shadow" />
                    <div class="flex flex-col justify-start">
                        <p class="text-gray-700 text-sm font-medium">Mugs</p>
                        <p class="text-gray-600 text-sm">Ceramic</p>
                        <p class="text-gray-600 text-sm">Sublimation-ready</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- SALES TODAY (column 3) -->
<div class="col-span-1 w-full">
    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition flex flex-col justify-start h-full">
        <h2 class="text-gray-700 text-lg font-semibold mb-4">Sales Today</h2>
       <br>
        <p class="text-3xl font-semibold text-gray-900">₱3,240</p>
        <p class="text-sm text-red-500 mt-1">↓ 5% from yesterday</p>
    </div>
</div>

<!-- LOW STOCK ALERTS (column 4) -->
<div class="col-span-1 w-full">
    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition flex flex-col justify-start h-full">
        <h2 class="text-gray-700 text-lg font-semibold mb-4">Low Stock Alerts</h2>
       <br>
        <p class="text-3xl font-semibold text-gray-900">6</p>
        <p class="text-sm text-yellow-500 mt-1">Check inventory soon</p>
    </div>
</div>


</div>


<!-- Main Content Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Left: Sales Summary Chart -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales Summary</h2>
        <div class="h-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
            <p class="text-gray-400">📊 Chart will appear here</p>
        </div>
    </div>

 <!-- Right: Low Stock / Reorder Products -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Low Stock Products</h2>

    <div class="grid grid-cols-3 gap-6 text-center">
        <!-- Product 1: Tarpaulin -->
        <div class="flex flex-col items-center gap-2 p-3 border border-gray-200 rounded-lg">
            <div class="text-gray-700 font-medium">Tarpaulin</div>
            <div class="text-gray-500">Current Stock: 1</div>
            <button 
                title="Re Order"
                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2 a10 10 0 1 1-9.95 9.95" />
                    <polygon points="-2,12 4,9 4,15" fill="currentColor" transform="translate(1,0) rotate(-25 1 12)"/>
                </svg>
            </button>
        </div>

        <!-- Product 2: Mugs -->
        <div class="flex flex-col items-center gap-2 p-3 border border-gray-200 rounded-lg">
            <div class="text-gray-700 font-medium">Mugs</div>
            <div class="text-gray-500">Current Stock: 2</div>
            <button 
                title="Re Order"
                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2 a10 10 0 1 1-9.95 9.95" />
                    <polygon points="-2,12 4,9 4,15" fill="currentColor" transform="translate(1,0) rotate(-25 1 12)"/>
                </svg>
            </button>
        </div>

        <!-- Product 3: Plain T-Shirt -->
        <div class="flex flex-col items-center gap-2 p-3 border border-gray-200 rounded-lg">
            <div class="text-gray-700 font-medium">Plain T-Shirt</div>
            <div class="text-gray-500">Current Stock: 3</div>
            <button 
                title="Re Order"
                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2 a10 10 0 1 1-9.95 9.95" />
                    <polygon points="-2,12 4,9 4,15" fill="currentColor" transform="translate(1,0) rotate(-25 1 12)"/>
                </svg>
            </button>
        </div>

        <!-- Product 4: Plain Jersey -->
        <div class="flex flex-col items-center gap-2 p-3 border border-gray-200 rounded-lg">
            <div class="text-gray-700 font-medium">Plain Jersey</div>
            <div class="text-gray-500">Current Stock: 4</div>
            <button 
                title="Re Order"
                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2 a10 10 0 1 1-9.95 9.95" />
                    <polygon points="-2,12 4,9 4,15" fill="currentColor" transform="translate(1,0) rotate(-25 1 12)"/>
                </svg>
            </button>
        </div>

        <!-- Product 5: Sling -->
        <div class="flex flex-col items-center gap-2 p-3 border border-gray-200 rounded-lg">
            <div class="text-gray-700 font-medium">Sling</div>
            <div class="text-gray-500">Current Stock: 5</div>
            <button 
                title="Re Order"
                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2 a10 10 0 1 1-9.95 9.95" />
                    <polygon points="-2,12 4,9 4,15" fill="currentColor" transform="translate(1,0) rotate(-25 1 12)"/>
                </svg>
            </button>
        </div>
    </div>
</div>

</div>
@endsection
