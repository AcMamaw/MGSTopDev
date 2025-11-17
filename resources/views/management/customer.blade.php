@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<style>[x-cloak] { display: none !important; }</style>

<div x-data="{ showAddCustomerModal: false, fname: '', mname: '', lname: '', contact_no: '', address: '' }">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage customer records including contact information and addresses.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">
        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="customer-search" placeholder="Search customers"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Customer -->
        <button @click="showAddCustomerModal = true"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Customer</span>
        </button>
    </div>
</div>

<!-- Customers Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
        <table id="customer-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">First Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Middle Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Last Name</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Address</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="customer-table-body" class="divide-y divide-gray-100">
                @foreach ($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-gray-800 font-medium">C{{ str_pad($customer->customer_id,3,'0',STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $customer->fname }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $customer->mname }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $customer->lname }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $customer->contact_no }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $customer->address }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </button>
                            <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-archive">
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

<!-- Add Customer Modal -->
<div x-show="showAddCustomerModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="showAddCustomerModal = false" class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Customer</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">First Name</label>
                    <input type="text" x-model="fname" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                    <input type="text" x-model="mname" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                    <input type="text" x-model="lname" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                    <input type="text" x-model="contact_no" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Address</label>
                    <input type="text" x-model="address" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="showAddCustomerModal = false" class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
            <button @click="
                if(!fname.trim() || !lname.trim() || !contact_no.trim()){
                    alert('First Name, Last Name, and Contact No are required!');
                    return;
                }
                fetch('{{ route('customers.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ fname, mname, lname, contact_no, address })
                })
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('customer-table-body');
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    const c = data.customer;
                    row.innerHTML = `
                        <td class='px-4 py-3 text-center text-gray-800 font-medium'>C${String(c.customer_id).padStart(3,'0')}</td>
                        <td class='px-4 py-3 text-center text-gray-600'>${c.fname}</td>
                        <td class='px-4 py-3 text-center text-gray-600'>${c.mname ?? ''}</td>
                        <td class='px-4 py-3 text-center text-gray-600'>${c.lname}</td>
                        <td class='px-4 py-3 text-center text-gray-600'>${c.contact_no}</td>
                        <td class='px-4 py-3 text-center text-gray-600'>${c.address ?? ''}</td>
                        <td class='px-4 py-3 text-center'>
                            <div class='flex items-center justify-center space-x-2'>
                                <button class='p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                        <path d='M12 20h9'/>
                                        <path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z'/>
                                    </svg>
                                </button>
                                <button onclick='deleteRow(this)' class='p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                        <path d='M3 4h18v4H3z'/>
                                        <path d='M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8'/>
                                        <path d='M10 12h4'/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);

                    // reset modal
                    fname=''; mname=''; lname=''; contact_no=''; address=''; showAddCustomerModal=false;
                    updateCustomerPagination();
                })
                .catch(err => console.error(err));
            " class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="customer-pagination-info">Showing 1 to 1 of 1 results</div>
    <ul id="customer-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
const customerRowsPerPage = 5;
const customerTableBody = document.getElementById('customer-table-body');

let customerRows = Array.from(customerTableBody.querySelectorAll('tr'));
const customerPaginationLinks = document.getElementById('customer-pagination-links');
const customerPaginationInfo = document.getElementById('customer-pagination-info');
let customerCurrentPage = 1;
let customerTotalPages = Math.ceil(customerRows.length / customerRowsPerPage);

function showCustomerPage(page){
    customerCurrentPage = page;
    customerRows.forEach(r => r.style.display='none');
    const start = (page-1)*customerRowsPerPage;
    const end = start+customerRowsPerPage;
    customerRows.slice(start,end).forEach(r => r.style.display='');
    renderCustomerPagination();
    const startItem = customerRows.length ? start+1 : 0;
    const endItem = end>customerRows.length ? customerRows.length : end;
    customerPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${customerRows.length} results`;
}

function renderCustomerPagination(){
    customerPaginationLinks.innerHTML='';
    const prev = document.createElement('li');
    prev.className='border rounded px-2 py-1';
    prev.innerHTML = customerCurrentPage===1 ? '« Prev' : '<a href="#">« Prev</a>';
    if(customerCurrentPage!==1) prev.querySelector('a').addEventListener('click',e=>{e.preventDefault();showCustomerPage(customerCurrentPage-1)});
    customerPaginationLinks.appendChild(prev);

    for(let i=1;i<=customerTotalPages;i++){
        const li=document.createElement('li');
        li.className='border rounded px-2 py-1'+(i===customerCurrentPage?' bg-sky-400 text-white':'');
        li.innerHTML = i===customerCurrentPage?i:`<a href="#">${i}</a>`;
        if(i!==customerCurrentPage) li.querySelector('a').addEventListener('click',e=>{e.preventDefault();showCustomerPage(i)});
        customerPaginationLinks.appendChild(li);
    }

    const next=document.createElement('li');
    next.className='border rounded px-2 py-1';
    next.innerHTML = customerCurrentPage===customerTotalPages?'Next »':'<a href="#">Next »</a>';
    if(customerCurrentPage!==customerTotalPages) next.querySelector('a').addEventListener('click',e=>{e.preventDefault();showCustomerPage(customerCurrentPage+1)});
    customerPaginationLinks.appendChild(next);
}

function deleteRow(button){
    if(confirm('Are you sure you want to remove this row?')){
        button.closest('tr').remove();
        updateCustomerPagination();
    }
}

function updateCustomerPagination(){
    customerRows = Array.from(customerTableBody.querySelectorAll('tr'));
    customerTotalPages = Math.ceil(customerRows.length/customerRowsPerPage);
    showCustomerPage(1);
}

// Search
document.getElementById('customer-search').addEventListener('input',function(){
    const query=this.value.toLowerCase();
    customerRows.forEach(row=>{
        const cells=Array.from(row.querySelectorAll('td'));
        const match=cells.some(c=>c.textContent.toLowerCase().includes(query));
        row.style.display=match?'':'none';
    });
});

// Initialize
showCustomerPage(1);
</script>
@endsection
