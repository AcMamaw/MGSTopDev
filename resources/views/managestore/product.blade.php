@extends('layouts.app')

@section('title', 'Products')

@section('content')
{{-- Prevent modal flashing --}}
<style>[x-cloak] { display: none !important; }</style>

<div x-data="{
    showAddProductModal: false,
    supplierId: '',
    productName: '',
    description: '',
    price: '',
    unit: ''
}">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Products</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage product records including supplier, price, unit, and status.</p>
</header>

<!-- Message Container (Dynamic) -->
<div id="message-container" class="max-w-7xl mx-auto mb-6" style="display: none;">
    <div id="message-content" class="px-4 py-3 rounded"></div>
</div>

<!-- Success Message (Server-side - for page reloads) -->
@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto">
    {{ session('success') }}
</div>
@endif

<!-- Error Messages (Server-side - for page reloads) -->
@if($errors->any())
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-7xl mx-auto">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
    

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
    <!-- Search -->
    <div class="relative w-full md:w-1/4">
        <input type="text" id="product-search" placeholder="Search products"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
    </div>

    <!-- Add Product Button -->
    <button @click="showAddProductModal = true"
        class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-plus">
            <path d="M12 5v14" />
            <path d="M5 12h14" />
        </svg>
        <span>Add New Product</span>
    </button>
</div>

<!-- Products Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="product-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Unit</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="product-table-body" class="divide-y divide-gray-100">
                @foreach ($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        P{{ str_pad($product->product_id,3,'0',STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->supplier->supplier_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->product_name }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->description }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $product->unit }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </button>
                            <button title="Archive" onclick="deleteRow(this)"
                                class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-archive">
                                    <path d="M3 4h18v4H3z" />
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                    <path d="M10 12h4" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div x-show="showAddProductModal" x-cloak x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div @click.away="showAddProductModal = false" x-transition
        class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Product</h2>

        <div class="space-y-4">

            <!-- Supplier -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Supplier</label>
                <select x-model="supplierId"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <option value="" disabled selected>Select a supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                <input type="text" x-model="productName"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Enter product name">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <input type="text" x-model="description"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Enter description">
            </div>

            <!-- Unit -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Unit</label>
                <input type="text" x-model="unit"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Enter unit">
            </div>

        </div>

        <div class="mt-6 flex justify-end gap-3">

            <!-- Cancel -->
            <button @click="showAddProductModal = false"
                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                Cancel
            </button>

            <button 
            @click="if(productName && supplierId && unit){

                    fetch('{{ route('products.store') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type':'application/json', 
                            'X-CSRF-TOKEN':'{{ csrf_token() }}' 
                        },
                        body: JSON.stringify({ 
                            supplier_id: supplierId, 
                            product_name: productName, 
                            description: description, 
                            unit: unit 
                        })
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(err => Promise.reject(err));
                        }
                        return res.json();
                    })
                    .then(data => {

                        const tbody = document.getElementById('product-table-body');
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';

                        row.innerHTML = `
                            <td class='px-4 py-3 text-center font-medium text-gray-800'>
                                P${String(data.product_id).padStart(3,'0')}
                            </td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.supplier_name}</td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.product_name}</td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.unit}</td>
                            <td class='px-4 py-3 text-center'>
                                <div class='flex items-center justify-center space-x-2'>
                                    <button title='Edit' class='p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                            <path d='M12 20h9' />
                                            <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z' />
                                        </svg>
                                    </button>
                                    <button title='Archive' onclick='deleteRow(this)' class='p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='22' height='25' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                            <path d='M3 4h18v4H3z' />
                                            <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8' />
                                            <path d='M10 12h4' />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        `;

                        tbody.appendChild(row);

                        // Reset Fields
                        supplierId = '';
                        productName = '';
                        description = '';
                        unit = '';
                        showAddProductModal = false;

                        // Show Success Message
                        showMessage('Product added successfully!', 'success');

                        updateProductPagination();

                    })
                    .catch(err => {
                        console.error(err);
                        let errorMsg = 'Error saving product';
                        if (err.errors) {
                            errorMsg = Object.values(err.errors).flat().join(', ');
                        } else if (err.message) {
                            errorMsg = err.message;
                        }
                        showMessage(errorMsg, 'error');
                    });

                } else {
                    showMessage('Please fill all required fields.', 'error');
                }
            "
            class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const content = document.getElementById('message-content');
    
    // Reset classes
    content.className = 'px-4 py-3 rounded';
    
    if (type === 'success') {
        content.className += ' bg-green-100 border border-green-400 text-green-700';
    } else if (type === 'error') {
        content.className += ' bg-red-100 border border-red-400 text-red-700';
    }
    
    content.textContent = message;
    container.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}
