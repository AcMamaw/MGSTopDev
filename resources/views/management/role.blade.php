@extends('layouts.app')

@section('title', 'Roles')

@section('content')

{{-- Prevent modal flashing --}}
<style>[x-cloak] { display: none !important; }</style>

<div x-data="{ showAddRoleModal: false, roleName: '', roleDescription: '' }">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Roles</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage role records and their descriptions.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">

        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="role-search" placeholder="Search roles"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Role -->
        <button @click="showAddRoleModal = true"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                fill="none" stroke="currentColor" stroke-width="2"
                class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Role</span>
        </button>
    </div>
</div>

<!-- Roles Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="role-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Role ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Role Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="role-table-body" class="divide-y divide-gray-100">
                @foreach ($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">
                        R{{ str_pad($role->role_id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $role->role_name }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $role->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                             <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>
                                <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
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

<!-- Add Role Modal -->
<div x-show="showAddRoleModal" x-cloak x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div @click.away="showAddRoleModal = false"
        x-transition class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative">

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Creating a Role</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Role Name</label>
                <input type="text" x-model="roleName"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Enter role name">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Description</label>
                <textarea x-model="roleDescription"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Enter description"></textarea>
            </div>
        </div>


        <div class="mt-6 flex justify-end gap-3">
            <button @click="showAddRoleModal = false"
                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>

                <button
                @click="
                    if (!roleName.trim()) {
                        alert('Role name is required!');
                        return;
                    }

                    fetch('{{ route('roles.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            role_name: roleName,
                            description: roleDescription
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        const tbody = document.getElementById('role-table-body');
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';

                        row.innerHTML = `
                            <td class='px-4 py-3 text-center font-medium text-gray-800'>
                                R${String(data.role_id).padStart(3,'0')}
                            </td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.role_name}</td>
                            <td class='px-4 py-3 text-center text-gray-600'>${data.description ?? ''}</td>
                            <td class='px-4 py-3 text-center'>
                                <button title='Edit' class='p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor'
                                        stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-square-pen'>
                                        <path d='M12 20h9'/>
                                        <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z'/>
                                    </svg>
                                </button>

                                <button title='Archive' onclick='deleteRow(this)' class='p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='none' stroke='currentColor'
                                        stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-archive'>
                                        <path d='M3 4h18v4H3z'/>
                                        <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8'/>
                                        <path d='M10 12h4'/>
                                    </svg>
                                </button>
                            </td>
                        `;
                        
                        tbody.appendChild(row);

                        // reset and close modal
                        roleName = '';
                        roleDescription = '';
                        showAddRoleModal = false;
                        updatePagination();
                    })
                    .catch(error => console.error(error));
                "
                class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="role-pagination-info">Showing 1 to 1 of 1 results</div>
    <ul id="role-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
// Front-end Pagination for ROLES
const roleRowsPerPage = 5;
const roleTableBody = document.getElementById('role-table-body');
const roleRows = Array.from(roleTableBody.querySelectorAll('tr'));
const rolePaginationLinks = document.getElementById('role-pagination-links');
const rolePaginationInfo = document.getElementById('role-pagination-info');

let roleCurrentPage = 1;
let roleTotalPages = Math.ceil(roleRows.length / roleRowsPerPage);

// Main show page function
function showRolePage(page) {
    roleCurrentPage = page;
    roleRows.forEach(row => row.style.display = 'none');

    const start = (page - 1) * roleRowsPerPage;
    const end = start + roleRowsPerPage;
    roleRows.slice(start, end).forEach(row => row.style.display = '');

    renderRolePagination();

    const startItem = roleRows.length ? start + 1 : 0;
    const endItem = end > roleRows.length ? roleRows.length : end;
    rolePaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${roleRows.length} results`;
}

// Pagination UI
function renderRolePagination() {
    rolePaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = roleCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (roleCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => {
        e.preventDefault();
        showRolePage(roleCurrentPage - 1);
    });
    rolePaginationLinks.appendChild(prev);

    for (let i = 1; i <= roleTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === roleCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === roleCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== roleCurrentPage) li.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showRolePage(i);
        });
        rolePaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = roleCurrentPage === roleTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (roleCurrentPage !== roleTotalPages) next.querySelector('a').addEventListener('click', e => {
        e.preventDefault();
        showRolePage(roleCurrentPage + 1);
    });
    rolePaginationLinks.appendChild(next);
}

// Delete Row
function deleteRow(button) {
    if (confirm("Are you sure you want to remove this row from the table?")) {
        button.closest('tr').remove();
        updateRolePagination();
    }
}

// Update after delete
function updateRolePagination() {
    roleRows.length = 0;
    Array.from(roleTableBody.querySelectorAll('tr')).forEach(tr => roleRows.push(tr));
    roleTotalPages = Math.ceil(roleRows.length / roleRowsPerPage);
    showRolePage(1);
}

// Search Filter
document.getElementById('role-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    roleRows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const match = cells.some(cell => cell.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});

// Initialize
showRolePage(1);
</script>

@endsection
