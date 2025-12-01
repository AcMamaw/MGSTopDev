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
        <button @click="showAddEmployeeModal = true"
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
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-500 font-medium">EMP{{ str_pad($emp->employee_id,3,'0',STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->role->role_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->fname }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->lname }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->gender }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->bdate }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->email }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $emp->contact_no }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">
                        <div class="flex items-center justify-center space-x-2">
                            <!-- User Password -->
                            <button @click="username='{{ $emp->user->username ?? '' }}'; password='{{ $emp->user->plain_password ?? '' }}'; showUserModal=true"
                                    title="Password"
                                    class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                    <circle cx="12" cy="17" r="2" fill="currentColor"/>
                                    <line x1="12" y1="12" x2="12" y2="15" />
                                </svg>
                            </button>
                            <!-- Edit -->
                            <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <!-- Archive -->
                            <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
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

    <!-- Add Employee Modal -->
    <div x-show="showAddEmployeeModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div @click.away="showAddEmployeeModal=false" class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Employee</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div><label class="block text-gray-700 mb-1">First Name</label><input type="text" x-model="fname" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-gray-700 mb-1">Last Name</label><input type="text" x-model="lname" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-gray-700 mb-1">Gender</label>
                        <select x-model="gender" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div><label class="block text-gray-700 mb-1">Role</label>
                        <select x-model="role_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Role</option>
                            <template x-for="role in roles" :key="role.role_id">
                                <option :value="role.role_id" x-text="role.role_name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="space-y-4">
                    <div><label class="block text-gray-700 mb-1">Email</label><input type="email" x-model="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-gray-700 mb-1">Contact No</label><input type="text" x-model="contact_no" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-gray-700 mb-1">Birth Date</label><input type="date" x-model="bdate" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"></div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button @click="showAddEmployeeModal=false" class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
                <button @click="addEmployee()" class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 text-white
                 transition">Confirm</button>
            </div>
        </div>
    </div>

    
  <!-- User Modal -->
    @include('added.user_employee')
  
    <!-- Pagination -->
    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
        <div id="employee-pagination-info">Showing 1 to 1 of 1 results</div>
        <ul id="employee-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>

</div>

<script>
function employeePage() {
    return {
        showAddEmployeeModal:false,
        showUserModal:false,
        fname:'', lname:'', gender:'', role_id:'', email:'', contact_no:'', bdate:'',
        username:'', password:'',
        roles:@json($roles),

        addEmployee() {
            if(!this.fname.trim() || !this.lname.trim() || !this.role_id){
                alert('First Name, Last Name, and Role are required!');
                return;
            }

            fetch('{{ route("employees.store") }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
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

                // 🔥 SET USERNAME & PASSWORD FROM BACKEND
                this.username = data.username;
                this.password = data.plain_password;

                // 🔥 SHOW PASSWORD MODAL IMMEDIATELY
                this.showUserModal = true;

                // REFRESH TABLE PAGINATION
                updateEmployeePagination();

                // Add new row to table
                const e = data.employee;
                const tbody = document.getElementById('employee-table-body');
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                row.innerHTML = `
                    <td class='px-4 py-3 text-center font-medium text-gray-500"'>EMP${String(e.employee_id).padStart(3,'0')}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.role?.role_name ?? 'N/A'}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.fname}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.lname}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.gender}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.bdate}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.email ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>${e.contact_no ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-500"'>
                       <!-- User Password -->
                            <button 
                                onclick="window.employeeShowPassword('{{ $emp->user->username ?? '' }}', '{{ $emp->user->plain_password ?? '' }}')"
                                    title="Password"
                                    class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                                
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="11" width="12" height="10" rx="2" ry="2"/>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                    <circle cx="12" cy="17" r="2" fill="currentColor"/>
                                    <line x1="12" y1="12" x2="12" y2="15" />
                                </svg>
                            </button>
                            <!-- Edit -->
                            <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <!-- Archive -->
                            <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 4h18v4H3z"/>
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                    <path d="M10 12h4"/>
                                </svg>
                            </button>
                         </td>
                      `;

                tbody.appendChild(row);

                // Reset
                this.fname=''; this.lname=''; this.gender=''; this.role_id='';
                this.email=''; this.contact_no=''; this.bdate='';
                this.showAddEmployeeModal=false;

                updateEmployeePagination();
            })
            .catch(err => console.error(err));
        }
    }
}

