<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGS</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@jaames/iro/dist/iro.min.js"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

<style>
    [x-cloak] { display: none !important; }
</style>


<style>
    /* Ensures the layout spans the full viewport height */
body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    display: flex;
    min-height: 100vh;
    --sidebar-width: 16rem;
}

/* Sidebar width control */
.sidebar-collapsed #sidebar {
    --sidebar-width: 5rem;
}

#sidebar {
    width: var(--sidebar-width);
    transition: width 0.3s ease-in-out;
}

/* Hide text labels when collapsed */
.sidebar-collapsed .nav-text,
.sidebar-collapsed .logo-text,
.sidebar-collapsed .logout-text {
    display: none;
}

/* Center icons when collapsed */
.sidebar-collapsed .nav-link,
.sidebar-collapsed button[onclick^="toggleDropdown"] {
    justify-content: center !important;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    transition: all 0.3s ease-in-out;
}

.sidebar-collapsed .logout-link {
    justify-content: center;
}

/* Adjust spacing for collapsed mode */
.sidebar-collapsed .space-x-3 {
    margin-right: 0;
}

/* Dropdown styling while collapsed */
.sidebar-collapsed button[onclick^="toggleDropdown"] {
    opacity: 1;
    color: #000000; /* 🖤 black icons when collapsed */
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

/* 🌤 Hover effect (sky blue instead of red) */
.sidebar-collapsed button[onclick^="toggleDropdown"]:hover {
    background-color: #e0f2fe; /* sky-100 */
    transform: scale(1.05);
    width: calc(100% + -2px); 
    padding-left: -3px;
    padding-right: 4px;
    color: #0284c7; /* sky-600 text color on hover */
}

.sidebar-collapsed button[onclick^="toggleDropdown"]:hover svg:first-child {
    transform: translateX(3px);
    transition: transform 0.3s ease;
}

/* Center the management folder icon correctly when collapsed */
.sidebar-collapsed button[onclick^="toggleDropdown"] svg.w-5 {
    margin-left: 2px;
}


/* Hide dropdown menus and arrows while collapsed */
.sidebar-collapsed #fileDropdown,
.sidebar-collapsed #arrow-fileDropdown {
    display: none !important;
}

/* Smooth transition for all sidebar items */
.nav-link,
button[onclick^="toggleDropdown"] {
    transition: all 0.3s ease-in-out;
    color: #000000; /* 🖤 black text and icons */
}

/* Normal state: icon slightly left */
.sidebar-collapsed .nav-link svg:first-child {
    transform: translateX(-2px);
    transition: transform 0.2s ease;
    color: #000000; /* ensure icons are black */
}

/* Hover state: icon moves right */
.sidebar-collapsed .nav-link:hover svg:first-child {
    transform: translateX(2px);
}

/* 🌤 Hover effect for the link background */
.sidebar-collapsed .nav-link:hover {
    background-color: #e0f2fe; /* sky-100 */
    color: #0284c7; /* sky-600 text color */
    transform: scale(1.04);
    transition: all 0.2s ease;
}

/* Custom scrollbar for content area */
.content-area::-webkit-scrollbar {
    width: 8px;
}

.content-area::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.content-area::-webkit-scrollbar-track {
    background-color: #f1f5f9;
}
</style>


</head>
<body class="bg-gray-100">

<div id="sidebar" 
    class="w-64 bg-white text-gray-800 flex flex-col shadow-2xl transition-width duration-300 h-screen"
    style="width: var(--sidebar-width);">


 <!-- 🔧 Responsive logo section -->
<div class="flex items-center justify-center h-20 border-b border-sky-100">
    <img 
        src="{{ asset('images/ace.jpg') }}" 
        alt="AMMS Logo" 
        class="transition-all duration-300 object-contain max-w-full max-h-full"
        :class="sidebarOpen ? 'w-10 h-10' : 'w-[10px] h-[18px]'"
    >
</div>

        
<style>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #1b1b1bff;
  border-radius: 10px;
}

