@extends('layouts.app')

@section('title', 'Categories')

@section('content')

<style>[x-cloak] { display: none !important; }</style>

<div x-data="{ showAddCategoryModal: false, categoryName: '', categoryDescription: '' }">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage categories including their names and descriptions.</p>
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
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="category-search" placeholder="Search categories"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Category -->
        <button @click="showAddCategoryModal = true"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                stroke="currentColor" stroke-width="2" class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Category</span>
        </button>
    </div>
</div>

<!-- Categories Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="category-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Category ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Category Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="category-table-body" class="divide-y divide-gray-100">
                @foreach ($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        C{{ str_pad($category->category_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $category->category_name }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $category->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </button>

                            <button title="Archive" onclick="deleteCategoryRow(this)"
                                class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-archive">
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

<!-- Add Category Modal -->
<div x-show="showAddCategoryModal" x-cloak x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div @click.away="showAddCategoryModal = false"
        class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Create Category</h2>

        <div class="space-y-4">
            <!-- Category Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Category Name</label>
                <input type="text" x-model="categoryName"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    placeholder="Enter category name">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <textarea x-model="categoryDescription"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                    placeholder="Enter description"></textarea>
            </div>
        </div>

        <!-- Modal Buttons -->
        <div class="mt-6 flex justify-end gap-3">
            <!-- Cancel -->
            <button @click="showAddCategoryModal = false"
                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                Cancel
            </button>

            <!-- Confirm -->
            <button
                @click="
                    if (!categoryName.trim()) {
                        showMessage('Category name is required!', 'error');
                        return;
                    }

                    fetch('{{ route('category.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            category_name: categoryName,
                            description: categoryDescription
                        })
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(err => Promise.reject(err));
                        }
                        return res.json();
                    })
                    .then(data => {
                        const tbody = document.getElementById('category-table-body');
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';

                        row.innerHTML = `
                            <td class='px-4 py-3 text-center font-medium text-gray-800'>
                                C${String(data.category_id).padStart(3,'0')}
                            </td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.category_name}</td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                            <td class='px-4 py-3 text-center'>
                                <div class='flex items-center justify-center space-x-2'>
                                    <button title='Edit'
                                        class='p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none'
                                            stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-square-pen'>
                                            <path d='M12 20h9'/>
                                            <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z'/>
                                        </svg>
                                    </button>
                                    <button title='Archive' onclick='deleteCategoryRow(this)'
                                        class='p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='none'
                                            stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-archive'>
                                            <path d='M3 4h18v4H3z'/>
                                            <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8'/>
                                            <path d='M10 12h4'/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        `;

                        tbody.appendChild(row);

                        // Reset Fields
                        categoryName = '';
                        categoryDescription = '';
                        showAddCategoryModal = false;

                        // Show Success Message
                        showMessage('Category added successfully!', 'success');

                        if (typeof updateCategoryPagination === 'function') {
                            updateCategoryPagination();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        let errorMsg = 'Error saving category';
                        if (err.errors) {
                            errorMsg = Object.values(err.errors).flat().join(', ');
                        } else if (err.message) {
                            errorMsg = err.message;
                        }
                        showMessage(errorMsg, 'error');
                    });
                "
                class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
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
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

function deleteCategoryRow(button) {
    if (confirm('Are you sure you want to archive this category?')) {
        const row = button.closest('tr');
        row.remove();
        showMessage('Category archived successfully!', 'success');
    }
}
</script>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="category-pagination-info">Showing 1 to 1 of 1 results</div>
    <ul id="category-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
/* PAGINATION */
const catRowsPerPage = 5;
const catTableBody = document.getElementById('category-table-body');
let catRows = Array.from(catTableBody.querySelectorAll('tr'));
const catPaginationLinks = document.getElementById('category-pagination-links');
const catPaginationInfo = document.getElementById('category-pagination-info');

let catCurrentPage = 1;
let catTotalPages = Math.ceil(catRows.length / catRowsPerPage);

function showCategoryPage(page) {
    catCurrentPage = page;
    catRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * catRowsPerPage;
    const end = start + catRowsPerPage;
    catRows.slice(start, end).forEach(r => r.style.display = '');

    renderCategoryPagination();

    const startItem = catRows.length ? start + 1 : 0;
    const endItem = end > catRows.length ? catRows.length : end;

    catPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${catRows.length} results`;
}

function renderCategoryPagination() {
    catPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = catCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (catCurrentPage !== 1)
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCategoryPage(catCurrentPage - 1);
        });
    catPaginationLinks.appendChild(prev);

    for (let i = 1; i <= catTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === catCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === catCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== catCurrentPage)
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showCategoryPage(i);
            });
        catPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = catCurrentPage === catTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (catCurrentPage !== catTotalPages)
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCategoryPage(catCurrentPage + 1);
        });
    catPaginationLinks.appendChild(next);
}

/* DELETE ROW */
function deleteCategoryRow(button) {
    if (confirm("Remove this category from the table?")) {
        button.closest('tr').remove();
        updateCategoryPagination();
    }
}

function updateCategoryPagination() {
    catRows = Array.from(catTableBody.querySelectorAll('tr'));
    catTotalPages = Math.ceil(catRows.length / catRowsPerPage);
    showCategoryPage(1);
}

/* SEARCH */
document.getElementById('category-search').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    catRows.forEach(r => {
        const match = Array.from(r.querySelectorAll('td')).some(c => c.textContent.toLowerCase().includes(q));
        r.style.display = match ? '' : 'none';
    });
});

showCategoryPage(1);
</script>

@endsection
