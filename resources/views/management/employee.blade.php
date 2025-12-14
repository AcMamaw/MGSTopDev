@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<style>[x-cloak] { display: none !important; }</style>

<div x-data="employeePage()" class="px-4 sm:px-6 lg:px-8">

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Employees
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">
            Manage employee records including roles, personal details, and contact information.
        </p>
    </header>

   {{-- <div x-show="alertVisible" 
          x-transition
          class="max-w-7xl mx-auto mb-6"
          x-cloak>
        <div :class="alertType === 'success'
                ? 'bg-green-50 border-l-4 border-green-400 text-green-700'
                : 'bg-red-50 border-l-4 border-red-400 text-red-700'"
              class="px-4 py-3 rounded-lg flex justify-between items-center shadow-sm text-sm">
            <span x-text="alertMessage"></span>
            <button type="button"
                    @click="alertVisible=false"
                    class="font-bold text-lg leading-none ml-4 opacity-70 hover:opacity-100 transition">
                &times;
            </button>
        </div>
    </div>  --}}

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
        {{-- Left: Search --}}
        <div class="relative w-full md:w-80">
            <input type="text" id="employee-search" placeholder="Search employees"
                class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm
                        focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        {{-- Right: Archive + Add --}}
        <div class="w-full md:w-auto flex items-center justify-end space-x-3">
            @include('added.archive_employee')

            <button @click="openAddModal()"
                    class="w-full md:w-auto bg-yellow-400 text-gray-900 px-6 py-2 rounded-full font-bold
                        flex items-center justify-center space-x-2 hover:bg-yellow-500 transition
                        shadow-lg shadow-yellow-200/50">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                </svg>
                <span>Add New Employee</span>
            </button>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto overflow-x-auto border-t-4 border-yellow-400">
        <table id="employee-table" class="min-w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Employee ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">First Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Last Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Gender</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Birth Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Contact No</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody id="employee-table-body" class="divide-y divide-gray-100">
                @foreach ($employees->where('archive', '!=', 'Archived') as $emp)
                    <tr class="hover:bg-yellow-50/50 transition-colors employee-row"
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
                        <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                            EMP{{ str_pad($emp->employee_id,3,'0',STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="role">
                            {{ $emp->role->role_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="fname">
                            {{ $emp->fname }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="lname">
                            {{ $emp->lname }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="gender">
                            {{ $emp->gender }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="bdate">
                            {{ $emp->bdate }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="email">
                            {{ $emp->email }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600" data-cell="contact_no">
                            {{ $emp->contact_no }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button
                                    @click="openUserModal(
                                        '{{ $emp->user->username ?? '' }}',
                                        '{{ $emp->user->plain_password ?? '' }}',
                                        '{{ $emp->user->password ?? '' }}'
                                    )"
                                    title="Account Info"
                                    class="p-2 rounded-full text-blue-500 hover:text-blue-700 hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                        <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                        <circle cx="12" cy="17" r="2" fill="currentColor"/>
                                        <line x1="12" y1="12" x2="12" y2="15" />
                                    </svg>
                                </button>
                                <button
                                    title="Edit"
                                    @click="openEditModal($event)"
                                    class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-100 transition-colors duration-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>
                                <button title="Archive" onclick="archiveEmployee(this)"
                                    class="p-2 rounded-full text-red-500 hover:text-red-700 hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 4h18v4H3z" />
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                    <path d="M10 12h4" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="showEmployeeModal" x-cloak x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="closeEmployeeModal()" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl">
            <h2 class="text-3xl font-extrabold mb-7 text-center text-gray-800"
                x-text="isEdit ? 'Edit Employee Record' : 'Add New Employee'"></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">First Name</label>
                        <input type="text"
                            x-model="fname"
                            @focus="$event.target.select()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Enter first name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name</label>
                        <input type="text"
                            x-model="lname"
                            @focus="$event.target.select()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Enter last name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
                        <select x-model="gender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:ring-2 focus:ring-yellow-400 focus:outline-none focus:border-yellow-400 transition">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                        <select x-model="role_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:ring-2 focus:ring-yellow-400 focus:outline-none focus:border-yellow-400 transition">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email"
                            x-model="email"
                            @focus="$event.target.select()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Enter email address">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contact No</label>
                        <input type="text"
                            x-model="contact_no"
                            @focus="$event.target.select()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Enter contact number">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Birth Date</label>
                        <input type="date"
                            x-model="bdate"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:ring-2 focus:ring-yellow-400 focus:outline-none focus:border-yellow-400 transition">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button @click="closeEmployeeModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button x-show="!isEdit" @click="addEmployee()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Confirm
                </button>

                <button x-show="isEdit" @click="updateEmployee()"
                        class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Update
                </button>
            </div>
        </div>
    </div>

    <div x-show="showUserModal" x-cloak x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
        <div @click.away="showUserModal=false"
            class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
            <h2 class="text-3xl font-extrabold mb-7 text-left text-gray-800">Employee User Account</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 text-left">Username</label>
                    <input type="text"
                        x-model="username"
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 font-mono text-left">
                </div>

                <div x-data="{ showPassword: false }">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 text-left">
                        Password
                        <span x-show="isPasswordHashed" class="text-xs font-normal text-gray-500">(hashed/stored)</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                            x-model="password"
                            readonly
                            :class="isPasswordHashed ? 'text-xs' : ''"
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 font-mono text-left">

                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 p-1 rounded-full">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>

                           <svg x-show="showPassword"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                                        a10.05 10.05 0 012.382-4.568M6.223 6.223A9.956 9.956 0 0112 5
                                        c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.043 5.197M15 12
                                        a3 3 0 00-3-3M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <p x-show="isPasswordHashed" class="mt-4 text-xs text-gray-500 text-left">
                ⚠️ This is the hashed password stored in the database. It cannot be reversed to the original text. You must use a separate reset feature to provide a new password.
            </p>

            <div class="mt-6 flex justify-end">
                <button
                    type="button"
                    @click="showUserModal = false; window.location.reload()"
                    class="px-6 py-2 rounded-full text-black bg-yellow-400 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Close
                </button>
            </div>
        </div>
    </div>

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

        alertVisible:false,
        alertMessage:'',
        alertType:'success',

        fname:'', lname:'', gender:'', role_id:'', email:'', contact_no:'', bdate:'',
        username:'', password:'',

        roles:@json($roles),

        showAlert(message, type='success') {
            this.alertMessage = message;
            this.alertType = type;
            this.alertVisible = true;
            setTimeout(() => { this.alertVisible = false; }, 4000);
        },

        openAddModal() {
            this.isEdit = false;
            this.editingId = null;
            this.fname=''; this.lname=''; this.gender=''; this.role_id='';
            this.email=''; this.contact_no=''; this.bdate='';
            this.showEmployeeModal = true;
        },

        openEditModal(event) {
            const row = event.currentTarget.closest('tr.employee-row');
            if (!row) return;

            this.isEdit    = true;
            this.editingId = row.dataset.id;

            this.fname      = row.dataset.fname || '';
            this.lname      = row.dataset.lname || '';
            this.gender     = row.dataset.gender || '';
            this.role_id    = row.dataset.role_id || '';
            this.email      = row.dataset.email || '';
            this.contact_no = row.dataset.contact_no || '';
            this.bdate      = row.dataset.bdate || '';

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
                const msg = 'First Name, Last Name, and Role are required.';
                this.showAlert(msg, 'error');
                alert(msg);
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch('{{ route("employees.store") }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':csrfToken,
                    'Accept':'application/json'
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
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = data.message || 'Error adding employee.';
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                    this.showAlert(firstError || msg, 'error');
                    alert(firstError || msg);
                    throw new Error(msg);
                }
                return data;
            })
            .then(data => {
                const e = data.employee;
                const tbody = document.getElementById('employee-table-body');
                const row = document.createElement('tr');
                row.className = 'hover:bg-yellow-50/50 transition-colors employee-row';
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
                    <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                        EMP${String(e.employee_id).padStart(3,'0')}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="role">${roleName}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="fname">${e.fname}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="lname">${e.lname}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="gender">${e.gender}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="bdate">${e.bdate}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="email">${e.email ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600" data-cell="contact_no">${e.contact_no ?? ''}</td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        <div class="flex items-center justify-center space-x-2">
                            <button
                                onclick="Alpine.store('emp').openUserModal('${data.username ?? ''}', '${data.plain_password ?? ''}', '${data.password_hash ?? ''}')"
                                title="Account Info"
                                class="p-2 rounded-full text-blue-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                    <rcle cx="12" cy="17" r="2" fill="currentColor"/>
                                    <line x1="12" y1="12" x2="12" y2="15" />
                                </svg>
                            </button>
                            <button title="Edit"
                                onclick="Alpine.store('emp').openEditModal(event)"
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
                this.showAlert('Employee created successfully.', 'success');
                alert('Employee created successfully.');
            })
            .catch(err => {
                console.error(err);
                // generic fallback
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
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = data.message || 'Error updating employee.';
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                    this.showAlert(firstError || msg, 'error');
                    alert(firstError || msg);
                    throw new Error(msg);
                }
                return data;
            })
            .then(e => {
                const tbody = document.getElementById('employee-table-body');
                const row = Array.from(tbody.querySelectorAll('.employee-row'))
                    .find(r => r.dataset.id == this.editingId);
                if (!row) return;

                row.dataset.role_id    = e.role_id;
                row.dataset.fname      = e.fname;
                row.dataset.lname      = e.lname;
                row.dataset.gender     = e.gender;
                row.dataset.bdate      = e.bdate;
                row.dataset.email      = e.email ?? '';
                row.dataset.contact_no = e.contact_no ?? '';

                const roleName = this.roles.find(r => r.role_id == e.role_id)?.role_name ?? 'N/A';

                row.children[0].textContent = 'EMP' + String(e.employee_id).padStart(3,'0');
                row.querySelector('[data-cell="role"]').textContent       = roleName;
                row.querySelector('[data-cell="fname"]').textContent      = e.fname;
                row.querySelector('[data-cell="lname"]').textContent      = e.lname;
                row.querySelector('[data-cell="gender"]').textContent     = e.gender;
                row.querySelector('[data-cell="bdate"]').textContent      = e.bdate;
                row.querySelector('[data-cell="email"]').textContent      = e.email ?? '';
                row.querySelector('[data-cell="contact_no"]').textContent = e.contact_no ?? '';

                this.closeEmployeeModal();
                this.showAlert('Employee updated successfully.', 'success');
                alert('Employee updated successfully.');
            })
            .catch(err => {
                console.error(err);
                // generic fallback
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
        openEditModal(event) {
            const root = document.querySelector('[x-data^="employeePage"]');
            if (!root || !root.__x) return;
            root.__x.$data.openEditModal(event);
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
    employeePaginationInfo.textContent =
        `Showing ${startItem} to ${endItem} of ${employeeRows.length} results`;
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
        const li = document.createElement('li');
        li.className='border rounded px-2 py-1' +
            (i === employeeCurrentPage ? ' bg-yellow-400 text-black' : '');
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


function updateEmployeePagination() {
    employeeRows = Array.from(employeeTableBody.querySelectorAll('tr'));
    employeeTotalPages = Math.ceil(employeeRows.length / employeeRowsPerPage);
    if (employeeCurrentPage > employeeTotalPages && employeeTotalPages > 0) {
        employeeCurrentPage = employeeTotalPages;
    }
    showEmployeePage(employeeCurrentPage || 1);
}

document.getElementById('employee-search').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();

    if (!query) {
        showEmployeePage(1);
        return;
    }

    employeeRows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const match = cells.some(c => c.textContent.toLowerCase().includes(query));
        row.style.display = match ? '' : 'none';
    });
});

showEmployeePage(1);
</script>

<script>
function archiveEmployee(button) {
    if (!confirm('Are you sure you want to archive this employee?')) return;

    const row = button.closest('tr');
    if (!row) return;

    const id = row.dataset.id;

    fetch('{{ route("employees.archive", ":id") }}'.replace(':id', id), {
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
    .then(() => {
        row.classList.add('opacity-50');
        alert('Employee archived successfully!');
        location.reload();
    })
    .catch(() => {
        alert('Failed to archive employee. Please try again.');
    });
}
</script>

@endsection
