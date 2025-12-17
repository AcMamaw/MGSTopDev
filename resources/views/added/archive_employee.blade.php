<div x-data="{
        showArchiveModal: false,
        searchQuery: '',
        filterArchived() {
            const q = this.searchQuery.toLowerCase().trim();
            const rows = document.querySelectorAll('#archiveEmployeesBody tr[data-employee]');
            let hasVisible = false;

            rows.forEach(row => {
                const searchable = row.getAttribute('data-search').toLowerCase();
                if (!q || searchable.includes(q)) {
                    row.style.display = '';
                    hasVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });

            const empty = document.getElementById('archiveEmployeesEmpty');
            if (empty) empty.style.display = hasVisible ? 'none' : '';
        }
    }"
>
    <div class="w-full flex justify-end">
        <button @click="showArchiveModal = true"
                class="bg-gray-200 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center space-x-2
                    hover:bg-gray-300 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 6v6l4 2" />
            </svg>
            <span>Archive History</span>
        </button>
    </div>

    <div x-show="showArchiveModal" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showArchiveModal = false"
             class="bg-white w-full max-w-5xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex items-center justify-between">
                <span>Archived Employees</span>
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="filterArchived()"
                    placeholder="Search archived employees"
                    class="ml-4 w-64 px-3 py-1.5 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-1 focus:ring-yellow-500"
                >
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="archiveEmployeesBody" class="divide-y divide-gray-100">
                        @forelse ($employees->where('archive', 'Archived') as $employee)
                            <tr
                                data-employee
                                data-id="{{ $employee->employee_id }}"
                                data-search="{{ $employee->fname }} {{ $employee->lname }} {{ $employee->email }} {{ $employee->role->role_name ?? '' }}"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-4 py-2 text-center text-sm font-semibold text-yellow-700">
                                    E{{ str_pad($employee->employee_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-800">
                                    {{ $employee->fname }} {{ $employee->lname }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-700">
                                    {{ $employee->role->role_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2 text-left text-sm text-gray-700">
                                    {{ $employee->email }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm text-gray-700">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Remove from Archive --}}
                                        <button
                                            title="Remove from Archive"
                                            onclick="unarchiveEmployee({{ $employee->employee_id }})"
                                            class="inline-flex items-center justify-center h-9 w-9 rounded-full text-green-500
                                                hover:text-green-700 hover:bg-green-100 transition-colors duration-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 width="25" height="25"
                                                 fill="none" stroke="currentColor"
                                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <g transform="scale(0.9) translate(1.5,1.5)">
                                                    <path d="M3 4h18v4H3z" />
                                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                                    <path d="M10 12h4" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="archiveEmployeesEmpty">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No archived employees.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="showArchiveModal = false"
                        class="bg-yellow-500 text-gray-900 font-bold px-8 py-2 rounded-full hover:bg-yellow-600 transition shadow-md">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function unarchiveEmployee(id) {
    if (!confirm('Remove this employee from archive?')) return;

    fetch('{{ route("employees.unarchive", ":id") }}'.replace(':id', id), {
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

function deleteEmployeePermanently(id) {
    if (!confirm('Permanently delete this employee? This cannot be undone.')) return;

    fetch('{{ route("employees.destroy", ":id") }}'.replace(':id', id), {
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
