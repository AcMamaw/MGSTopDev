@extends('layouts.app')

@section('title', 'Stock In')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <header class="mb-8">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">In Stocks</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage stock-in records</p>
    </header>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Controls -->
    <div class="max-w-7xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4">

        <!-- Search Input with Icon + Filter -->
        <div x-data="{
                searchQuery: '',
                placeholderIndex: 0,
                placeholders: [
                    'Search Stock-in',
                    'Search Stock ID',
                    'Product Name',
                    'Supplier',
                    'Added By',
                    'Date Added'
                ],
                nextPlaceholder() {
                    this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
                }
            }"
            class="relative w-full md:w-1/4"
        >
            <!-- Search Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>

            <!-- Input -->
            <input type="text"
                   x-model="searchQuery"
                   :placeholder="placeholders[placeholderIndex]"
                   @input="searchStockin()"
                   class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-xl text-sm
                          focus:ring-2 focus:ring-black focus:outline-none" />

            <!-- Filter Icon -->
            <button type="button"
                    @click="nextPlaceholder()"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    title="Change Filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 2 L16 2 L10 10 L10 16 L6 16 L6 10 Z"/>
                </svg>
            </button>
        </div>

        <!-- Buttons (right side) -->
        <div class="flex gap-2">
            <!-- Add Button -->
            <button onclick="openStockModal()"
                class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold hover:bg-yellow-500 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Add New Stock</span>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white p-6 rounded-xl shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stock In ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Quantity</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Inputed By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Stocked Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stockins as $si)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-600 font-medium">
                        SI{{ str_pad($si->stockin_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-2 text-center text-gray-600">
                        {{ $si->product->product_name ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->quantity_product }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($si->unit_cost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($si->total, 2) }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $si->employee->fname ?? '' }} {{ $si->employee->lname ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $si->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- ADD STOCK IN MODAL -->
<div id="addStockModal" 
    class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-0 invisible transition-all duration-300">

    <div id="stockModalBox"
        class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Stock In</h2>

        <form method="POST" action="{{ route('instock.store') }}">
            @csrf

            <!-- Product with Add Button -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product</label>
                <div class="flex gap-2">
                    <select id="productSelect" name="product_id" required 
                        class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>

                    <button type="button"
                        onclick="openAddProductModal()"
                        class="px-4 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold flex-shrink-0">
                        Add
                    </button>
                </div>
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Quantity</label>
                <input type="number" name="quantity_product" min="1" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    value="1">
            </div>

            <!-- Unit Cost -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Unit Cost</label>
                <input type="number" name="unit_cost" min="0" step="0.01" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    value="0.00">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeStockModal()"
                    class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 font-semibold">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ADD PRODUCT MODAL -->
<div id="addProductModal" 
    class="fixed inset-0 z-[60] items-center justify-center bg-black bg-opacity-0 invisible transition-all duration-300">

    <div id="addProductModalBox"
        class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl transform scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Product</h2>

        <form id="addProductForm">
            @csrf

            <!-- Product Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                <input type="text" name="product_name" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"></textarea>
            </div>

            <!-- Unit -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Unit</label>
                <input type="text" name="unit" placeholder="e.g., pcs, kg, box, liter" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeAddProductModal()"
                    class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </button>

                <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-black rounded-lg hover:bg-yellow-500 font-semibold">
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    // ==================== STOCK IN MODAL ====================
    function openStockModal() {
        const modal = document.getElementById('addStockModal');
        const box = document.getElementById('stockModalBox');

        modal.classList.remove('invisible');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            box.classList.remove('scale-90', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeStockModal() {
        const modal = document.getElementById('addStockModal');
        const box = document.getElementById('stockModalBox');

        modal.classList.add('bg-opacity-0');
        modal.classList.remove('bg-opacity-50');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-90', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('invisible');
            modal.classList.remove('flex');
        }, 300);
    }

    // ==================== ADD PRODUCT MODAL ====================
    function openAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const box = document.getElementById('addProductModalBox');

        modal.classList.remove('invisible');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            box.classList.remove('scale-90', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const box = document.getElementById('addProductModalBox');

        modal.classList.add('bg-opacity-0');
        modal.classList.remove('bg-opacity-50');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-90', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('invisible');
            modal.classList.remove('flex');
        }, 300);
    }

    // ==================== AJAX FORM SUBMISSION ====================
    document.addEventListener('DOMContentLoaded', function() {
        const addProductForm = document.getElementById('addProductForm');
        
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.textContent = 'Adding...';

            fetch('{{ route("instock.product.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Add new product to the select dropdown in Stock In modal
                const productSelect = document.getElementById('productSelect');
                const newOption = new Option(data.product_name, data.product_id, true, true);
                productSelect.add(newOption);

                // Close the add product modal
                closeAddProductModal();

                // Reset form
                addProductForm.reset();

                // Show success message
                showSuccessMessage('Product added successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    });

    // ==================== SUCCESS MESSAGE ====================
    function showSuccessMessage(message) {
        // Create success message element
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-[70] transform translate-x-0 transition-transform duration-300';
        successDiv.textContent = message;
        
        document.body.appendChild(successDiv);
        
        // Slide in animation
        setTimeout(() => {
            successDiv.classList.add('translate-x-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            successDiv.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(successDiv);
            }, 300);
        }, 3000);
    }
</script>

@endsection