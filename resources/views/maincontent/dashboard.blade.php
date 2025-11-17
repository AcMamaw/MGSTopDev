@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Dashboard Header (Styled Consistently) -->
<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex items-center space-x-2 text-xl font-bold text-red-600">
        </div>
    </div>
<p class="text-gray-600 mt-2">
    Welcome back, {{ auth()->user()->employee->fname ?? '' }}! Here's what’s happening in your MGS system today.
</p>
</header>


<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-gray-500 text-sm font-medium mb-2">Total Medicines</h2>
        <p class="text-3xl font-semibold text-gray-900">128</p>
        <p class="text-sm text-green-500 mt-1">↑ 8% from last week</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-gray-500 text-sm font-medium mb-2">Total Suppliers</h2>
        <p class="text-3xl font-semibold text-gray-900">12</p>
        <p class="text-sm text-green-500 mt-1">↑ 2 new this month</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-gray-500 text-sm font-medium mb-2">Sales Today</h2>
        <p class="text-3xl font-semibold text-gray-900">₱3,240</p>
        <p class="text-sm text-red-500 mt-1">↓ 5% from yesterday</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-gray-500 text-sm font-medium mb-2">Low Stock Alerts</h2>
        <p class="text-3xl font-semibold text-gray-900">6</p>
        <p class="text-sm text-yellow-500 mt-1">Check inventory soon</p>
    </div>
</div>

<!-- Charts and Activity Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left: Chart placeholder -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales Summary</h2>
        <div class="h-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
            <p class="text-gray-400">📊 Chart will appear here</p>
        </div>
    </div>

    <!-- Right: Recent Activity -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
        <ul class="divide-y divide-gray-200">
            <li class="py-3 flex justify-between">
                <span class="text-gray-700">Supplier <strong>MedLife</strong> added new stock</span>
                <span class="text-sm text-gray-500">2h ago</span>
            </li>
            <li class="py-3 flex justify-between">
                <span class="text-gray-700">3 medicines marked as expired</span>
                <span class="text-sm text-gray-500">5h ago</span>
            </li>
            <li class="py-3 flex justify-between">
                <span class="text-gray-700">Order #2025-015 confirmed</span>
                <span class="text-sm text-gray-500">1d ago</span>
            </li>
        </ul>
    </div>
</div>
@endsection