// Pagination & Search
const employeeRowsPerPage=5;
const employeeTableBody=document.getElementById('employee-table-body');
let employeeRows=Array.from(employeeTableBody.querySelectorAll('tr'));
const employeePaginationLinks=document.getElementById('employee-pagination-links');
const employeePaginationInfo=document.getElementById('employee-pagination-info');
let employeeCurrentPage=1;
let employeeTotalPages=Math.ceil(employeeRows.length/employeeRowsPerPage);

function showEmployeePage(page){
    employeeCurrentPage=page;
    employeeRows.forEach(r=>r.style.display='none');
    const start=(page-1)*employeeRowsPerPage;
    const end=start+employeeRowsPerPage;
    employeeRows.slice(start,end).forEach(r=>r.style.display='');
    renderEmployeePagination();
    const startItem=employeeRows.length?start+1:0;
    const endItem=end>employeeRows.length?employeeRows.length:end;
    employeePaginationInfo.textContent=`Showing ${startItem} to ${endItem} of ${employeeRows.length} results`;
}


function renderEmployeePagination(){
    employeePaginationLinks.innerHTML='';
    const prev=document.createElement('li'); prev.className='border rounded px-2 py-1';
    prev.innerHTML=employeeCurrentPage===1?'« Prev':'<a href="#">« Prev</a>';
    if(employeeCurrentPage!==1) prev.querySelector('a').addEventListener('click',e=>{e.preventDefault();showEmployeePage(employeeCurrentPage-1)});
    employeePaginationLinks.appendChild(prev);

    for(let i=1;i<=employeeTotalPages;i++){
        const li=document.createElement('li'); li.className='border rounded px-2 py-1'+(i===employeeCurrentPage?' bg-sky-400 text-white':'');
        li.innerHTML=i===employeeCurrentPage?i:`<a href="#">${i}</a>`;
        if(i!==employeeCurrentPage) li.querySelector('a').addEventListener('click',e=>{e.preventDefault();showEmployeePage(i)});
        employeePaginationLinks.appendChild(li);
    }

    const next=document.createElement('li'); next.className='border rounded px-2 py-1';
    next.innerHTML=employeeCurrentPage===employeeTotalPages?'Next »':'<a href="#">Next »</a>';
    if(employeeCurrentPage!==employeeTotalPages) next.querySelector('a').addEventListener('click',e=>{e.preventDefault();showEmployeePage(employeeCurrentPage+1)});
    employeePaginationLinks.appendChild(next);
}

function deleteRow(button){ if(confirm('Are you sure you want to remove this row?')){ button.closest('tr').remove(); updateEmployeePagination(); } }
function updateEmployeePagination(){ employeeRows=Array.from(employeeTableBody.querySelectorAll('tr')); employeeTotalPages=Math.ceil(employeeRows.length/employeeRowsPerPage); showEmployeePage(1); }

// Search
document.getElementById('employee-search').addEventListener('input', function(){
    const query=this.value.toLowerCase();
    employeeRows.forEach(row=>{
        const cells=Array.from(row.querySelectorAll('td'));
        const match=cells.some(c=>c.textContent.toLowerCase().includes(query));
        row.style.display=match?'':'none';
    });
});

// Initialize
showEmployeePage(1);
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('emp', {
        openUserModal(username, password) {
            const root = document.querySelector('[x-data]');
            root.__x.$data.username = username;
            root.__x.$data.password = password;
            root.__x.$data.showUserModal = true;
        }
    });
});
</script>

@endsection
