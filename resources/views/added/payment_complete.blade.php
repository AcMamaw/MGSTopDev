<!-- RECEIPT MODAL -->
<div x-show="showReceipt" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div id="printableReceipt"
         @click.away="showReceipt = false"
         class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative text-gray-800 max-h-[90vh] overflow-y-auto">

       <!-- Download PDF Button -->
        <a :href="receipt.pdf_url || '#'"
        :target="receipt.pdf_url ? '_blank' : '_self'"
        x-show="receipt.pdf_url"
        class="no-print absolute top-4 right-4 p-3 rounded-full bg-yellow-400 text-black shadow-lg hover:bg-yellow-500 transition"
        title="Download PDF Receipt">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
        </a>

        <!-- Upload Success -->
        <div x-show="uploadSuccess"
             class="no-print mb-3 px-3 py-2 bg-green-50 border border-green-300 rounded text-sm text-green-800">

            Receipt saved!
            <a :href="uploadedUrl" target="_blank" class="underline font-semibold">View PDF</a>
        </div>

        <!-- Upload Error -->
        <div x-show="uploadError"
             class="no-print mb-3 px-3 py-2 bg-red-50 border border-red-300 rounded text-sm text-red-800"
             x-text="uploadError">
        </div>

        <!-- HEADER -->
        <div class="flex justify-between mb-4">

            <div>
                <h1 class="text-lg font-bold">Mariviles Graphic Studio</h1>
                <p class="text-xs">Adopted CO.</p>
                <p class="text-xs">Mati City</p>
            </div>

            <div>
                <img src="{{ asset('images/ace.jpg') }}"
                     class="h-16 object-contain">
            </div>

        </div>

        <!-- RECEIPT INFO -->
        <div class="flex justify-between text-xs mb-3">

            <div>
                Receipt #:
                <span
                    x-text="receipt?.receipt_number ? ('R-' + String(receipt.receipt_number).padStart(5,'0')) : 'N/A'">
                </span>
            </div>

            <div class="text-right">
                <p class="font-semibold text-yellow-500 tracking-widest">RECEIPT</p>
                <p>
                    Date:
                    <span x-text="receipt?.payment_date || 'N/A'"></span>
                </p>
            </div>

        </div>

        <!-- CUSTOMER -->
        <div class="flex justify-between text-xs mb-4">

            <div>
                <p class="text-gray-500 text-[10px] uppercase">Billed To</p>
                <p class="font-semibold"
                   x-text="receipt?.customer_name || 'N/A'"></p>
            </div>

            <div class="text-right">
                <p>
                    Status:
                    <span x-text="receipt?.status || 'N/A'"></span>
                </p>

                <p>
                    Payment:
                    <span x-text="receipt?.payment_method || paymentMethod"></span>
                </p>

                <template x-if="(receipt?.payment_method || paymentMethod) === 'GCash'">
                    <p>
                        Ref:
                        <span x-text="receipt?.reference_number || paymentReference"></span>
                    </p>
                </template>

            </div>

        </div>

        <!-- TABLE HEADER -->
        <div class="grid grid-cols-12 text-xs font-semibold bg-yellow-400 py-2 px-3">
            <div class="col-span-1">Qty</div>
            <div class="col-span-5">Item</div>
            <div class="col-span-2 text-right">Unit</div>
            <div class="col-span-2 text-right">Customize</div>
            <div class="col-span-2 text-right">Total</div>
        </div>

        <!-- ITEMS -->
        <template x-for="(item,index) in (receipt?.items || [])" :key="index">

            <div class="grid grid-cols-12 text-xs py-2 px-3 border-b">

                <div class="col-span-1 font-bold"
                     x-text="item.quantity || item.qty || 1"></div>

                <div class="col-span-5">

                    <p class="font-semibold"
                       x-text="item.product_name || item.description || 'N/A'"></p>

                    <p class="text-[10px] text-gray-600"
                       x-show="item.size || item.color">

                        <span x-show="item.size">
                            Size: <span x-text="item.size"></span>
                        </span>

                        <span x-show="item.color">
                            | Color:
                            <span x-text="colorNameFromHex(item.color)"></span>
                        </span>

                    </p>

                </div>

                <div class="col-span-2 text-right">
                    ₱<span x-text="Number(item.unit_price || 0).toFixed(2)"></span>
                </div>

                <div class="col-span-2 text-right">
                    ₱<span x-text="Number(item.custom_amount || 0).toFixed(2)"></span>
                </div>

                <div class="col-span-2 text-right font-semibold">
                    ₱<span x-text="Number(item.amount || 0).toFixed(2)"></span>
                </div>

            </div>

        </template>

        <!-- TOTALS -->
        <div class="flex justify-end mt-4">

            <div class="w-56 text-xs space-y-1">

                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>
                        ₱<span x-text="Number(receipt?.amount || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between font-semibold border-t pt-1">
                    <span>Total</span>
                    <span>
                        ₱<span x-text="Number(receipt?.amount || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Balance</span>
                    <span>
                        ₱<span x-text="Number(receipt?.balance || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Cash</span>
                    <span>
                        ₱<span x-text="Number(receipt?.cash || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Change</span>
                    <span>
                        ₱<span x-text="Number(receipt?.change_amount || 0).toFixed(2)"></span>
                    </span>
                </div>

            </div>

        </div>

        <!-- NOTES -->
        <div class="mt-4 text-xs">
            <p class="font-semibold">Notes</p>
            <p>
                Thank you for choosing Mariviles Graphic Studio.
            </p>
        </div>

        <!-- SIGNATURE -->
        <div class="mt-6 flex justify-end text-xs">

            <div class="text-right">

                <p class="border-b px-6 pb-1 font-semibold">

                    {{ auth()->user()->employee->fname ?? '' }}
                    {{ auth()->user()->employee->lname ?? '' }}

                </p>

                <p class="text-gray-600 mt-1">Authorized by</p>

            </div>

        </div>

        <!-- CLOSE BUTTON -->
        <div class="mt-6 flex justify-center no-print">

            <button
                @click="showReceipt=false;showSuccess=true;window.location.reload()"
                class="px-6 py-2 bg-yellow-400 rounded font-semibold hover:bg-yellow-500">

                Close

            </button>

        </div>

    </div>
</div>