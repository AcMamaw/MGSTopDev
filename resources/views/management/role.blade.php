@extends('layouts.app')

@section('title', 'Roles')

@section('content')

<style>[x-cloak]{display:none !important;}</style>

<div x-data="rolePage()">

    <header class="mb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5L12 2z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                Roles
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Define and manage user roles and their associated descriptions within the system.</p>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-lg shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-lg shadow-sm">
            <p class="font-bold mb-1">Error Submitting Form:</p>
            <ul class="list-disc list-inside ml-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

     <div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">

            <!-- Left: search -->
            <div class="relative w-full md:w-80">
                <input type="text" id="role-search" placeholder="Search roles"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>

            <!-- Right: archive + add -->
            <div class="flex w-full md:w-auto items-center justify-end gap-3">
                @include('added.archive_role')

                <button @click="openAddModal()"
                    class="w-full md:w-auto bg-yellow-400 text-gray-900 px-6 py-2 rounded-full font-bold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-lg shadow-yellow-200/50">
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
    </div>

    {{-- TABLE OUTSIDE PADDED WRAPPER FOR FULL STRETCH --}}
    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="role-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Role ID</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Role Name</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="role-table-body" class="divide-y divide-gray-100">
                    @php
                        $activeRoles = $roles->where('archive', '!=', 'Archived');
                    @endphp

                    @forelse($activeRoles as $role)
                        <tr class="hover:bg-yellow-50/50 transition-colors"
                            data-id="{{ $role->role_id }}"
                            data-name="{{ $role->role_name }}"
                            data-description="{{ $role->description }}">
                            <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                                R{{ str_pad($role->role_id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $role->role_name }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate">
                                {{ $role->description }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button title="Edit"
                                            @click="openEditModal($event)"
                                            class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                    </button>

                                    <button title="Archive" onclick="archiveRole(this)"
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
                        <tr id="role-empty-initial">
                            <td colspan="4" class="px-4 py-12 text-center text-gray-500 italic">
                                No roles found. Add a new role to get started.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="role-empty-search" style="display:none;">
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
                                <p class="text-gray-700 font-semibold">No roles found</p>
                                <p class="text-gray-400 text-xs">
                                    There are currently no roles matching these filters.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION BACK INSIDE PADDED WRAPPER --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
            <div id="role-pagination-info"></div>
            <ul id="role-pagination-links" class="pagination-links flex gap-2"></ul>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="showRoleModal" x-cloak x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="closeModal()"
            class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative">
            <h2 class="text-3xl font-extrabold mb-7 text-center text-gray-800"
                x-text="isEdit ? 'Edit Role Record' : 'Create New Role'"></h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role Name</label>
                    <input type="text"
                        x-model="roleName"
                        @focus="$event.target.select()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        placeholder="Enter role name">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea rows="3"
                        x-model="roleDescription"
                        @focus="$event.target.select()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        placeholder="Enter description"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button @click="closeModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button x-show="!isEdit"
                        @click="addRole()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Confirm
                </button>

                <button x-show="isEdit"
                        @click="updateRole()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Update
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function rolePage() {
    return {
        showRoleModal: false,
        isEdit: false,
        editingId: null,

        roleName: '',
        roleDescription: '',

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.roleName = '';
            this.roleDescription = '';
            this.showRoleModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr');
            this.isEdit = true;
            this.editingId = row.dataset.id;
            this.roleName = row.dataset.name;
            this.roleDescription = row.dataset.description ?? '';
            this.showRoleModal = true;
        },

        closeModal() {
            this.showRoleModal = false;
        },

        addRole() {
            if (!this.roleName.trim()) {
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
                    role_name: this.roleName,
                    description: this.roleDescription
                })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('role-table-body');
                const emptyInitial = document.getElementById('role-empty-initial');
                if (emptyInitial) emptyInitial.style.display = 'none';

                const row = document.createElement('tr');
                row.className = 'hover:bg-yellow-50/50 transition-colors';
                row.dataset.id = data.role_id;
                row.dataset.name = data.role_name;
                row.dataset.description = data.description ?? '';

                row.innerHTML = `
                    <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                        R${String(data.role_id).padStart(3,'0')}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">${data.role_name}</td>
                    <td class="px-4 py-3 text-center text-gray-600 max-w-xs truncate">${data.description ?? ''}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit"
                                onclick="window.__roleOpenEditFromRow(event)"
                                class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive"
                                onclick="deleteRoleRow(this)"
                                class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-archive">
                                    <path d="M3 4h18v4H3z"/>
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                    <path d="M10 12h4"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                const emptySearch = document.getElementById('role-empty-search');
                tbody.insertBefore(row, emptySearch);

                refreshRoleRows();
                showRolePage(1);

                this.closeModal();
                alert('Role added successfully!');
            })
            .catch(error => {
                console.error(error);
                alert('Something went wrong while saving the role.');
            });
        },

        updateRole() {
            if (!this.editingId) return;

            fetch('{{ route("roles.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    role_name: this.roleName,
                    description: this.roleDescription
                })
            })
            .then(res => res.json())
            .then(r => {
                const tbody = document.getElementById('role-table-body');
                const row = Array.from(tbody.querySelectorAll('tr')).find(tr => tr.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.name = r.role_name;
                row.dataset.description = r.description ?? '';

                row.children[0].textContent = 'R' + String(r.role_id).padStart(3,'0');
                row.children[1].textContent = r.role_name;
                row.children[2].textContent = r.description ?? '';

                this.closeModal();
                alert('Role updated successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while updating the role.');
            });
        }
    }
}

// helper so dynamically added rows can open edit modal
window.__roleOpenEditFromRow = function (event) {
    const root = document.querySelector('[x-data^="rolePage"]');
    if (!root || !root.__x) return;
    root.__x.$data.openEditModal(event);
};

// pagination + search
const roleRowsPerPage = 5;
const roleTableBody   = document.getElementById('role-table-body');
const roleEmptyInitial= document.getElementById('role-empty-initial');
const roleEmptySearch = document.getElementById('role-empty-search');

let roleAllRows = Array.from(roleTableBody.querySelectorAll('tr'))
    .filter(r => ![roleEmptyInitial, roleEmptySearch].includes(r));
let roleFilteredRows = [...roleAllRows];

const rolePaginationLinks = document.getElementById('role-pagination-links');
const rolePaginationInfo  = document.getElementById('role-pagination-info');
let roleCurrentPage = 1;

function refreshRoleRows() {
    roleAllRows = Array.from(roleTableBody.querySelectorAll('tr'))
        .filter(r => ![roleEmptyInitial, roleEmptySearch].includes(r));
    roleFilteredRows = [...roleAllRows];
}

function showRolePage(page) {
    const total = roleFilteredRows.length;
    const totalPages = Math.max(1, Math.ceil(total / roleRowsPerPage));

    if (total === 0) {
        if (roleEmptyInitial) roleEmptyInitial.style.display = '';
        if (roleEmptySearch && document.getElementById('role-search').value.trim()) {
            roleEmptyInitial.style.display = 'none';
            roleEmptySearch.style.display = '';
        }
        rolePaginationLinks.innerHTML = '';
        rolePaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        return;
    }

    if (roleEmptyInitial) roleEmptyInitial.style.display = 'none';
    if (roleEmptySearch)  roleEmptySearch.style.display  = 'none';

    const totalPagesClamped = Math.max(1, totalPages);
    if (page < 1) page = 1;
    if (page > totalPagesClamped) page = totalPagesClamped;
    roleCurrentPage = page;

    roleAllRows.forEach(r => r.style.display = 'none');

    const start = (page - 1) * roleRowsPerPage;
    const end   = start + roleRowsPerPage;
    roleFilteredRows.slice(start, end).forEach(r => r.style.display = '');

    renderRolePagination(totalPagesClamped);

    const startItem = total ? start + 1 : 0;
    const endItem   = Math.min(end, total);
    rolePaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${total} results`;
}

function renderRolePagination(totalPages) {
    rolePaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = roleCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
    if (roleCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showRolePage(roleCurrentPage - 1);
        });
    }
    rolePaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === roleCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === roleCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== roleCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showRolePage(i);
            });
        }
        rolePaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = roleCurrentPage === totalPages ? 'Next »' : '<a href="#">Next »</a>';
    if (roleCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showRolePage(roleCurrentPage + 1);
        });
    }
    rolePaginationLinks.appendChild(next);
}

