@extends('layouts.app')

@section('title', 'Categories')

@section('content')

<style>[x-cloak] { display: none !important; }</style>

<div x-data="categoryPage()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage categories including their names and descriptions.</p>
</header>

<div id="message-container" class="max-w-7xl mx-auto mb-6" style="display: none;">
    <div id="message-content" class="px-4 py-3 rounded"></div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-7xl mx-auto">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">
        <div class="relative w-full md:w-1/4">
            <input type="text" id="category-search" placeholder="Search categories"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <button @click="openAddModal()"
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
                <tr class="hover:bg-gray-50 category-row"
                    data-id="{{ $category->category_id }}"
                    data-name="{{ $category->category_name }}"
                    data-description="{{ $category->description }}">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        C{{ str_pad($category->category_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $category->category_name }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $category->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                    @click="openEditModal($event)"
                                    class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </button>

                            <button title="Archive" onclick="deleteCategoryRow(this)"
                                    class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
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

            <tr id="category-empty-row" class="{{ $categories->count() ? 'hidden' : '' }}">
                <td colspan="4" class="px-4 py-10 text-center text-gray-500 text-sm">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-16 w-16 text-gray-300"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                            <path d="M14 3v5h5" />
                            <path d="M9 13h6" />
                            <path d="M9 17h3" />
                        </svg>
                        <p class="text-gray-700 font-semibold">
                            No categories found
                        </p>
                        <p class="text-gray-400 text-xs">
                            There are currently no categories matching these filters.
                        </p>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div x-show="showCategoryModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="closeModal()"
         class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative">

        <h2 class="text-2xl font-bold mb-4 text-gray-800"
            x-text="isEdit ? 'Edit Category' : 'Create Category'"></h2>

        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Category Name</label>
                <input type="text"
                       x-model="categoryName"
                       @focus="if(isEdit){ categoryName=''; }"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none
                              text-gray-400 focus:text-gray-900"
                       placeholder="">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <textarea
                    x-model="categoryDescription"
                    @focus="if(isEdit){ categoryDescription=''; }"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none
                           text-gray-400 focus:text-gray-900"
                    placeholder=""></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="closeModal()"
                    class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                Cancel
            </button>

            <button x-show="!isEdit"
                    @click="addCategory()"
                    class="px-6 py-2 rounded-lg text-black bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>

            <button x-show="isEdit"
                    @click="updateCategory()"
                    class="px-6 py-2 rounded-lg text-black bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Update
            </button>
        </div>
    </div>
</div>

</div>

<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="category-pagination-info"></div>
    <ul id="category-pagination-links" class="pagination-links flex gap-2"></ul>
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
                row.className = 'hover:bg-gray-50 category-row';
                row.dataset.id = data.category_id;
                row.dataset.name = data.category_name;
                row.dataset.description = data.description ?? '';

                row.innerHTML = `
                    <td class='px-4 py-3 text-center font-medium text-gray-800'>
                        C${String(data.category_id).padStart(3,'0')}
                    </td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.category_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                    <td class='px-4 py-3 text-center'>
                        <div class='flex items-center justify-center space-x-2'>
                            <button title='Edit'
                                onclick='window.__categoryOpenEditFromRow(event)'
                                class='p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none'
                                    stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-square-pen'>
                                    <path d='M12 20h9'/>
                                    <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z'/>
                                </svg>
                            </button>
                            <button title='Archive' onclick='deleteCategoryRow(this)'
                                class='p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200'>
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

                if (emptyRow) {
                    tbody.insertBefore(row, emptyRow);
                } else {
                    tbody.appendChild(row);
                }

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

const catRowsPerPage   = 5;
const catTableBody     = document.getElementById('category-table-body');
const catEmptyRow      = document.getElementById('category-empty-row');
const catPaginationLinks = document.getElementById('category-pagination-links');
const catPaginationInfo  = document.getElementById('category-pagination-info');
const catSearchInput   = document.getElementById('category-search');

let allCatRows = Array.from(catTableBody.querySelectorAll('.category-row'));
let catCurrentPage = 1;
let catVisibleRows = [...allCatRows];

function applyCategorySearch() {
    const q = (catSearchInput.value || '').toLowerCase();

    catVisibleRows = allCatRows.filter(r => {
        if (!q) return true;
        const text = r.textContent.toLowerCase();
        return text.includes(q);
    });

    if (catVisibleRows.length === 0) {
        allCatRows.forEach(r => r.style.display = 'none');
        catEmptyRow.classList.remove('hidden');
        catPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        catPaginationLinks.innerHTML = '';
        return;
    } else {
        catEmptyRow.classList.add('hidden');
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
    catPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${catVisibleRows.length} results`;
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

function deleteCategoryRow(button) {
    if (confirm("Remove this category from the table?")) {
        const row = button.closest('tr');
        row.remove();

        const index = allCatRows.indexOf(row);
        if (index !== -1) allCatRows.splice(index, 1);

        updateCategoryPagination();
        alert('Category archived successfully!');
    }
}

function updateCategoryPagination() {
    allCatRows = Array.from(catTableBody.querySelectorAll('.category-row'));
    applyCategorySearch();
}

catSearchInput.addEventListener('input', applyCategorySearch);
applyCategorySearch();
</script>

@endsection