.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background-color: #333;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #1b1b1bff transparent;
}
</style>

        
<!-- 🧭 Scrollable Menu Section -->
<nav class="flex-1 overflow-y-auto p-4 custom-scrollbar">
    <ul class="space-y-2">
        <!-- 🏠 Dashboard (All roles) -->
        <li>
            <a href="{{ route('dashboard') }}"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                {{ request()->routeIs('dashboard') 
                    ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                    : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="w-6 h-6 flex-shrink-0">
                    <path d="M3 11L12 3l9 8"/>
                    <path d="M5 10v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V10"/>
                    <path d="M9 21V12h6v9"/>
                </svg>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>

        @php
            $role = auth()->user()->employee->role->role_name;
        @endphp

        <!-- (Staff) -->
        @if(in_array($role, ['All Around Staff']))

       <!-- 📦 Inventory Dropdown -->
        <li class="relative">
            <button 
                onclick="toggleDropdown('inventoryDropdown')" 
                class="w-full flex items-center justify-between p-3 rounded-lg text-sm font-medium transition duration-150
                text-gray-800 hover:bg-sky-100 hover:text-sky-700">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">

                        <!-- Box -->
                        <path d="M3 7l9-4 9 4v10l-9 4-9-4z" />
                        <path d="M3 7l9 4 9-4" />
                        <path d="M12 21V11" />

                        <!-- Bigger Arrow, moved upward -->
                        <path d="M12 -1v7" />
                        <path d="M16 3L12 7 8 3" />

                    </svg>
                  <span class="nav-text whitespace-nowrap">Inventory</span>
                </div>
                <svg id="arrow-inventoryDropdown" 
                    class="w-4 h-4 ml-2 transition-transform duration-200 
                    {{ request()->routeIs('inventory.*') ? 'rotate-180' : '' }}" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <ul id="inventoryDropdown" 
                class="{{ request()->routeIs('inventory.*') ? 'block' : 'hidden' }} 
                mt-1 bg-white rounded-lg shadow-lg overflow-hidden text-sm">
                 <li>
                    <a href="{{ route('stock') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('inventory.stockentry') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M3 7l9-4 9 4v10l-9 4-9-4z" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 7l9 4 9-4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 21V11" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Total Stocks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('outstock') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('inventory.stockout') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">

                            <!-- Box -->
                            <path d="M3 7l9-4 9 4v10l-9 4-9-4z" />
                            <path d="M3 7l9 4 9-4" />
                            <path d="M12 21V11" />

                        <!-- Straight X moved right and higher -->
                            <path d="M15 12.5l4 4M15 16.1l4-4" />
                         </svg>
                        <span>Out Stocks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stockadjustment') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('inventory.stockadjustment') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <!-- Sliders Icon (matches uploaded design) -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            class="w-5 h-5 flex-shrink-0" 
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5 ">
                            <line x1="4" y1="6" x2="20" y2="6" stroke-linecap="round"/>
                            <circle cx="8" cy="6" r="1.5" fill="currentColor"/>
                            
                            <line x1="4" y1="12" x2="20" y2="12" stroke-linecap="round"/>
                            <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                            
                            <line x1="4" y1="18" x2="20" y2="18" stroke-linecap="round"/>
                            <circle cx="16" cy="18" r="1.5" fill="currentColor"/>
                        </svg>
                        <span>Stock Adjustment</span>
                    </a>
                </li>
            </ul>
        </li>

            <li>
                <a href="{{ route('instock') }}"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                {{ request()->routeIs('inventory.stockentry') 
                    ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                    : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                   <!-- Icon always visible -->
                    <svg class="w-6 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7l9-4 9 4v10l-9 4-9-4z" />
                        <path d="M3 7l9 4 9-4" />
                        <path d="M12 21V11" />
                        <path d="M14.5 13.6l1.65 1.65 2.85-3" />
                    </svg>                   
                    <span class="nav-text">In Stock</span>
                </a>
            </li> 

            <!-- 🚚 Delivery -->
            <li>
                <a href="{{ route('delivery') }}"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                {{ request()->routeIs('delivery.index') 
                    ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                    : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    <!-- Truck / Delivery icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="w-6 h-6 flex-shrink-0" 
                        fill="none" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7h13v10H3zM16 10h4l1 2v5h-5z" />
                        <circle cx="7.5" cy="17.5" r="1.5" />
                        <circle cx="17.5" cy="17.5" r="1.5" />
                    </svg>
                    <span class="nav-text">Deliveries   </span>
                </a>
            </li>
            @endif

       <!-- (Cashier Only) -->
        @if($role === 'Cashier')
    <!-- 🧰 Manage Store Dropdown -->
        <li class="relative">
            <button 
                onclick="toggleDropdown('maintenanceDropdown')" 
                class="w-full flex items-center justify-between p-3 rounded-lg text-sm font-medium transition duration-150
                text-gray-800 hover:bg-sky-100 hover:text-sky-700">
                <div class="flex items-center space-x-3">
                     <!-- Storefront Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 flex-shrink-0"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9l1.5-5h15L21 9M4 9h16v11a1 1 0 01-1 1H5a1 1 0 01-1-1V9zm3 11V13h10v7" />
                    </svg>
                    <span class="nav-text whitespace-nowrap">Manage Store</span>
                </div>
                <svg id="arrow-maintenanceDropdown" 
                    class="w-4 h-4 ml-2 transition-transform duration-200" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <ul id="maintenanceDropdown" 
                class="{{ request()->routeIs('maintenance.*') ? 'block' : 'hidden' }} 
                    mt-1 bg-white rounded-lg shadow-lg overflow-hidden text-sm">
                <li>
                    <a href="{{ route('product') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('maintenance.products') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 7h12l-1 12H7L6 7z" /> <!-- bag outline -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7V5a3 3 0 016 0v2" /> <!-- handles -->
                        </svg>
                        <span>Products</span>
                    </a>
                </li>
               <li>
                    <a href="{{ route('category') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('category.index') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Top-left square -->
                            <rect x="3" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Top-right square -->
                            <rect x="13" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-left square -->
                            <rect x="3" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-right square -->
                            <rect x="13" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        <span>Categories</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- 🏷️ Sales -->
        <li>
            <a href="{{ route('purchaseorder') }}"
               class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
               {{ request()->routeIs('inventory.purchaseorder') 
                   ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                   : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                <!-- Shopping Cart Icon (Outline) -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 flex-shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6H19m-6 0a1 1 0 11-2 0m2 0a1 1 0 01-2 0" />
                </svg>
                <span class="nav-text"> Sales</span>
            </a>
        </li>

            <!-- 📊 Reports -->
            <li>
                <a href="{{ route('reports') }}"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                {{ request()->routeIs('reports.index') 
                    ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                    : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    <!-- Clipboard with checkmarks icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="w-5 h-5 flex-shrink-0" 
                        fill="none" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 2h6a2 2 0 012 2h1a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h1a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 8h6M9 12h6M9 16h6M7 12l1 1 2-2M7 16l1 1 2-2" />
                    </svg>
                    <span class="nav-text">Reports</span>
                </a>
            </li>


           <!-- Request -->
                <li>
                    <a href="{{ route('request') }}" 
                    class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                    {{ request()->routeIs('request') 
                        ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                        : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    
                        <!-- Request / Paper Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            class="w-5 h-5 flex-shrink-0" 
                            fill="none" viewBox="0 0 24 24" 
                            stroke="currentColor" stroke-width="1.9">
                            <!-- Message bubble -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4h16v12H6l-2 2V4z" />
                            <!-- Lines inside the message -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 7h12M6 11h8" />
                        </svg>

                        <span class="nav-text">Requests</span>
                    </a>
                </li>
        @endif


        <!--  (Admin Only) -->
        @if($role === 'Admin')
          <!-- 📁 Management Dropdown -->
        <li class="relative">
            <button 
                onclick="toggleDropdown('managementDropdown')" 
                class="w-full flex items-center justify-between p-3 rounded-lg text-sm font-medium 
                    transition duration-150 text-gray-800 hover:bg-sky-100 hover:text-sky-700">

                <!-- Left Section: Folder Icon + Label -->
                <div class="flex items-center space-x-3">
                    <!-- Folder Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                    <span class="nav-text whitespace-nowrap">Management</span>
                </div>

                <!-- Dropdown Arrow -->
                <svg id="arrow-managementDropdown" 
                    class="w-4 h-4 ml-2 transition-transform duration-200" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Items -->
            <ul id="managementDropdown"
                class="{{ request()->routeIs('management.supplier') || request()->routeIs('employee.index') ? 'block' : 'hidden' }} 
                    mt-1 bg-white rounded-lg shadow-lg overflow-hidden text-sm">

                <!-- 🧱 Suppliers -->
                <li>
                    <a href="{{ route('supplier') }}" 
                    class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                            {{ request()->routeIs('management.supplier') 
                                ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                                : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                                        class="w-5 h-5 flex-shrink-0">
                                    <!-- Head -->
                                    <circle cx="12" cy="7" r="4" />
                                    <!-- Shoulders -->
                                    <path d="M4 20c0-4 4-7 8-7" />
                                        <!-- Main cube -->
                                        <path d="M13 14l4-2 4 2v4l-4 2-4-2v-4z" stroke-linecap="round" stroke-linejoin="round"/>
                                        <!-- Top face -->
                                        <path d="M13 14l4 2 4-2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <!-- Vertical line -->
                                    </svg>
                        <span>Suppliers</span>
                    </a>
                </li>

                <!-- 👤 Employee -->
                <li>
                    <a href="{{ route('employee') }}"
                    class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                            {{ request()->routeIs('employee.index') 
                                ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                                : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">

                        <svg xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                            class="w-5 h-5 flex-shrink-0">
                            <!-- Head -->
                            <circle cx="12" cy="7" r="4" />
                            <!-- Shoulders -->
                            <path d="M4 20c0-4 4-7 8-7s8 3 8 7" />
                        </svg>

                        <span>Employee</span>
                    </a>
                </li>

                <!-- 👥 Customer -->
                <li>
                    <a href="{{ route('customer') }}"
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                            {{ request()->routeIs('customer.index') 
                                ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                                : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                            class="w-5 h-5 flex-shrink-0">
                            
                            <!-- Center user -->
                            <circle cx="12" cy="7" r="3"/>
                            <path d="M6 20v-2c0-2 3-4 6-4s6 2 6 4v2H6z"/>

                            <!-- Left user -->
                            <circle cx="5" cy="10" r="2.5"/>
                            <path d="M1 20v-2c0-1.5 2.5-3 5-3"/>

                            <!-- Right user -->
                            <circle cx="19" cy="10" r="2.5"/>
                            <path d="M23 20v-2c0-1.5-2.5-3-5-3"/>
                        </svg>
                        <span>Customer</span>
                    </a>
                </li>

                <!-- ⚙️ Roles -->
                <li>
                    <a href="{{ route('role') }}"
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                            {{ request()->routeIs('roles.index') 
                                ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                                : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                           <svg xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="w-5 h-4 flex-shrink-0">
                                
                                <!-- Outer gear shape -->
                                <path d="M12 15.5a3.5 3.5 0 1 0 0-7a3.5 3.5 0 0 0 0 7z"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33a1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        <span>Roles</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- 🧰 Manage Store Dropdown -->
        <li class="relative">
            <button 
                onclick="toggleDropdown('maintenanceDropdown')" 
                class="w-full flex items-center justify-between p-3 rounded-lg text-sm font-medium transition duration-150
                text-gray-800 hover:bg-sky-100 hover:text-sky-700">
                <div class="flex items-center space-x-3">
                     <!-- Storefront Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 flex-shrink-0"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9l1.5-5h15L21 9M4 9h16v11a1 1 0 01-1 1H5a1 1 0 01-1-1V9zm3 11V13h10v7" />
                    </svg>
                    <span class="nav-text whitespace-nowrap">Manage Store</span>
                </div>
                <svg id="arrow-maintenanceDropdown" 
                    class="w-4 h-4 ml-2 transition-transform duration-200" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <ul id="maintenanceDropdown" 
                class="{{ request()->routeIs('maintenance.*') ? 'block' : 'hidden' }} 
                    mt-1 bg-white rounded-lg shadow-lg overflow-hidden text-sm">
                <li>
                    <a href="{{ route('product') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('maintenance.products') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 7h12l-1 12H7L6 7z" /> <!-- bag outline -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7V5a3 3 0 016 0v2" /> <!-- handles -->
                        </svg>
                        <span>Products</span>
                    </a>
                </li>
                   <li>
            </li>
               <li>
                    <a href="{{ route('category') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('category.index') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Top-left square -->
                            <rect x="3" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Top-right square -->
                            <rect x="13" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-left square -->
                            <rect x="3" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-right square -->
                            <rect x="13" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        <span>Categories</span>
                    </a>
                </li>
            </ul>
        </li>

         <!-- 📊 Reports -->
            <li>
                <a href="{{ route('reports') }}"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                {{ request()->routeIs('reports.index') 
                    ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                    : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    <!-- Clipboard with checkmarks icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="w-5 h-5 flex-shrink-0" 
                        fill="none" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 2h6a2 2 0 012 2h1a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h1a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 8h6M9 12h6M9 16h6M7 12l1 1 2-2M7 16l1 1 2-2" />
                    </svg>
                    <span class="nav-text">Reports</span>
                </a>
            </li>
             <!-- Request -->
                <li>
                    <a href="{{ route('request') }}" 
                    class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                    {{ request()->routeIs('request') 
                        ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                        : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    
                        <!-- Request / Paper Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            class="w-5 h-5 flex-shrink-0" 
                            fill="none" viewBox="0 0 24 24" 
                            stroke="currentColor" stroke-width="1.9">
                            <!-- Message bubble -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4h16v12H6l-2 2V4z" />
                            <!-- Lines inside the message -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 7h12M6 11h8" />
                        </svg>

                        <span class="nav-text">Requests</span>
                    </a>
                </li>
        @endif


 <!-- (Layout Artist Only) -->
        @if($role === 'Layout Artist')
    <!-- 🧰 Manage Store Dropdown -->
        <li class="relative">
            <button 
                onclick="toggleDropdown('maintenanceDropdown')" 
                class="w-full flex items-center justify-between p-3 rounded-lg text-sm font-medium transition duration-150
                text-gray-800 hover:bg-sky-100 hover:text-sky-700">
                <div class="flex items-center space-x-3">
                     <!-- Storefront Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 flex-shrink-0"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9l1.5-5h15L21 9M4 9h16v11a1 1 0 01-1 1H5a1 1 0 01-1-1V9zm3 11V13h10v7" />
                    </svg>
                    <span class="nav-text whitespace-nowrap">Manage Store</span>
                </div>
                <svg id="arrow-maintenanceDropdown" 
                    class="w-4 h-4 ml-2 transition-transform duration-200" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <ul id="maintenanceDropdown" 
                class="{{ request()->routeIs('maintenance.*') ? 'block' : 'hidden' }} 
                    mt-1 bg-white rounded-lg shadow-lg overflow-hidden text-sm">
                <li>
                    <a href="{{ route('product') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('maintenance.products') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 7h12l-1 12H7L6 7z" /> <!-- bag outline -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7V5a3 3 0 016 0v2" /> <!-- handles -->
                        </svg>
                        <span>Products</span>
                    </a>
                </li>
               <li>
                    <a href="{{ route('category') }}" 
                        class="block px-4 py-2 flex items-center space-x-2 rounded-lg text-sm font-medium transition duration-150
                        {{ request()->routeIs('category.index') 
                            ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                            : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Top-left square -->
                            <rect x="3" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Top-right square -->
                            <rect x="13" y="3" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-left square -->
                            <rect x="3" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <!-- Bottom-right square -->
                            <rect x="13" y="13" width="8" height="8" rx="1" ry="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        <span>Categories</span>
                    </a>
                </li>
            </ul>
        </li>   
            <!-- Production Flow -->
                <li>
                  <a href="{{ route('joborders') }}" 
                    class="nav-link flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition duration-150
                    {{ request()->routeIs('request') 
                        ? 'bg-sky-100 text-sky-700 shadow-md hover:bg-sky-200' 
                        : 'text-gray-800 hover:bg-sky-100 hover:text-sky-700' }}">
                    
                        <!-- Job Order Icon - Wider Paper -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            class="w-5 h-5 flex-shrink-0" 
                            fill="none" viewBox="0 0 28 24" 
                            stroke="currentColor" stroke-width="1.9">
                        <!-- Wider Paper/document outline -->
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 2H22C23.1046 2 24 2.89543 24 4V20C24 21.1046 23.1046 22 22 22H6C4.89543 22 4 21.1046 4 20V4C4 2.89543 4.89543 2 6 2Z" />
                        <!-- Lines inside the paper -->
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7H20M8 11H20M8 15H16" />
                        <!-- Optional check mark for “job done” -->
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 18L13 21L19 16" />
                        </svg>

                        <span class="nav-text">Job Order</span>
                    </a>
                </li>
        @endif
    </ul>
</nav>

      <!-- 🚪 Fixed Logout Button -->
        <div class="p-4 border-t border-gray-200 bg-white sticky bottom-0">
           <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="logout-link flex items-center space-x-3 p-3 rounded-lg text-sm font-semibold text-white bg-gray-800 hover:bg-pink-600 hover:text-white transition duration-150 w-full text-left">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="logout-text whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 2. Content Area Container -->
    <div class="flex-1 flex flex-col max-h-screen overflow-hidden">
        
       <!-- Top Bar -->
<header class="bg-sky-100 p-[1.23rem] shadow-md flex justify-between items-center z-10 sticky top-0">
            
            <!-- Sidebar Toggle Button and Title -->
            <div class="flex items-center space-x-4">
                <button id="sidebarToggle" 
                    class="text-gray-500 hover:text-sky-400 transition duration-150 focus:outline-none p-2 rounded-md hover:bg-sky-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path id="menuIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
                <h1 class="text-xl font-semibold text-gray-800">Mariviles Graphic Studio</h1>
            </div>
            
            <!-- User Profile Section -->
            <div class="flex items-center space-x-3">

                <div class="text-right hidden sm:block profile-details">
                    <!-- Display Employee Name -->
                    <div class="font-medium text-gray-800">
                        {{ auth()->user()->employee->fname ?? '' }} 
                        {{ auth()->user()->employee->lname ?? '' }}
                    </div>

                    <!-- Role Name -->
                    <div class="text-xs text-pink-600 font-semibold">
                        ({{ auth()->user()->employee->role->role_name ?? 'No Role' }})
                    </div>
                </div>

                <!-- Avatar Initials -->
                <div class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center 
                            text-white font-bold text-lg shadow-md flex-shrink-0">

                    @php
                        $emp = auth()->user()->employee;

                        $initials = '';
                        if ($emp) {
                            $first = $emp->fname ? strtoupper(substr($emp->fname, 0, 1)) : '';
                            $last  = $emp->lname ? strtoupper(substr($emp->lname, 0, 1)) : '';
                            $initials = $first . $last;
                        }
                    @endphp

                    {{ $initials }}
                </div>

            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-6 overflow-y-auto content-area">
            @yield('content')
        </main>
    </div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebarToggle = document.getElementById('sidebarToggle');

    const dropdownIds = [
        'managementDropdown',
        'maintenanceDropdown',
        'inventoryDropdown'
    ];

    // ✅ Sidebar toggle button
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-collapsed');
            dropdownIds.forEach(id => {
                const dropdown = document.getElementById(id);
                const arrow = document.getElementById(`arrow-${id}`);
                if (dropdown && arrow) {
                    dropdown.classList.add('hidden');
                    dropdown.classList.remove('block');
                    arrow.classList.remove('rotate-180');
                }
            });
        });
    }

    // ✅ Collapse sidebar by default on small screens
    if (window.innerWidth < 768) {
        body.classList.add('sidebar-collapsed');
    }

    // ✅ Dropdown toggle
    window.toggleDropdown = function(id) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(`arrow-${id}`);

        if (body.classList.contains('sidebar-collapsed')) {
            body.classList.remove('sidebar-collapsed');
            setTimeout(() => {
                dropdown.classList.toggle('hidden');
                dropdown.classList.toggle('block');
                arrow.classList.toggle('rotate-180');
            }, 300);
        } else {
            dropdown.classList.toggle('hidden');
            dropdown.classList.toggle('block');
            arrow.classList.toggle('rotate-180');
        }
    };

    // ✅ New behavior: expand smoothly + navigate
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (event) => {
            // Get link destination
            const href = link.getAttribute('href');

            // Only trigger if sidebar is collapsed and link has a valid route
            if (body.classList.contains('sidebar-collapsed') && href && href !== '#') {
                event.preventDefault(); // Stop immediate navigation
                body.classList.remove('sidebar-collapsed'); // Expand sidebar

                // Wait for smooth transition, then navigate
                setTimeout(() => {
                    window.location.href = href;
                }, 350); // Matches sidebar animation duration
            }
        });
    });

    // ✅ Auto-close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
        const isDropdownButton = event.target.closest('button[onclick^="toggleDropdown"]');
        const isInsideDropdown = dropdownIds.some(id => event.target.closest(`#${id}`));

        if (!isDropdownButton && !isInsideDropdown) {
            dropdownIds.forEach(id => {
                const dropdown = document.getElementById(id);
                const arrow = document.getElementById(`arrow-${id}`);
                if (dropdown && arrow) {
                    dropdown.classList.add('hidden');
                    dropdown.classList.remove('block');
                    arrow.classList.remove('rotate-180');
                }
            });
        }
    });
});
</script>

</body>
</html>
