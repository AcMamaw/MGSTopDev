@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<style>[x-cloak]{display:none !important;}</style>

<div x-data="supplierPage()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage supplier records including contact details and addresses.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-col md:flex-row items-stretch justify-between gap-4">
    <!-- Search -->
    <div class="relative w-full md:w-1/4">
        <input type="text" id="supplier-search" placeholder="Search suppliers"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
    </div>

    <!-- Add Supplier -->
    <button @click="showAddSupplierModal = true"
        class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>
        <span>Add New Supplier</span>
    </button>
</div>

<!-- Supplier Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
    <table id="supplier-table" class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier Name</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact Person</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Email</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Address</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
            </tr>
        </thead>
        <tbody id="supplier-table-body" class="divide-y divide-gray-100">
            @foreach($suppliers as $supplier)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-center text-gray-800 font-medium">SUP{{ str_pad($supplier->supplier_id,3,'0',STR_PAD_LEFT) }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->supplier_name }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_person }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->contact_no }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->email }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $supplier->address }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                            </svg>
                        </button>
                        <button title="Archive" onclick="deleteRow(this)" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

<!-- Add Supplier Modal -->
<div x-show="showAddSupplierModal" x-cloak x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div @click.away="showAddSupplierModal = false"
        x-transition class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md relative text-center">

        <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Supplier</h2>

        <div class="space-y-4 flex flex-col items-center">
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Supplier Name</label>
                <input type="text" x-model="supplierName"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-center"
                    placeholder="">
            </div>
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Contact Person</label>
                <input type="text" x-model="contactPerson"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-center"
                    placeholder="">
            </div>
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                <input type="text" x-model="contactNo"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-center"
                    placeholder="">
            </div>
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" x-model="email"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-center"
                    placeholder="">
            </div>
            <div class="w-full">
                <label class="block text-gray-700 font-medium mb-1">Address</label>
                <input type="text" x-model="address"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-center"
                    placeholder="">
            </div>
        </div>

        <div class="mt-6 flex justify-center gap-3">
            <button @click="showAddSupplierModal = false"
                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                Cancel
            </button>

            <button @click="addSupplier()"
                class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="supplier-pagination-info">Showing 1 to 1 of 1 results</div>
    <ul id="supplier-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
function supplierPage() {
    return {
        showAddSupplierModal: false,
        supplierName: '',
        contactPerson: '',
        contactNo: '',
        email: '',
        address: '',

        addSupplier() {
            // Basic validation
            if (!this.supplierName.trim()) {
                alert('Supplier Name is required!');
                return;
            }

            fetch('{{ route("suppliers.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    supplier_name: this.supplierName,
                    contact_person: this.contactPerson,
                    contact_no: this.contactNo,
                    email: this.email,
                    address: this.address
                })
            })
            .then(res => res.json())
            .then(s => {
                // s is now the supplier object returned from backend
                const tbody = document.getElementById('supplier-table-body');
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                row.innerHTML = `
                    <td class='px-4 py-3 text-center text-gray-800 font-medium'>SUP${String(s.supplier_id).padStart(3,'0')}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${s.supplier_name}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${s.contact_person}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${s.contact_no ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${s.email ?? ''}</td>
                    <td class='px-4 py-3 text-center text-gray-600'>${s.address ?? ''}</td>
                    <td class='px-4 py-3 text-center'>
                        <div class='flex items-center justify-center space-x-2'>
                            <button title="Edit" class="p-2 rounded-full text-gray-400 hover:text-green-600 hover:bg-green-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button title="Archive" onclick='deleteRow(this)' class='p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 4h18v4H3z"/>
                                    <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                                    <path d="M10 12h4"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                `;
                
                tbody.appendChild(row);

                // Reset modal inputs
                this.supplierName = '';
                this.contactPerson = '';
                this.contactNo = '';
                this.email = '';
                this.address = '';
                this.showAddSupplierModal = false;

                // Update pagination if any
                updateSupplierPagination();
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong while saving the supplier.');
            });
        }
    }
}


// Pagination logic
const supRowsPerPage = 5;
const supTableBody = document.getElementById('supplier-table-body');
let supRows = Array.from(supTableBody.querySelectorAll('tr'));
const supPaginationLinks = document.getElementById('supplier-pagination-links');
const supPaginationInfo = document.getElementById('supplier-pagination-info');
let supCurrentPage = 1;
let supTotalPages = Math.ceil(supRows.length / supRowsPerPage);

function showSupplierPage(page){
    supCurrentPage = page;
    supRows.forEach(r => r.style.display='none');
    const start=(page-1)*supRowsPerPage;
    const end=start+supRowsPerPage;
    supRows.slice(start,end).forEach(r=>r.style.display='');
    renderSupplierPagination();
    const startItem = supRows.length ? start+1 : 0;
    const endItem = end>supRows.length ? supRows.length : end;
    supPaginationInfo.textContent=`Showing ${startItem} to ${endItem} of ${supRows.length} results`;
}

function renderSupplierPagination(){
    supPaginationLinks.innerHTML='';
    const prev=document.createElement('li'); prev.className='border rounded px-2 py-1';
    prev.innerHTML=supCurrentPage===1?'« Prev':'<a href="#">« Prev</a>';
    if(supCurrentPage!==1) prev.querySelector('a').addEventListener('click',e=>{e.preventDefault();showSupplierPage(supCurrentPage-1)});
    supPaginationLinks.appendChild(prev);

    for(let i=1;i<=supTotalPages;i++){
        const li=document.createElement('li'); li.className='border rounded px-2 py-1'+(i===supCurrentPage?' bg-sky-400 text-white':'');
        li.innerHTML=i===supCurrentPage?i:`<a href="#">${i}</a>`;
        if(i!==supCurrentPage) li.querySelector('a').addEventListener('click',e=>{e.preventDefault();showSupplierPage(i)});
        supPaginationLinks.appendChild(li);
    }

    const next=document.createElement('li'); next.className='border rounded px-2 py-1';
    next.innerHTML=supCurrentPage===supTotalPages?'Next »':'<a href="#">Next »</a>';
    if(supCurrentPage!==supTotalPages) next.querySelector('a').addEventListener('click',e=>{e.preventDefault();showSupplierPage(supCurrentPage+1)});
    supPaginationLinks.appendChild(next);
}

function deleteRow(button){ if(confirm('Are you sure you want to remove this row?')){ button.closest('tr').remove(); updateSupplierPagination(); } }
function updateSupplierPagination(){ supRows=Array.from(supTableBody.querySelectorAll('tr')); supTotalPages=Math.ceil(supRows.length/supRowsPerPage); showSupplierPage(1); }

// Search
document.getElementById('supplier-search').addEventListener('input', function(){
    const query=this.value.toLowerCase();
    supRows.forEach(row=>{
        const cells=Array.from(row.querySelectorAll('td'));
        const match=cells.some(c=>c.textContent.toLowerCase().includes(query));
        row.style.display=match?'':'none';
    });
});

// Initialize
showSupplierPage(1);
</script>
@endsection
