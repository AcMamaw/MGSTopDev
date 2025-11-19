<!-- Add Order Modal -->
<div x-show="showAddOrder" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <!-- Modal Container -->
    <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl relative
                overflow-y-auto max-h-[calc(100vh-2rem)] p-8"
         x-data="orderData()">

        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex justify-between items-center">
            <span>Add New Order</span>
            <span class="text-sm font-light text-gray-600">
                Input by: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
            </span>
        </h2>

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf

            <!-- Customer -->
            <div class="flex flex-col md:flex-row items-center gap-2 mb-4">
                <select name="customer_id"
                        x-model="selectedCustomer"
                        required
                        class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">
                            {{ $customer->fname }} {{ $customer->lname }}
                        </option>
                    @endforeach
                </select>
                <button type="button"
                        @click.stop="openCustomerModal()"
                        class="px-4 h-10 text-sm rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                    Add Customer
                </button>
            </div>

            <!-- Include Add Customer Modal -->
            <div x-show="showAddCustomerModal" x-cloak x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto">
                <div @click.away="closeCustomerModal()"
                     class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">
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
                        <button type="button" @click="closeCustomerModal()"
                                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
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

                                        let select = document.querySelector('select[name=customer_id]');
                                        let option = document.createElement('option');
                                        option.value = c.customer_id;
                                        option.textContent = `${c.fname} ${c.lname}`;
                                        select.appendChild(option);
                                        select.value = c.customer_id;
                                        selectedCustomer = c.customer_id;

                                        fname=''; mname=''; lname=''; contact_no=''; address='';
                                        closeCustomerModal();
                                        showAddOrder = true;
                                    })
                                    .catch(err => console.error(err));
                                "
                                class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Date -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Order Date</label>
                <input type="date" name="order_date" required
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
            </div>

            <!-- Order Items Table -->
            <div class="mb-4 overflow-x-auto">
                <label class="block text-gray-700 font-medium mb-2">Order Items</label>
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border w-52">Product</th>
                            <th class="px-4 py-2 border w-32">Available</th>
                            <th class="px-4 py-2 border w-20">Qty</th>
                            <th class="px-4 py-2 border w-32">Unit Price</th>
                            <th class="px-4 py-2 border w-32">Total</th>
                            <th class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-2 py-2 border">
                                    <select :name="'items['+index+'][stock_id]'"
                                            x-model="item.stock_id"
                                            @change="updateTotal(item)"
                                            required
                                            class="w-full px-2 py-1 border rounded">
                                        <option value="">Select</option>
                                        @foreach($inventories as $stock)
                                            <option value="{{ $stock->stock_id }}">
                                                {{ $stock->product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-sm text-gray-500 mt-1"
                                         x-text="item.stock_id ? stockList[item.stock_id]?.name + ' (' + stockList[item.stock_id]?.unit + ')' : ''">
                                    </div>
                                </td>
                                <td class="px-2 py-2 border text-center">
                                    <span x-text="item.stock_id ? stockList[item.stock_id]?.current_stock : '-'"></span>
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'items['+index+'][quantity]'"
                                           x-model.number="item.quantity"
                                           min="1"
                                           @input="updateTotal(item)"
                                           class="w-full px-2 py-1 border rounded text-right">
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'items['+index+'][price]'"
                                           x-model="item.price"
                                           readonly
                                           class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'items['+index+'][total]'"
                                           x-model="item.total"
                                           readonly
                                           class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>
                                <td class="px-2 py-2 border text-center">
                                   <button type="button"
                                           @click="items.splice(index, 1)"
                                           class="w-10 h-10 flex items-center justify-center ml-3 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                           <line x1="3" y1="6" x2="27" y2="6" />
                                           <rect x="6" y="6" width="18" height="18" rx="2" ry="2" />
                                           <line x1="10" y1="10" x2="10" y2="22" />
                                           <line x1="15" y1="10" x2="15" y2="22" />
                                           <line x1="20" y1="10" x2="20" y2="22" />
                                       </svg>
                                   </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <button type="button"
                        @click="items.push({stock_id:'', quantity:1, price:0, total:0})"
                        class="mt-2 bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Add Item
                </button>
            </div>

            <!-- Grand Total -->
            <div class="text-right text-xl font-bold text-gray-800 mb-4">
                Grand Total: ₱ <span x-text="grandTotal()"></span>
                <input type="hidden" name="total_amount" :value="grandTotal()">
            </div>

            <!-- Buttons -->
            <div class="flex flex-wrap justify-end gap-2 mb-2">
                <button type="button" @click="showAddOrder = false"
                        class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Save Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function orderData() {
    return {
        selectedCustomer: '',
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
        updateTotal(item) {
            if(item.stock_id && this.stockList[item.stock_id]) {
                item.price = this.stockList[item.stock_id].price;
                item.total = (item.quantity * item.price).toFixed(2);
            } else {
                item.price = 0;
                item.total = 0;
            }
        },
        grandTotal() {
            return this.items.reduce((sum, i) => sum + Number(i.total || 0), 0).toFixed(2);
        },
        showAddCustomerModal: false,
        fname: '', mname: '', lname: '', contact_no: '', address: '',
        openCustomerModal() { this.showAddCustomerModal = true; },
        closeCustomerModal() {
            this.showAddCustomerModal = false;
            this.fname=''; this.mname=''; this.lname=''; this.contact_no=''; this.address='';
        }
    }
}
</script>
