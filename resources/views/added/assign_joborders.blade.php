<div x-show="showAssignJobOrderModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

    <div @click.outside="showAssignJobOrderModal = false"
         class="bg-white w-full max-w-6xl rounded-xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Assign Job Order</h2>
                <p class="text-sm text-gray-500 mt-1">Select Layout Artists to assign to this order</p>
            </div>
        </div>

        <form @submit.prevent="submitAssignJobOrder()" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" :value="selectedOrderId">

            <!-- Search Bar -->
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="search"
                           x-model="employeeSearch"
                           placeholder="Search by name, email, or contact..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" 
                           id="selectAllEmployees" 
                           x-model="selectAllEmployees" 
                           @change="toggleSelectAll()"
                           class="w-4 h-4 cursor-pointer">
                    <label for="selectAllEmployees" class="font-medium text-gray-800 cursor-pointer whitespace-nowrap">
                        Select All
                    </label>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Select</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Employee ID</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">First Name</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Last Name</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Contact No</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Loading State -->
                        <template x-if="employees.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-500">Loading employees...</p>
                                </td>
                            </tr>
                        </template>

                        <!-- Employee Rows -->
                        <template x-for="employee in employees.filter(emp => {
                            if (!employeeSearch) return true;
                            const search = employeeSearch.toLowerCase();
                            const fullName = (emp.fname + ' ' + emp.lname).toLowerCase();
                            const email = (emp.email || '').toLowerCase();
                            const contact = (emp.contact_no || '').toLowerCase();
                            return fullName.includes(search) || email.includes(search) || contact.includes(search);
                        })" :key="employee.employee_id">
                            <tr class="hover:bg-gray-50">
                                <!-- Checkbox -->
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" 
                                           :value="employee.employee_id" 
                                           x-model="selectedEmployees"
                                           :id="'emp-' + employee.employee_id"
                                           class="w-4 h-4 cursor-pointer text-yellow-600 focus:ring-2 focus:ring-yellow-500">
                                </td>
                                
                                <!-- Employee ID -->
                                <td class="px-4 py-3 text-center text-gray-500 font-medium">
                                    <span x-text="'EMP' + String(employee.employee_id).padStart(3, '0')"></span>
                                </td>
                                
                                <!-- First Name -->
                                <td class="px-4 py-3 text-center text-gray-500" x-text="employee.fname"></td>
                                
                                <!-- Last Name -->
                                <td class="px-4 py-3 text-center text-gray-500" x-text="employee.lname"></td>
                                
                                <!-- Email -->
                                <td class="px-4 py-3 text-center text-gray-500" x-text="employee.email || 'N/A'"></td>
                                
                                <!-- Contact No -->
                                <td class="px-4 py-3 text-center text-gray-500" x-text="employee.contact_no || 'N/A'"></td>
                                
                                <!-- Status -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full" 
                                              :class="employee.status === 'Active' ? 'bg-green-500' : 'bg-red-500'"></span>
                                        <span class="text-gray-800 text-xs font-semibold" x-text="employee.status"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- No Results -->
                        <template x-if="employees.length > 0 && employees.filter(emp => {
                            if (!employeeSearch) return true;
                            const search = employeeSearch.toLowerCase();
                            const fullName = (emp.fname + ' ' + emp.lname).toLowerCase();
                            const email = (emp.email || '').toLowerCase();
                            const contact = (emp.contact_no || '').toLowerCase();
                            return fullName.includes(search) || email.includes(search) || contact.includes(search);
                        }).length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No employees found matching "<span x-text="employeeSearch"></span>"
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Selected Count -->
            <div class="flex items-center justify-between pt-2">
                <div class="text-sm text-gray-600" x-show="selectedEmployees.length > 0">
                    <span class="font-semibold" x-text="selectedEmployees.length"></span> employee(s) selected
                </div>
                <div class="text-sm text-gray-500">
                    Total: <span x-text="employees.length"></span> employees
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" @click="showAssignJobOrderModal = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        :disabled="selectedEmployees.length === 0"
                    class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Assign <span x-show="selectedEmployees.length > 0" x-text="'(' + selectedEmployees.length + ')'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
