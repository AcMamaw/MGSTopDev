@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<style>[x-cloak] { display: none !important; }</style>

<div x-data="employeePage()">

    <!-- Header -->
    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Employees</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage employee records including roles, personal details, and contact information.</p>
    </header>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
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
    <div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="employee-search" placeholder="Search employees"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Employee -->
        <button @click="openAddModal()"
                class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>
            <span>Add New Employee</span>
        </button>
    </div>

    <!-- Employee Table -->
    <div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
        <table id="employee-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Employee ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">First Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Last Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Gender</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Birth Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="employee-table-body" class="divide-y divide-gray-100">
                @foreach ($employees as $emp)
                    <tr class="hover:bg-gray-50"
                        data-id="{{ $emp->employee_id }}"
                        data-role_id="{{ $emp->role_id }}"
                        data-fname="{{ $emp->fname }}"
                        data-lname="{{ $emp->lname }}"
                        data-gender="{{ $emp->gender }}"
                        data-bdate="{{ $emp->bdate }}"
                        data-email="{{ $emp->email }}"
                        data-contact_no="{{ $emp->contact_no }}"
                        data-username="{{ $emp->user->username ?? '' }}"
                        data-password_plain="{{ $emp->user->plain_password ?? '' }}"
                        data-password_hash="{{ $emp->user->password ?? '' }}">
                        <td class="px-4 py-3 text-center text-gray-500 font-medium">
                            EMP{{ str_pad($emp->employee_id,3,'0',STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->role->role_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->fname }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->lname }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->gender }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->bdate }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->email }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $emp->contact_no }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Account Info -->
                                <button
                                    @click="openUserModal(
                                        '{{ $emp->user->username ?? '' }}',
                                        '{{ $emp->user->plain_password ?? '' }}',
                                        '{{ $emp->user->password ?? '' }}'
                                    )"
                                    title="Account Info"
                                    class="p-2 rounded-full text-blue-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                        <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                        <circle cx="12" cy="17" r="2" fill="currentColor"/>
                                        <line x1="12" y1="12" x2="12" y2="15" />
                                    </svg>
                                </button>
                                <!-- Edit -->
                                <button
                                    title="Edit"
                                    @click="openEditModal(
                                        {{ $emp->employee_id }},
                                        '{{ $emp->fname }}',
                                        '{{ $emp->lname }}',
                                        '{{ $emp->gender }}',
                                        {{ $emp->role_id }},
                                        '{{ $emp->email }}',
                                        '{{ $emp->contact_no }}',
                                        '{{ $emp->bdate }}'
                                    )"
                                    class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>
                                <!-- Archive -->
                                <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 4h18v4H3z"/>
                                        <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                        <path d="M10 12h4"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

   <!-- Add / Edit Employee Modal -->
<div x-show="showEmployeeModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     style="display: none;">
    <div @click.away="closeEmployeeModal()" class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl">
        <h2 class="text-2xl font-bold mb-6 text-gray-800"
            x-text="isEdit ? 'Edit Employee' : 'Add New Employee'"></h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">First Name</label>
                    <input type="text"
                           x-model="fname"
                           @focus="fname=''"
                           class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Last Name</label>
                    <input type="text"
                           x-model="lname"
                           @focus="lname=''"
                           class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Gender</label>
                    <select x-model="gender"
                            @focus="gender=''"
                            class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Role</label>
                    <select x-model="role_id"
                            @focus="role_id=''"
                            class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email"
                           x-model="email"
                           @focus="email=''"
                           class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Contact No</label>
                    <input type="text"
                           x-model="contact_no"
                           @focus="contact_no=''"
                           class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Birth Date</label>
                    <input type="date"
                           x-model="bdate"
                           @focus="bdate=''"
                           class="w-full px-4 py-2 border rounded-lg text-gray-700 text-left focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="closeEmployeeModal()"
                    class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                Cancel
            </button>

            <button x-show="!isEdit" @click="addEmployee()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 text-black transition">
                Confirm
            </button>

            <button x-show="isEdit" @click="updateEmployee()"
                    class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 text-black transition">
                Update
            </button>
        </div>
    </div>
</div>


    <!-- User Account Info Modal -->
    <div x-show="showUserModal" x-cloak x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div @click.away="showUserModal=false"
             class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Employee User Account</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Username</label>
                    <input type="text"
                           x-model="username"
                           readonly
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-700">
                </div>

                <div x-data="{ showPassword: false }">
                    <label class="block text-gray-700 font-medium mb-1">
                        Password
                        <span x-show="isPasswordHashed" class="text-xs text-gray-500">(hashed)</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               x-model="password"
                               readonly
                               :class="isPasswordHashed ? 'text-xs' : ''"
                               class="w-full px-4 py-2 pr-10 border rounded-lg bg-gray-100 text-gray-700">

                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 bottom-2.5 text-gray-500 hover:text-gray-700">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                                      7-4.477 0-8.268-2.943-9.542-7z"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2" stroke="currentColor"/>
                            </svg>

                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3l18 18M10.58 10.58A3 3 0 0113.42 13.42M9.88 4.55A9.956 9.956 0 0112 5c4.477 
                                      0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.071 4.934"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6.26 6.26A9.955 9.955 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.61 
                                      0 3.146-.38 4.5-1.05"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <p x-show="isPasswordHashed" class="mt-4 text-xs text-gray-500 text-left">
                This is the hashed password stored in the database. It cannot be reversed to the original text.
                Use a reset-password feature to set a new password if needed.
            </p>

            <div class="mt-6 flex justify-end">
                <button @click="showUserModal=false"
                        class="px-6 py-2 rounded-lg text-black bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
        <div id="employee-pagination-info">Showing 1 to 1 of 1 results</div>
        <ul id="employee-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>

