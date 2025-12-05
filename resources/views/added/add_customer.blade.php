<!-- Add Customer Modal -->
<div x-show="showAddCustomerModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="closeCustomerModal()"
         class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">

        <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Customer</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Inputs -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">First Name</label>
                    <input type="text" x-model="fname"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                    <input type="text" x-model="mname"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                    <input type="text" x-model="lname"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            </div>

            <!-- Right Inputs -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                    <input type="text" x-model="contact_no"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Address</label>
                    <input type="text" x-model="address"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    @click="closeCustomerModal()"
                 class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                Cancel
            </button>

            <button type="button"
                    @click="
                        if(!fname.trim() || !lname.trim() || !contact_no.trim()){
                            alert('First Name, Last Name, and Contact No are required!');
                            return;
                        }

                        fetch('{{ route('orders.customer.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN':'{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ fname, mname, lname, contact_no, address })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) { alert('Failed to add customer!'); return; }

                            const c = data.customer;

                            // ADD CUSTOMER TO TABLE IF EXISTS
                            const tbody = document.getElementById('customer-table-body');
                            if(tbody){
                                const row = document.createElement('tr');
                                row.className = 'hover:bg-gray-50';
                                row.innerHTML = `
                                    <td class='px-4 py-3 text-center font-medium text-gray-800'>C${String(c.customer_id).padStart(3,'0')}</td>
                                    <td class='px-4 py-3 text-center text-gray-600'>${c.fname}</td>
                                    <td class='px-4 py-3 text-center text-gray-600'>${c.mname ?? ''}</td>
                                    <td class='px-4 py-3 text-center text-gray-600'>${c.lname}</td>
                                    <td class='px-4 py-3 text-center text-gray-600'>${c.contact_no}</td>
                                    <td class='px-4 py-3 text-center text-gray-600'>${c.address ?? ''}</td>
                                `;
                                tbody.appendChild(row);
                            }

                            // ADD CUSTOMER TO DROPDOWN
                            const select = document.querySelector('select[name=customer_id]');
                            if(select){
                                const option = document.createElement('option');
                                option.value = c.customer_id;
                                option.textContent = `${c.fname} ${c.lname}`;
                                select.appendChild(option);

                                // AUTO-SELECT NEW CUSTOMER
                                select.value = c.customer_id;
                                selectedCustomer = c.customer_id;
                            }

                            // RESET FIELDS
                            fname=''; mname=''; lname=''; contact_no=''; address='';

                            // CLOSE CUSTOMER MODAL
                            closeCustomerModal();

                            // RE-OPEN ORDER MODAL
                            showAddOrder = true;
                        })
                        .catch(err => console.error(err));
                    "
                    class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
function orderData() {
    return {
        // Customer Modal
        showAddCustomerModal: false,
        fname:'', mname:'', lname:'', contact_no:'', address:'',

        // Order Modal
        showAddOrder: false,
        selectedCustomer: '',

        // Stock / Items
        items: [{ stock_id:'', quantity:1, price:0, total:0 }],
        stockList: {
            @foreach($inventories as $stock)
            '{{ $stock->stock_id }}': {
                product_id: '{{ $stock->product_id }}',
                name: '{{ $stock->product->product_name ?? "No Product" }}',
                unit: '{{ $stock->product->unit ?? "" }}',
                price: {{ $stock->unit_cost ?? 0 }},
                current_stock: {{ $stock->current_stock ?? 0 }}
            },
            @endforeach
        },

        // Customer Modal Methods
        openCustomerModal(){ this.showAddCustomerModal = true; },
        closeCustomerModal(){ 
            this.showAddCustomerModal = false; 
            this.fname=''; this.mname=''; this.lname=''; this.contact_no=''; this.address=''; 
        },

        // Order Methods
        updateTotal(item){
            if(item.stock_id && this.stockList[item.stock_id]){
                item.price = this.stockList[item.stock_id].price;
                item.total = (item.quantity * item.price).toFixed(2);
            } else {
                item.price = 0; item.total = 0;
            }
        },
        grandTotal(){
            return this.items.reduce((sum, i) => sum + Number(i.total||0),0).toFixed(2);
        }
    }
}
</script>