// Search
document.getElementById('role-search').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();

    if (!query) {
        roleFilteredRows = [...roleAllRows];
    } else {
        roleFilteredRows = roleAllRows.filter(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            return cells.some(c => c.textContent.toLowerCase().includes(query));
        });
    }

    if (roleFilteredRows.length === 0) {
        roleAllRows.forEach(r => r.style.display = 'none');
        if (roleEmptyInitial) roleEmptyInitial.style.display = 'none';
        if (roleEmptySearch)  roleEmptySearch.style.display  = '';
        rolePaginationLinks.innerHTML = '';
        rolePaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
    } else {
        if (roleEmptySearch) roleEmptySearch.style.display = 'none';
        showRolePage(1);
    }
});

// init
showRolePage(1);
</script>

<script>
function archiveRole(button) {
    if (!confirm('Are you sure you want to archive this role?')) return;

    const row = button.closest('tr');
    if (!row) return;

    const id = row.dataset.id;

    fetch('{{ route("roles.archive", ":id") }}'.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ archive: 'Archived' })
    })
    .then(res => res.json())
    .then(() => {
        row.classList.add('opacity-50');
        alert('Role archived successfully!');
        location.reload();
    })
    .catch(() => {
        alert('Failed to archive role. Please try again.');
    });
}

function unarchiveRole(id) {
    if (!confirm('Remove this role from archive?')) return;

    fetch('{{ route("roles.unarchive", ":id") }}'.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ archive: null })
    })
    .then(res => res.json())
    .then(() => {
        window.location.reload();
    });
}

function deleteRolePermanently(id) {
    if (!confirm('Permanently delete this role? This cannot be undone.')) return;

    fetch('{{ route("roles.destroy", ":id") }}'.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        window.location.reload();
    });
}
</script>

@endsection