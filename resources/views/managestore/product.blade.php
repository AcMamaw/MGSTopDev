@extends('layouts.app')

@section('title', 'Products')

@section('content')
<style>[x-cloak] { display: none !important; }</style>

<div x-data="productPage()" class="px-4 sm:px-6 lg:px-8">

    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-6 left-1/2 -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-full shadow-xl text-md z-[9999]"
         x-cloak>
        <span x-text="toastMessage" class="font-semibold"></span>
    </div>

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                    <path d="M10 9l3-3 3 3"/>
                    <path d="M13 18l-3-3-3 3"/>
                    <path d="M13 6v12"/>
                    <path d="M11 10h2"/>
                </svg>
                Product
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Manage product records including supplier, category, price, unit, and status.</p>
    </header>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-lg shadow-sm max-w-7xl mx-auto">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-lg shadow-sm max-w-7xl mx-auto">
            <p class="font-bold mb-1">Validation Errors:</p>
            <ul class="list-disc list-inside ml-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        {{-- LEFT: search --}}
        <div class="relative w-full md:w-80">
            <input type="text" id="product-search" placeholder="Search products"
                class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        {{-- RIGHT: archive + add --}}
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            @include('added.archive_product')

            <button @click="openAddModal()"
                class="w-full md:w-auto bg-yellow-400 text-gray-900 px-6 py-2 rounded-full font-bold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-lg shadow-yellow-200/50">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Add New Product</span>
            </button>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="product-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Product ID</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Product Name</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="product-table-body" class="divide-y divide-gray-100">
                    @forelse ($products->where('archive', '!=', 'Archived') as $product)
                        <tr class="hover:bg-yellow-50/50 transition-colors product-row"
                            data-id="{{ $product->product_id }}"
                            data-supplier_id="{{ $product->supplier_id }}"
                            data-supplier_name="{{ $product->supplier->supplier_name ?? '-' }}"
                            data-category_id="{{ $product->category_id }}"
                            data-category_name="{{ $product->category->category_name ?? '-' }}"
                            data-product_name="{{ $product->product_name }}"
                            data-description="{{ $product->description }}"
                            data-unit="{{ $product->unit }}"
                            data-markup_rule="{{ $product->markup_rule ?? 0 }}"
                            data-image_path="{{ $product->image_path ?? '' }}">
                            <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                                P{{ str_pad($product->product_id,3,'0',STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $product->supplier->supplier_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $product->category->category_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ $product->product_name }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate">{{ $product->description }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $product->unit }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Set Image --}}
                                    <button title="Set Image"
                                            @click="openImageModal($event)"
                                            class="p-2 rounded-full text-blue-500 hover:text-blue-700 hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                             <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                             <circle cx="8.5" cy="8.5" r="1.5"/>
                                             <path d="M21 15l-3.5-3.5L13 16l-4-4L3 20"/>
                                        </svg>
                                    </button>
                                    {{-- Edit --}}
                                    <button title="Edit"
                                            @click="openEditModal($event)"
                                            class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                    </button>
                                    <button
                                        title="Archive"
                                        onclick="markArchive(this)"
                                        class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
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
                    @empty
                        <tr id="product-empty-row" class="">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-10 w-10 text-gray-400"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                                        <line x1="12" y1="6" x2="12" y2="18"/>
                                        <line x1="6" y1="12" x2="18" y2="12"/>
                                    </svg>
                                    <p class="text-gray-700 font-semibold mt-2">No products found</p>
                                    <p class="text-gray-500 text-sm">Use the button above to add your first product record.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add / Edit Product Modal --}}
    <div x-show="showProductModal" x-cloak x-transition
          class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="closeModal()"
             class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg">
            <h2 class="text-3xl font-extrabold mb-7 text-center text-gray-800"
                x-text="isEdit ? 'Edit Product Details' : 'Add New Product'"></h2>

            <div class="grid grid-cols-1 gap-6">
                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Supplier</label>
                    <select x-model="supplierId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                        <option value="" disabled selected>Select a supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">
                                {{ $supplier->supplier_name }}
                                @if($supplier->supplier_name === 'MGS Team')
                                    (Owned Company)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select x-model="categoryId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                        <option value="" disabled selected>Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name</label>
                    <input type="text"
                            x-model="productName"
                            @focus="if(isEdit){ $el.select(); }"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Enter product name">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <input type="text"
                            x-model="description"
                            @focus="if(isEdit){ $el.select(); }"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Brief product description">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Unit --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Unit</label>
                        <input type="text"
                                x-model="unit"
                                @focus="if(isEdit){ $el.select(); }"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                                placeholder="e.g. per piece, per box, per liter">
                    </div>

                    {{-- Markup Rule --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Markup Rule (%)</label>
                        <input type="number"
                                x-model="markupRule"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                                placeholder="e.g. 50" min="0">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button @click="closeModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button x-show="!isEdit"
                        @click="addProduct()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Confirm
                </button>

                <button x-show="isEdit"
                        @click="updateProduct()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Update
                </button>
            </div>
        </div>
    </div>

  

    {{-- Image Modal (file upload) --}}
    <div x-show="showImageModal" x-cloak x-transition
          class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="closeImageModal()"
             class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-sm">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Set Product Image</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Image File</label>
                    <input type="file"
                            accept="image/*"
                            @change="onImageSelected($event)"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0 file:text-sm file:font-semibold
                                    file:bg-yellow-50 file:text-black-700 hover:file:bg-yellow-100 transition duration-150">
                </div>

                <template x-if="imagePreview">
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Image Preview:</p>
                        <div class="bg-gray-100 p-2 rounded-lg border border-gray-200">
                            <img :src="imagePreview"
                                 class="w-full h-40 object-contain rounded-lg"
                                 alt="Product Image Preview">
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button @click="closeImageModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button @click="uploadImage()"
                        :disabled="!imageFile"
                        :class="{'opacity-50 cursor-not-allowed': !imageFile}"
                        class="px-6 py-2 rounded-full bg-yellow-500 text-black font-bold hover:bg-yellow-600 transition shadow-md shadow-blue-200/50">
                    Upload & Save
                </button>
            </div>
        </div>
    </div>


    {{-- Pagination --}}
    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
        <div id="product-pagination-info"></div>
        <ul id="product-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>
</div>

<script>
function productPage() {
    return {
        // product modal
        showProductModal: false,
        isEdit: false,
        editingId: null,

        supplierId: '',
        categoryId: '',
        productName: '',
        description: '',
        unit: '',
        markupRule: '',

        // image modal
        showImageModal: false,
        imageProductId: null,
        imageFile: null,
        imagePreview: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.supplierId = '';
            this.categoryId = '';
            this.productName = '';
            this.description = '';
            this.unit = '';
            this.markupRule = '';
            this.showProductModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            if (!row) return;

            this.isEdit = true;
            this.editingId = row.dataset.id;

            this.supplierId  = row.dataset.supplier_id || '';
            this.categoryId  = row.dataset.category_id || '';
            this.productName = row.dataset.product_name || '';
            this.description = row.dataset.description || '';
            this.unit        = row.dataset.unit || '';
            this.markupRule  = row.dataset.markup_rule || '';

            this.showProductModal = true;
        },

        closeModal() {
            this.showProductModal = false;
        },

        openImageModal(event) {
            const row = event.currentTarget.closest('tr');
            if (!row) return;

            this.imageProductId = row.dataset.id;
            this.imageFile = null;
            this.imagePreview = row.dataset.image_path || '';
            this.showImageModal = true;
        },

        closeImageModal() {
            this.showImageModal = false;
            this.imageProductId = null;
            this.imageFile = null;
            this.imagePreview = '';
        },

        onImageSelected(e) {
            const file = e.target.files[0];
            if (!file) {
                this.imageFile = null;
                this.imagePreview = '';
                return;
            }

            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.imagePreview = ev.target.result;
            };
            reader.readAsDataURL(file);
        },

        uploadImage() {
            if (!this.imageProductId || !this.imageFile) {
                alert('Please choose an image file first.');
                return;
            }

            const formData = new FormData();
            formData.append('image', this.imageFile);

            fetch('{{ route("products.updateImage", 0) }}'.replace('/0', '/' + this.imageProductId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(p => {
                const tbody = document.getElementById('product-table-body');
                if (!tbody) return;
                const row = Array.from(tbody.querySelectorAll('tr'))
                    .find(r => r.dataset.id == this.imageProductId);
                if (row) {
                    row.dataset.image_path = p.image_path || '';
                }
                this.closeImageModal();
                alert('Product image updated successfully!');
            })
            .catch(console.error);
        },

        addProduct() {
            if (!this.productName || !this.supplierId || !this.categoryId || !this.unit) {
                alert('Please fill all required fields.');
                return;
            }

            fetch('{{ route('products.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    supplier_id: this.supplierId,
                    category_id: this.categoryId,
                    product_name: this.productName,
                    description: this.description,
                    unit: this.unit,
                    markup_rule: this.markupRule || 0
                })
            })
            .then(res => res.json())
            .then(data => {
                const tbody    = document.getElementById('product-table-body');
                const emptyRow = document.getElementById('product-empty-row');
                if (!tbody) return;

                const row   = document.createElement('tr');
                row.className = 'hover:bg-yellow-50/50 transition-colors product-row';
                row.dataset.id            = data.product_id;
                row.dataset.supplier_id   = data.supplier_id;
                row.dataset.supplier_name = data.supplier_name;
                row.dataset.category_id   = data.category_id;
                row.dataset.category_name = data.category_name;
                row.dataset.product_name  = data.product_name;
                row.dataset.description   = data.description ?? '';
                row.dataset.unit          = data.unit;
                row.dataset.markup_rule   = data.markup_rule ?? 0;
                row.dataset.image_path    = data.image_path ?? '';

                row.innerHTML = `
                    <td class='px-4 py-3 text-center font-medium text-gray-800'>
                        P${String(data.product_id).padStart(3,'0')}
                    </td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.supplier_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.category_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.product_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.unit}</td>
                    <td class='px-4 py-3 text-center'>
                        <div class='flex items-center justify-center space-x-2'>
                            <button title="Set Image"
                                    @click="openImageModal($event)"
                                    class="p-2 rounded-full text-blue-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="16" rx="2" ry="2"/>
                                    <rcle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="M21 13l-3.5-3.5L13 14l-2-2L6 17"/>
                                </svg>
                            </button>
                            <button title='Edit'
                                onclick='window.__productOpenEditFromRow(event)'
                                class='p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                    <path d='M12 20h9' />
                                    <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z' />
                                </svg>
                            </button>
                            <button title='Archive' onclick='deleteRow(this)' class='p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='22' height='25' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                    <path d='M3 4h18v4H3z' />
                                    <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8' />
                                    <path d='M10 12h4' />
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                if (emptyRow) emptyRow.style.display = 'none';
                tbody.appendChild(row);

                this.supplierId = '';
                this.categoryId = '';
                this.productName = '';
                this.description = '';
                this.unit = '';
                this.markupRule = '';
                this.showProductModal = false;

                updateProductPagination();
                alert('Product added successfully!');
            })
            .catch(console.error);
        },

        updateProduct() {
            if (!this.editingId) return;

            const payload = {};
            if (this.supplierId)  payload.supplier_id  = this.supplierId;
            if (this.categoryId)  payload.category_id  = this.categoryId;
            if (this.productName) payload.product_name = this.productName;
            if (this.description) payload.description  = this.description;
            if (this.unit)        payload.unit         = this.unit;
            if (this.markupRule !== '') payload.markup_rule = this.markupRule;

            if (Object.keys(payload).length === 0) {
                alert('Nothing to update.');
                return;
            }

            fetch('{{ route("products.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(p => {
                const tbody = document.getElementById('product-table-body');
                if (!tbody) return;

                const row = Array.from(tbody.querySelectorAll('.product-row')).find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.supplier_id   = p.supplier_id;
                row.dataset.supplier_name = p.supplier_name;
                row.dataset.category_id   = p.category_id;
                row.dataset.category_name = p.category_name;
                row.dataset.product_name  = p.product_name;
                row.dataset.description   = p.description ?? '';
                row.dataset.unit          = p.unit;
                row.dataset.markup_rule   = p.markup_rule ?? 0;

                row.children[0].textContent = 'P' + String(p.product_id).padStart(3,'0');
                row.children[1].textContent = p.supplier_name;
                row.children[2].textContent = p.category_name;
                row.children[3].textContent = p.product_name;
                row.children[4].textContent = p.description ?? '';
                row.children[5].textContent = p.unit;

                this.showProductModal = false;
                updateProductPagination();
                alert('Product updated successfully!');
            })
            .catch(console.error);
        }
    }
}

window.__productOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="productPage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

const productRowsPerPage    = 5;
const productTableBody      = document.getElementById('product-table-body');
const productEmptyRow       = document.getElementById('product-empty-row');
const productPaginationLinks= document.getElementById('product-pagination-links');
const productPaginationInfo = document.getElementById('product-pagination-info');
const productSearchInput    = document.getElementById('product-search');

let allProductRows = Array.from(productTableBody.querySelectorAll('.product-row'));
let productCurrentPage = 1;
let visibleRows = [...allProductRows];

function applyProductSearch() {
    const q = (productSearchInput.value || '').toLowerCase();

    visibleRows = allProductRows.filter(row => {
        if (!q) return true;
        return row.textContent.toLowerCase().includes(q);
    });

    if (visibleRows.length === 0) {
        allProductRows.forEach(r => r.style.display = 'none');
        if (productEmptyRow) productEmptyRow.style.display = '';
        productPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        productPaginationLinks.innerHTML = '';
        return;
    } else {
        if (productEmptyRow) productEmptyRow.style.display = 'none';
    }

    productCurrentPage = 1;
    showProductPage(1);
}

function showProductPage(page) {
    const totalPages = Math.ceil(visibleRows.length / productRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    productCurrentPage = page;

    allProductRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * productRowsPerPage;
    const end   = start + productRowsPerPage;

    visibleRows.slice(start, end).forEach(r => r.style.display = '');

    renderProductPagination(totalPages);

    const startItem = visibleRows.length ? start + 1 : 0;
    const endItem   = end > visibleRows.length ? visibleRows.length : end;
    productPaginationInfo.textContent =
        `Showing ${startItem} to ${endItem} of ${visibleRows.length} results`;
}

function renderProductPagination(totalPages) {
    productPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = productCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (productCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showProductPage(productCurrentPage - 1);
        });
    }
    productPaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' +
                       (i === productCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === productCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== productCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showProductPage(i);
            });
        }
        productPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = productCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (productCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showProductPage(productCurrentPage + 1);
        });
    }
    productPaginationLinks.appendChild(next);
}

function updateProductPagination() {
    allProductRows = Array.from(productTableBody.querySelectorAll('.product-row'));
    applyProductSearch();
}

productSearchInput.addEventListener('input', applyProductSearch);

// initialize on load
updateProductPagination();
</script>

<script>
function markArchive(button) {
    if (!confirm('Are you sure you want to archive this product?')) return;

    const row = button.closest('tr');
    if (!row) return;

    const id = row.dataset.id;

    fetch('{{ route("products.archive", ":id") }}'.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            archive: 'Archived'
        })
    })
    .then(res => res.json())
    .then(data => {
        alert('Product archived successfully!');
        location.reload();
    })
    .catch(() => {
        alert('Failed to archive product. Please try again.');
    });
}
</script>

@endsection