</script>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="product-pagination-info">Showing 1 to 1 of 1 results</div>
    <ul id="product-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
const productRowsPerPage = 5;
const productTableBody = document.getElementById('product-table-body');
let productRows = Array.from(productTableBody.querySelectorAll('tr'));
const productPaginationLinks = document.getElementById('product-pagination-links');
const productPaginationInfo = document.getElementById('product-pagination-info');
let productCurrentPage = 1;

function showProductPage(page) {
    productCurrentPage = page;
    productRows.forEach(r => r.style.display='none');
    const start = (page-1)*productRowsPerPage;
    const end = start + productRowsPerPage;
    productRows.slice(start,end).forEach(r=>r.style.display='');
    renderProductPagination();
    const startItem = productRows.length ? start+1 : 0;
    const endItem = end>productRows.length ? productRows.length:end;
    productPaginationInfo.textContent=`Showing ${startItem} to ${endItem} of ${productRows.length} results`;
}

function renderProductPagination() {
    const totalPages = Math.ceil(productRows.length / productRowsPerPage);
    productPaginationLinks.innerHTML='';
    const prev = document.createElement('li');
    prev.className='border rounded px-2 py-1';
    prev.innerHTML=productCurrentPage===1?'« Prev':`<a href="#">« Prev</a>`;
    if(productCurrentPage!==1) prev.querySelector('a').addEventListener('click',e=>{e.preventDefault();showProductPage(productCurrentPage-1);});
    productPaginationLinks.appendChild(prev);
    for(let i=1;i<=totalPages;i++){
        const li=document.createElement('li');
        li.className='border rounded px-2 py-1'+(i===productCurrentPage?' bg-sky-400 text-white':'');
        li.innerHTML=i===productCurrentPage?i:`<a href="#">${i}</a>`;
        if(i!==productCurrentPage) li.querySelector('a').addEventListener('click',e=>{e.preventDefault();showProductPage(i);});
        productPaginationLinks.appendChild(li);
    }
    const next=document.createElement('li');
    next.className='border rounded px-2 py-1';
    next.innerHTML=productCurrentPage===totalPages?'Next »':`<a href="#">Next »</a>`;
    if(productCurrentPage!==totalPages) next.querySelector('a').addEventListener('click',e=>{e.preventDefault();showProductPage(productCurrentPage+1);});
    productPaginationLinks.appendChild(next);
}

function deleteRow(button){
    if(confirm("Are you sure you want to remove this row from the table?")){
        button.closest('tr').remove();
        updateProductPagination();
    }
}

function updateProductPagination(){
    productRows = Array.from(productTableBody.querySelectorAll('tr'));
    showProductPage(1);
}

// Search
document.getElementById('product-search').addEventListener('input',function(){
    const query=this.value.toLowerCase();
    productRows.forEach(row=>{
        const match=Array.from(row.querySelectorAll('td')).some(td=>td.textContent.toLowerCase().includes(query));
        row.style.display=match?'':'none';
    });
});

// Initialize
showProductPage(1);
</script>

@endsection
