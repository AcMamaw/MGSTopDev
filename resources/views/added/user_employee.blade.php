<div x-show="showUserModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="showUserModal=false" class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Employee User Account</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Username</label>
                <input type="text" x-model="username" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="text" x-model="password" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100">
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="showUserModal=false" class="px-6 py-2 rounded-lg bg-yellow-400 font-semibold hover:bg-yellow-500 transition">OK</button>
        </div>
    </div>
</div>
