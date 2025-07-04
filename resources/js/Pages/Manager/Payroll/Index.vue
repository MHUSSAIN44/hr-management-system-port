<!-- resources/js/Pages/Manager/Payroll/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        payrolls: Object,
        employee: Object,
        years: Array,
        months: Array,
        stats: Object,
        filters: Object,
    });

    // Search and filters
    const search = ref(props.filters.search || '');
    const perPage = ref(props.filters.per_page || 15);
    const statusFilter = ref(props.filters.status || '');
    const monthFilter = ref(props.filters.month || '');
    const yearFilter = ref(props.filters.year || '');

    // Modals
    const showViewModal = ref(false);
    const currentPayroll = ref(null);

    // Watch for URL changes
    watch([search, perPage, statusFilter, monthFilter, yearFilter], () => {
        router.get(route('manager.payroll.index'), {
            search: search.value,
            per_page: perPage.value,
            status: statusFilter.value,
            month: monthFilter.value,
            year: yearFilter.value,
        }, {
            preserveState: true,
            replace: true,
        });
    });

    // Modal functions
    const openViewModal = (payroll) => {
        currentPayroll.value = payroll;
        showViewModal.value = true;
    };

    const closeModals = () => {
        showViewModal.value = false;
        currentPayroll.value = null;
    };

    const resetFilters = () => {
        search.value = '';
        statusFilter.value = '';
        monthFilter.value = '';
        yearFilter.value = '';
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'AED',
            minimumFractionDigits: 2
        }).format(amount);
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'paid':
                return 'bg-green-100 text-green-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getMonthName = (month) => {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return monthNames[month - 1] || month;
    };
</script>

<template>
    <Head title="My Payroll" />

    <ManagerLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Payroll</h2>
                <div class="text-sm text-gray-600">
                    Manager: {{ employee?.employee_name }}
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Payrolls -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payrolls</p>
                                    <p class="text-xl font-bold text-gray-900">{{ stats.total_payrolls }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Month Salary -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">This Month</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ stats.current_month_payroll ? formatCurrency(stats.current_month_payroll.net_salary) : 'N/A' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- YTD Earnings -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">YTD Earnings</p>
                                    <p class="text-xl font-bold text-gray-900">{{ formatCurrency(stats.ytd_earnings) }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Monthly -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-indigo-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Monthly</p>
                                    <p class="text-xl font-bold text-gray-900">{{ formatCurrency(stats.average_monthly_salary) }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="w-full lg:w-1/3">
                                <SearchInput v-model="search" placeholder="Search payrolls..." />
                            </div>
                            <div class="flex flex-wrap gap-4 items-center">
                                <FilterDropdown v-model="statusFilter" label="Status" :options="[
                                    { value: '', label: 'All Status' },
                                    { value: 'pending', label: 'Pending' },
                                    { value: 'paid', label: 'Paid' },
                                    { value: 'cancelled', label: 'Cancelled' }
                                ]" />
                                <FilterDropdown v-model="monthFilter" label="Month" :options="[
                                    { value: '', label: 'All Months' },
                                    ...months
                                ]" />
                                <FilterDropdown v-model="yearFilter" label="Year" :options="[
                                    { value: '', label: 'All Years' },
                                    ...years.map(y => ({ value: y, label: y }))
                                ]" />
                                <FilterDropdown v-model="perPage" label="Per Page" :options="[15, 25, 50, 100]" />
                                <button @click="resetFilters" class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payrolls Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Basic Salary
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gross Salary
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Net Salary
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="payroll in payrolls.data" :key="payroll.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ getMonthName(payroll.month) }} {{ payroll.year }}</div>
                                        <div class="text-sm text-gray-500">Pay Period</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(payroll.basic_salary) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(payroll.gross_salary) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.net_salary) }}</div>
                                        <div class="text-sm text-gray-500">Take-home pay</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(payroll.status)">
                                           {{ payroll.status }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(payroll.payment_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button @click="openViewModal(payroll)" class="text-green-600 hover:text-green-900" title="Quick View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <Link :href="route('manager.payroll.show', payroll.id)" class="text-blue-600 hover:text-blue-900" title="Full Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </Link>
                                            <Link :href="route('manager.payroll.payslip', payroll.id)" class="text-purple-600 hover:text-purple-900" title="Download Payslip">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="payrolls.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No payroll records found.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="payrolls.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick View Modal -->
        <div v-if="showViewModal && currentPayroll" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Manager Payroll Summary</h3>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ getMonthName(currentPayroll.month) }} {{ currentPayroll.year }}</h4>
                                    <p class="text-gray-600">Manager Pay Period</p>
                                </div>
                                <div class="text-right">
                                   <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(currentPayroll.status)">
                                       {{ currentPayroll.status }}
                                   </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Basic Salary:</span>
                                        <span class="text-gray-900">{{ formatCurrency(currentPayroll.basic_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Accommodation:</span>
                                        <span class="text-gray-900">{{ formatCurrency(currentPayroll.accommodation) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Allowances:</span>
                                        <span class="text-gray-900">{{ formatCurrency(currentPayroll.allowances) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Overtime:</span>
                                        <span class="text-gray-900">{{ formatCurrency(currentPayroll.overtime_amount) }}</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Deductions:</span>
                                        <span class="text-gray-900">{{ formatCurrency(currentPayroll.deductions) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="font-medium text-gray-500">Gross Salary:</span>
                                        <span class="text-gray-900 font-semibold">{{ formatCurrency(currentPayroll.gross_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-500">Net Salary:</span>
                                        <span class="text-gray-900 font-bold text-lg">{{ formatCurrency(currentPayroll.net_salary) }}</span>
                                    </div>
                                    <div v-if="currentPayroll.payment_date" class="flex justify-between">
                                        <span class="font-medium text-gray-500">Payment Date:</span>
                                        <span class="text-gray-900">{{ formatDate(currentPayroll.payment_date) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <Link :href="route('manager.payroll.payslip', currentPayroll.id)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            View Payslip
                        </Link>
                        <Link :href="route('manager.payroll.show', currentPayroll.id)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Full Details
                        </Link>
                        <button @click="closeModals" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>