</div>

<script>
function employeePage() {
    return {
        showEmployeeModal:false,
        isEdit:false,
        editingId:null,

        showUserModal:false,
        isPasswordHashed:false,

        fname:'', lname:'', gender:'', role_id:'', email:'', contact_no:'', bdate:'',
        username:'', password:'',

        roles:@json($roles),

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.fname=''; this.lname=''; this.gender=''; this.role_id='';
            this.email=''; this.contact_no=''; this.bdate='';
            this.showEmployeeModal = true;
        },

        openEditModal(id, fname, lname, gender, role_id, email, contact_no, bdate) {
            this.isEdit = true;
            this.editingId = id;
            this.fname = fname;
            this.lname = lname;
            this.gender = gender;
            this.role_id = role_id;
            this.email = email;
            this.contact_no = contact_no;
            this.bdate = bdate;
            this.showEmployeeModal = true;
        },

        openUserModal(username, plain, hash) {
            this.username = username || '';
            if (plain) {
                this.password = plain;
                this.isPasswordHashed = false;
            } else {
                this.password = hash || '';
                this.isPasswordHashed = true;
            }
            this.showUserModal = true;
        },

        closeEmployeeModal() {
            this.showEmployeeModal = false;
        },

        addEmployee() {
            if(!this.fname.trim() || !this.lname.trim() || !this.role_id){
                alert('First Name, Last Name, and Role are required!');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch('{{ route("employees.store") }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':csrfToken
                },
                body:JSON.stringify({
                    fname:this.fname,
                    lname:this.lname,
                    gender:this.gender,
                    role_id:this.role_id,
                    email:this.email,
                    contact_no:this.contact_no,
                    bdate:this.bdate
                })
            })
            .then(res => res.json())
            .then(data => {
                const e = data.employee;
                const tbody = document.getElementById('employee-table-body');
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.dataset.id = e.employee_id;
                row.dataset.role_id = e.role_id;
                row.dataset.fname = e.fname;
                row.dataset.lname = e.lname;
                row.dataset.gender = e.gender;
                row.dataset.bdate = e.bdate;
                row.dataset.email = e.email ?? '';
                row.dataset.contact_no = e.contact_no ?? '';
                row.dataset.username = data.username ?? '';
                row.dataset.password_plain = data.plain_password ?? '';
                row.dataset.password_hash = data.password_hash ?? '';

                const roleName = this.roles.find(r => r.role_id == e.role_id)?.role_name ?? 'N/A';

                row.innerHTML = `
                    <td class="px-4 py-3 text-center font-medium text-gray-500">EMP${String(e.employee_id).padStart(3,'0')}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${roleName}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.fname}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.lname}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.gender}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.bdate}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.email ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-500">${e.contact_no ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-500">
                        <div class="flex items-center justify-center space-x-2">
                            <button 
                                onclick="Alpine.store('emp').openUserModal('${data.username ?? ''}', '${data.plain_password ?? ''}', '${data.password_hash ?? ''}')"
                                title="Account Info"
                                class="p-2 rounded-full text-blue-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                    <circle cx="12" cy="17" r="2" fill="currentColor"/>
                                    <line x1="12" y1="12" x2="12" y2="15" />
                                </svg>
                            </button>
                            <button title="Edit"
                                onclick="Alpine.store('emp').openEditModal(${e.employee_id}, '${e.fname}', '${e.lname}', '${e.gender}', ${e.role_id}, '${e.email ?? ''}', '${e.contact_no ?? ''}', '${e.bdate}')"
                                class="p-2 rounded-full text-green-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 4h18v4H3z"/>
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                    <path d="M10 12h4"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(row);

                this.openUserModal(data.username, data.plain_password, data.password_hash);

                this.fname=''; this.lname=''; this.gender=''; this.role_id='';
                this.email=''; this.contact_no=''; this.bdate='';
                this.showEmployeeModal=false;

                updateEmployeePagination();
            })
            .catch(err => {
                console.error(err);
                alert('Error adding employee. Please try again.');
            });
        },

        updateEmployee() {
            if (!this.editingId) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch('{{ route("employees.update", 0) }}'.replace('/0', '/' + this.editingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fname: this.fname,
                    lname: this.lname,
                    gender: this.gender,
                    role_id: this.role_id,
                    email: this.email,
                    contact_no: this.contact_no,
                    bdate: this.bdate
                })
            })
            .then(async res => {
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    console.error('Update failed:', res.status, data);
                    throw new Error('Update failed');
                }
                return res.json();
            })
            .then(e => {
                const tbody = document.getElementById('employee-table-body');
                const row = Array.from(tbody.querySelectorAll('tr'))
                    .find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.role_id = e.role_id;
                row.dataset.fname = e.fname;
                row.dataset.lname = e.lname;
                row.dataset.gender = e.gender;
                row.dataset.bdate = e.bdate;
                row.dataset.email = e.email ?? '';
                row.dataset.contact_no = e.contact_no ?? '';

                const roleName = this.roles.find(r => r.role_id == e.role_id)?.role_name ?? 'N/A';

                row.children[0].textContent = 'EMP' + String(e.employee_id).padStart(3,'0');
                row.children[1].textContent = roleName;
                row.children[2].textContent = e.fname;
                row.children[3].textContent = e.lname;
                row.children[4].textContent = e.gender;
                row.children[5].textContent = e.bdate;
                row.children[6].textContent = e.email ?? '';
                row.children[7].textContent = e.contact_no ?? '';

                this.closeEmployeeModal();
                alert('Employee updated successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Error updating employee. Please try again.');
            });
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.store('emp', {
        openUserModal(username, plain, hash) {
            const root = document.querySelector('[x-data^="employeePage"]');
            if (!root || !root.__x) return;
            root.__x.$data.openUserModal(username, plain, hash);
        },
        openEditModal(id, fname, lname, gender, role_id, email, contact_no, bdate) {
            const root = document.querySelector('[x-data^="employeePage"]');
            if (!root || !root.__x) return;
            root.__x.$data.openEditModal(id, fname, lname, gender, role_id, email, contact_no, bdate);
        }
    });
});

// Pagination + search (unchanged)
const employeeRowsPerPage = 5;
const employeeTableBody = document.getElementById('employee-table-body');
let employeeRows = Array.from(employeeTableBody.querySelectorAll('tr'));
const employeePaginationLinks = document.getElementById('employee-pagination-links');
const employeePaginationInfo = document.getElementById('employee-pagination-info');
let employeeCurrentPage = 1;
let employeeTotalPages = Math.ceil(employeeRows.length / employeeRowsPerPage);

function showEmployeePage(page) {
    employeeCurrentPage = page;
    employeeRows.forEach(r => r.style.display = 'none');
    const start = (page - 1) * employeeRowsPerPage;
    const end = start + employeeRowsPerPage;
    employeeRows.slice(start, end).forEach(r => r.style.display = '');
    renderEmployeePagination();
    const startItem = employeeRows.length ? start + 1 : 0;
    const endItem = end > employeeRows.length ? employeeRows.length : end;
    employeePaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${employeeRows.length} results`;
}

function renderEmployeePagination() {
    employeePaginationLinks.innerHTML = '';

    const prev = document.createElement('li'); prev.className='border rounded px-2 py-1';
    prev.innerHTML = employeeCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
    if (employeeCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showEmployeePage(employeeCurrentPage - 1);
        });
    }
    employeePaginationLinks.appendChild(prev);

    for (let i = 1; i <= employeeTotalPages; i++) {
        const li = document.createElement('li'); li.className='border rounded px-2 py-1' + (i === employeeCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === employeeCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== employeeCurrentPage) {
            li.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                showEmployeePage(i);
            });
        }
        employeePaginationLinks.appendChild(li);
    }

    const next = document.createElement('li'); next.className='border rounded px-2 py-1';
    next.innerHTML = employeeCurrentPage === employeeTotalPages ? 'Next »' : '<a href="#">Next »</a>';
    if (employeeCurrentPage !== employeeTotalPages) {
        next.querySelector('a').addEventListener('click', e => {
            e.preventDefault();
            showEmployeePage(employeeCurrentPage + 1);
        });
    }
    employeePaginationLinks.appendChild(next);
}

function deleteRow(button) {
    if (confirm('Are you sure you want to archive this employee?')) {
        button.closest('tr').remove();
        updateEmployeePagination();
    }
}

function updateEmployeePagination() {
    employeeRows = Array.from(employeeTableBody.querySelectorAll('tr'));
    employeeTotalPages = Math.ceil(employeeRows.length / employeeRowsPerPage);
    if (employeeCurrentPage > employeeTotalPages && employeeTotalPages > 0) {
        employeeCurrentPage = employeeTotalPages;
    }
    showEmployeePage(employeeCurrentPage);
}

document.getElementById('employee-search').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();

    if (!query) {
        // reset: let pagination show first 5 rows again
        showEmployeePage(1);
        return;
    }

    // normal search: hide non‑matching rows
    employeeRows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const match = cells.some(c => c.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});

showEmployeePage(1);
</script>
@endsection
