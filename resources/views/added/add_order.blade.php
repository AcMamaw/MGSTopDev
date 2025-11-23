<!-- Combined Add Order + Payment Modal -->
<div x-show="showAddOrder" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl relative overflow-y-auto max-h-[calc(100vh-2rem)] p-6 flex gap-6"
         x-data="orderData()">

        <!-- Left: Add Order (65%) -->
        <div class="[flex-basis:65%] border-r pr-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Order</h2>

            <!-- Customer Selection -->
            <div class="flex flex-col md:flex-row items-center gap-2 mb-4">
                <select name="customer_id" x-model="selectedCustomer" required
                        class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">
                            {{ $customer->fname }} {{ $customer->lname }}
                        </option>
                    @endforeach
                </select>
                <button type="button" @click.stop="openCustomerModal()"
                        class="px-4 h-10 text-sm rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                    Add Customer
                </button>
            </div>

            <!-- Add Customer Modal -->
            <div x-show="showAddCustomerModal" x-cloak x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div @click.away="closeCustomerModal()"
                     class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Customer</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <button type="button" @click="closeCustomerModal()"
                                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
                        <button type="button" @click="addCustomer()"
                                class="px-6 py-2 rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="mb-4 overflow-x-auto flex-1">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 border w-2/5">Product</th>       
                        <th class="px-4 py-2 border w-1/12">Available</th>    
                        <th class="px-4 py-2 border w-1/6">Qty</th>          
                        <th class="px-4 py-2 border w-1/6">Unit Price</th>    
                        <th class="px-4 py-2 border w-1/6">Total</th>        
                        <th class="px-4 py-2 border w-1/12">Action</th>      
                    </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-2 py-2 border">
                                    <select :name="'items['+index+'][stock_id]'" x-model="item.stock_id"
                                            @change="updateTotal(item)" required class="w-full px-2 py-1 border rounded">
                                        <option value="">Select</option>
                                        @foreach($inventories as $stock)
                                            <option value="{{ $stock->stock_id }}">
                                                {{ $stock->product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-2 py-2 border text-center">
                                    <span x-text="item.stock_id ? stockList[item.stock_id]?.current_stock : '-'"></span>
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number" x-model.number="item.quantity" min="1"
                                           @input="updateTotal(item)" class="w-full px-2 py-1 border rounded text-right">
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number" x-model="item.price" readonly
                                           class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>
                                <td class="px-2 py-2 border">
                                    <input type="number" x-model="item.total" readonly
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
                <button type="button" @click="items.push({stock_id:'', quantity:1, price:0, total:0})"
                        class="mt-2 bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Add Item
                </button>
            </div>

            <!-- Footer: Issued By + Grand Total -->
            <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">
                
                <!-- Issued By on the left -->
                <div class="text-lg font-semibold">
                   Issued by: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
                </div>

                <!-- Grand Total on the right -->
                <div class="text-xl font-bold">
                    Grand Total: ₱ <span x-text="grandTotal()"></span>
                </div>
            </div>
        </div>

        <!-- Right: Payment (35%) -->
        <div x-data="paymentSection()" class="[flex-basis:35%] pl-4">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Payment</h2>
            <form @submit.prevent="submitOrder()">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Payment Date</label>
                    <input type="date" x-model="paymentData.payment_date"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="number" :value="paymentAmount" readonly
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Cash</label>
                    <input type="number" x-model.number="paymentCash" step="0.01" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                </div>

                <!-- Dynamic Warning / Change Message -->
                <div x-show="paymentCash > 0" 
                    :class="paymentCash < paymentAmount ? 'bg-yellow-50 border border-yellow-300 text-yellow-800' : 'bg-green-50 border border-green-300 text-green-800'"
                    class="rounded-lg p-3 mb-4">
                    <template x-if="paymentCash < paymentAmount">
                        <p>⚠️ Partial Payment. Remaining: ₱<span x-text="(paymentAmount - paymentCash).toFixed(2)"></span></p>
                    </template>
                    <template x-if="paymentCash >= paymentAmount">
                        <p>✅ Fully Paid. Change: ₱<span x-text="(paymentCash - paymentAmount).toFixed(2)"></span></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Payment Method</label>
                    <select x-model="paymentMethod"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                    </select>
                </div>

                <!-- Reference Number only visible if GCash -->
                <div class="mb-4" x-show="paymentMethod === 'GCash'">
                    <label class="block text-gray-700 font-medium mb-1">Reference Number</label>
                    <input type="text" x-model="paymentReference"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showAddOrder=false"
                            class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                        Confirm Payment
                    </button>
                </div>
            </form>
        </div>
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

        // Payment fields
        paymentData: {
            payment_date: '{{ date("Y-m-d") }}',
        },
        paymentCash: 0,
        paymentMethod: '',
        paymentReference: '',

        // Customer modal
        showAddCustomerModal: false,
        fname:'', mname:'', lname:'', contact_no:'', address:'',
        openCustomerModal() { this.showAddCustomerModal = true; },
        closeCustomerModal() { 
            this.showAddCustomerModal = false; 
            this.fname=''; this.mname=''; this.lname=''; this.contact_no=''; this.address=''; 
        },
        addCustomer() {
            if(!this.fname.trim() || !this.lname.trim() || !this.contact_no.trim()) { 
                alert('First Name, Last Name, and Contact No are required!'); 
                return; 
            }
            fetch('{{ route('orders.customer.store') }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({ fname:this.fname, mname:this.mname, lname:this.lname, contact_no:this.contact_no, address:this.address })
            })
            .then(res=>res.json())
            .then(data=>{
                if(!data.success){ alert('Failed to add customer!'); return; }
                const c = data.customer;
                let select = document.querySelector('select[name=customer_id]');
                let option = document.createElement('option');
                option.value = c.customer_id;
                option.textContent = `${c.fname} ${c.lname}`;
                select.appendChild(option);
                select.value = c.customer_id;
                this.selectedCustomer = c.customer_id;
                this.closeCustomerModal();
            })
            .catch(err=>console.error(err));
        },

        // Update total for each item
        updateTotal(item) {
            if(item.stock_id && this.stockList[item.stock_id]) {
                item.price = this.stockList[item.stock_id].price;
                item.total = (item.quantity * item.price).toFixed(2);
            } else {
                item.price = 0;
                item.total = 0;
            }
        },

        // Calculate grand total
        grandTotal() {
            return this.items.reduce((sum, i) => sum + Number(i.total || 0), 0).toFixed(2);
        },

        // Computed payment amount
        get paymentAmount() {
            return Number(this.grandTotal());
        },

        // Computed change
        get paymentChange() {
            return Math.max(this.paymentCash - this.paymentAmount, 0).toFixed(2);
        },

        // Computed balance
        get paymentBalance() {
            return Math.max(this.paymentAmount - this.paymentCash, 0).toFixed(2);
        },

        // Computed status
        get paymentStatus() {
            return this.paymentCash >= this.paymentAmount ? 'Fully Paid' : 'Partial';
        },

        // Submit Order + Payment
        submitOrder() {
            if(!this.selectedCustomer) {
                alert('Please select a customer.');
                return;
            }
            if(this.items.length === 0) {
                alert('Please add at least one item.');
                return;
            }

            const payload = {
                customer_id: this.selectedCustomer,
                order_date: this.paymentData.payment_date,
                payment_date: this.paymentData.payment_date,
                items: this.items.map(i => ({
                    stock_id: i.stock_id,
                    quantity: i.quantity,
                    price: i.price
                })),
                total_amount: this.paymentAmount,
                cash: this.paymentCash,
                change_amount: this.paymentChange,
                balance: this.paymentBalance,
                status: this.paymentStatus,
                payment_method: this.paymentMethod,
                reference_number: this.paymentReference,
                employee_id: {{ auth()->user()->employee->employee_id }}
            };

            fetch('{{ route("orders.store") }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) { 
                    alert(data.message || 'Failed to create order and payment'); 
                    return; 
                }

                alert('Order and Payment successfully created!');
                window.location.href = '{{ route("purchaseorder") }}';
            })
            .catch(err => console.error(err));
        }
    }
}
</script>
