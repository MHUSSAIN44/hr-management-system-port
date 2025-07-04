<script setup>
    import { ref } from 'vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        employees: Array,
    });

    // Modal states
    const showUpdateModal = ref(false);
    const currentEmployee = ref(null);

    // Update balance form
    const updateForm = useForm({
        annual_leave_balance: '',
        medical_leave_balance: '',
        reason: ''
    });

    const openUpdateModal = (employee) => {
        currentEmployee.value = employee;
        updateForm.annual_leave_balance = employee.annual_leave_balance || 0;
        updateForm.medical_leave_balance = employee.medical_leave_balance || 0;
        updateForm.reason = '';
        showUpdateModal.value = true;
    };

    const closeModal = () => {
        showUpdateModal.value = false;
        currentEmployee.value = null;
        updateForm.reset();
    };

    const updateBalance = () => {
        updateForm.post(route('admin.employees.update-leave-balance', currentEmployee.value.id), {
            onSuccess: () => {
                closeModal();
            }
        });
    };

    const formatDate = (dateString) => {
        return dateString ? new Date(dateString).toLocaleDateString() : 'N/A';
    };

    const getBalanceColor = (balance, type) => {
        if (type === 'annual') {
            if (balance < 5) return 'text-red-600';
            if (balance < 10) return 'text-yellow-600';
            return 'text-green-600';
        } else {
            if (balance < 3) return 'text-red-600';
            if (balance < 7) return 'text-yellow-600';
            return 'text-green-600';
        }
    };
</script>

<template>
    <Head title="Leave Report" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employee Leave Report</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Employees</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ employees.length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div> </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Can Apply Annual</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ employees.filter(e => e.can_apply_annual).length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Low Annual Balance</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ employees.filter(e => e.annual_leave_balance < 5).length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Low Medical Balance</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ employees.filter(e => e.medical_leave_balance < 3).length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Leave Balances Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Employee Leave Balances</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage and update employee leave balances. Annual leave accrues at 2.5 days per month from visa start date.</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visa Start Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Leave</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medical Leave</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                   <span class="text-white text-sm font-semibold">
                                                       {{ employee.name.split(' ').map(n => n[0]).join('') }}
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ employee.name }}</div>
                                                <div class="text-sm text-gray-500">{{ employee.designation }} - {{ employee.department }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(employee.visa_start_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center space-x-2">
                                               <span :class="getBalanceColor(employee.annual_leave_balance, 'annual')" class="font-medium">
                                                   {{ employee.annual_leave_balance }} available
                                               </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Accrued: {{ employee.annual_leave_accrued }} | Used: {{ employee.annual_leave_used }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center space-x-2">
                                               <span :class="getBalanceColor(employee.medical_leave_balance, 'medical')" class="font-medium">
                                                   {{ employee.medical_leave_balance }} available
                                               </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Used: {{ employee.medical_leave_used }}/14
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                           <span v-if="employee.can_apply_annual" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                               Can Apply Annual
                                           </span>
                                            <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                               Annual Not Eligible
                                           </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openUpdateModal(employee)" class="text-blue-600 hover:text-blue-900" title="Update Balance">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="employees.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No employees found.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Balance Modal -->
        <div v-if="showUpdateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="updateBalance">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Leave Balance</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Update leave balance for <strong>{{ currentEmployee?.name }}</strong>
                            </p>

                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="annual_leave_balance" value="Annual Leave Balance" />
                                    <TextInput
                                            id="annual_leave_balance"
                                            v-model="updateForm.annual_leave_balance"
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            max="50"
                                            class="mt-1 block w-full"
                                            required
                                    />
                                    <InputError :message="updateForm.errors.annual_leave_balance" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">Current accrued: {{ currentEmployee?.annual_leave_accrued }} days</p>
                                </div>

                                <div>
                                    <InputLabel for="medical_leave_balance" value="Medical Leave Balance" />
                                    <TextInput
                                            id="medical_leave_balance"
                                            v-model="updateForm.medical_leave_balance"
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            max="14"
                                            class="mt-1 block w-full"
                                            required
                                    />
                                    <InputError :message="updateForm.errors.medical_leave_balance" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">Maximum: 14 days per year</p>
                                </div>

                                <div>
                                    <InputLabel for="reason" value="Reason for Update" />
                                    <textarea
                                            id="reason"
                                            v-model="updateForm.reason"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Explain why you're updating this employee's leave balance..."
                                            required
                                    ></textarea>
                                    <InputError :message="updateForm.errors.reason" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="updateForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="updateForm.processing">Updating...</span>
                                <span v-else>Update Balance</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModal" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>