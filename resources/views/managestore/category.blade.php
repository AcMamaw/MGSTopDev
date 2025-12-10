@extends('layouts.app')

@section('title', 'Categories')

@section('content')

<style>[x-cloak] { display: none !important; }</style>

<div x-data="categoryPage()" class="px-4 sm:px-6 lg:px-8">

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
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>
                Categories
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Manage categories including their names and descriptions for product classification.</p>
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
    {{-- LEFT: Search --}}
    <div class="relative w-full md:w-80">
        <input type="text" id="category-search" placeholder="Search categories"
            class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
    </div>

    {{-- RIGHT: Archive History + Add New Category --}}
    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
        @include('added.archive_categories')

        <button @click="openAddModal()"
            class="w-full md:w-auto bg-yellow-400 text-gray-900 px-6 py-2 rounded-full font-bold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-lg shadow-yellow-200/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Category</span>
        </button>
    </div>
</div>


    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="category-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Category ID</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Category Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                </tr>
                </thead>
                <tbody id="category-table-body" class="divide-y divide-gray-100">
                 @forelse ($categories->where('archive', '!=', 'Archived') as $category)
                    <tr class="hover:bg-yellow-50/50 transition-colors category-row"
                        data-id="{{ $category->category_id }}"
                        data-name="{{ $category->category_name }}"
                        data-description="{{ $category->description }}">
                        <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                            C{{ str_pad($category->category_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-left text-gray-600 font-medium">{{ $category->category_name }}</td>
                        <td class="px-4 py-3 text-left text-gray-600 max-w-md truncate">{{ $category->description }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Edit Button --}}
                                <button title="Edit"
                                        @click="openEditModal($event)"
                                        class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                </button>
                                {{-- Archive Button --}}
                                <button title="Archive"
                                        onclick="markArchive(this)"
                                        class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200">
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
                    <tr id="category-empty-row" class="">
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-10 w-10 text-gray-400"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                    <path d="M14 3v5h5" />
                                    <path d="M9 13h6" />
                                    <path d="M9 17h3" />
                                </svg>
                                <p class="text-gray-700 font-semibold mt-2">No categories found</p>
                                <p class="text-gray-500 text-sm">Start by adding a new category using the button above.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

 

    {{-- Add / Edit Category Modal --}}
    <div x-show="showCategoryModal" x-cloak x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="closeModal()"
             class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative border-t-4 border-yellow-400">

            <h2 class="text-3xl font-extrabold mb-7 text-center text-gray-800"
                x-text="isEdit ? 'Edit Category Details' : 'Create New Category'"></h2>

            <div class="space-y-6">
                {{-- Category Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category Name</label>
                    <input type="text"
                        x-model="categoryName"
                        @focus="if(isEdit){ $el.select(); }"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        placeholder="e.g., Beverages, Electronics, Groceries">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea
                        x-model="categoryDescription"
                        @focus="if(isEdit){ $el.select(); }"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none
                               text-gray-800 placeholder-gray-400 focus:border-yellow-400 transition resize-none h-24"
                        placeholder="A brief description of products belonging to this category."></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button @click="closeModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button x-show="!isEdit"
                        @click="addCategory()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Confirm
                </button>

                <button x-show="isEdit"
                        @click="updateCategory()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Update
                </button>
            </div>
        </div>
    </div>


    {{-- Pagination --}}
    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
        <div id="category-pagination-info"></div>
        <ul id="category-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>
</div>

<script>
function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const content = document.getElementById('message-content');

    content.className = 'px-4 py-3 rounded';

    if (type === 'success') {
        content.className += ' bg-green-100 border border-green-400 text-green-700';
    } else if (type === 'error') {
        content.className += ' bg-red-100 border border-red-400 text-red-700';
    }

    content.textContent = message;
    container.style.display = 'block';

    window.scrollTo({ top: 0, behavior: 'smooth' });

    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

function categoryPage() {
    return {
        showCategoryModal: false,
        isEdit: false,
        editingId: null,
        categoryName: '',
        categoryDescription: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.categoryName = '';
            this.categoryDescription = '';
            this.showCategoryModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            if (!row) return;

            this.isEdit = true;
            this.editingId = row.dataset.id;
            this.categoryName = row.dataset.name || '';
            this.categoryDescription = row.dataset.description || '';
            this.showCategoryModal = true;
        },

        closeModal() {
            this.showCategoryModal = false;
        },

        addCategory() {
            if (!this.categoryName.trim()) {
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
                    category_name: this.categoryName,
                    description: this.categoryDescription
                })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('category-table-body');
                const emptyRow = document.getElementById('category-empty-row');

                const row = document.createElement('tr');
                row.className = 'hover:bg-yellow-50/50 transition-colors category-row';
                row.dataset.id = data.category_id;
                row.dataset.name = data.category_name;
                row.dataset.description = data.description ?? '';

                row.innerHTML = `
                    <td class='px-4 py-3 text-center font-medium text-gray-800'>
                        C${String(data.category_id).padStart(3,'0')}
                    </td>
                    <td class='px-4 py-3 text-left text-gray-600 font-medium'>${data.category_name}</td>
                    <td class='px-4 py-3 text-left text-gray-600 max-w-md truncate'>${data.description ?? ''}</td>
                    <td class='px-4 py-3 text-center'>
                        <div class='flex items-center justify-center space-x-2'>
                            <button title='Edit'
                                onclick='window.__categoryOpenEditFromRow(event)'
                                class='p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'
                                     stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                    <path d='M12 20h9' />
                                    <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z' />
                                </svg>
                            </button>
                            <button title='Archive' onclick='deleteCategoryRow(this)'
                                class='p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'
                                     stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
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

                this.categoryName = '';
                this.categoryDescription = '';
                this.showCategoryModal = false;

                updateCategoryPagination();
                alert('Category added successfully!');
            });
        },

        updateCategory() {
            if (!this.editingId) return;

            if (!this.categoryName.trim()) {
                showMessage('Category name is required!', 'error');
                return;
            }

            fetch('{{ route("categories.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    category_name: this.categoryName,
                    description: this.categoryDescription
                })
            })
            .then(res => res.json())
            .then(c => {
                const tbody = document.getElementById('category-table-body');
                const row = Array.from(tbody.querySelectorAll('.category-row'))
                    .find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.name = c.category_name;
                row.dataset.description = c.description ?? '';

                row.children[0].textContent = 'C' + String(c.category_id).padStart(3,'0');
                row.children[1].textContent = c.category_name;
                row.children[2].textContent = c.description ?? '';

                this.showCategoryModal = false;
                updateCategoryPagination();
                alert('Category updated successfully!');
            });
        }
    }
}

window.__categoryOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="categoryPage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

const catRowsPerPage    = 5;
const catTableBody      = document.getElementById('category-table-body');
const catEmptyRow       = document.getElementById('category-empty-row');
const catPaginationLinks= document.getElementById('category-pagination-links');
const catPaginationInfo = document.getElementById('category-pagination-info');
const catSearchInput    = document.getElementById('category-search');

let allCatRows = Array.from(catTableBody.querySelectorAll('.category-row'));
let catCurrentPage = 1;
let catVisibleRows = [...allCatRows];

function applyCategorySearch() {
    const q = (catSearchInput.value || '').toLowerCase();

    catVisibleRows = allCatRows.filter(r => {
        if (!q) return true;
        return r.textContent.toLowerCase().includes(q);
    });

    if (catVisibleRows.length === 0) {
        allCatRows.forEach(r => r.style.display = 'none');
        if (catEmptyRow) catEmptyRow.style.display = '';
        catPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        catPaginationLinks.innerHTML = '';
        return;
    } else {
        if (catEmptyRow) catEmptyRow.style.display = 'none';
    }

    catCurrentPage = 1;
    showCategoryPage(1);
}

function showCategoryPage(page) {
    const catTotalPages = Math.ceil(catVisibleRows.length / catRowsPerPage) || 1;

    if (page < 1) page = 1;
    if (page > catTotalPages) page = catTotalPages;

    catCurrentPage = page;

    allCatRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * catRowsPerPage;
    const end   = start + catRowsPerPage;

    catVisibleRows.slice(start, end).forEach(r => r.style.display = '');

    renderCategoryPagination(catTotalPages);

    const startItem = catVisibleRows.length ? start + 1 : 0;
    const endItem   = end > catVisibleRows.length ? catVisibleRows.length : end;
    catPaginationInfo.textContent =
        `Showing ${startItem} to ${endItem} of ${catVisibleRows.length} results`;
}

function renderCategoryPagination(catTotalPages) {
    catPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = catCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (catCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCategoryPage(catCurrentPage - 1);
        });
    }
    catPaginationLinks.appendChild(prev);

    for (let i = 1; i <= catTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' +
                       (i === catCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === catCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== catCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showCategoryPage(i);
            });
        }
        catPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = catCurrentPage === catTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (catCurrentPage !== catTotalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showCategoryPage(catCurrentPage + 1);
        });
    }
    catPaginationLinks.appendChild(next);
}


function updateCategoryPagination() {
    allCatRows = Array.from(catTableBody.querySelectorAll('.category-row'));
    applyCategorySearch();
}

catSearchInput.addEventListener('input', applyCategorySearch);

// initialize on load
updateCategoryPagination();
</script>

<script>
function markArchive(button) {
    if (!confirm('Are you sure you want to archived category?')) return;

    const row = button.closest('tr');
    if (!row) return;

    const id = row.dataset.id;

    fetch('{{ route("categories.archive", ":id") }}'.replace(':id', id), {
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
        // optional: quick visual feedback before reload
        row.classList.add('opacity-50');

        alert('Category archived successfully!');

        // auto reload page so table refreshes from DB
        location.reload();
    })
    .catch(() => {
        alert('Failed to archive category. Please try again.');
    });
}
</script>


@endsection

