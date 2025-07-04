<!-- resources/js/Pages/Admin/Payroll/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import Pagination from '@/Components/Pagination.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        payrolls: Object,
        employees: Array,
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
    const employeeFilter = ref(props.filters.employee_id || '');

    // Modals
    const showGenerateModal = ref(false);
    const showViewModal = ref(false);
    const showDeleteModal = ref(false);

    // Current payroll for operations
    const currentPayroll = ref(null);

    // Generate payrolls form
    const generateForm = useForm({
        month: new Date().getMonth() + 1,
        year: new Date().getFullYear(),
        employee_ids: [],
    });

    // Watch for URL changes
    watch([search, perPage, statusFilter, monthFilter, yearFilter, employeeFilter], () => {
        router.get(route('admin.payroll.index'), {
            search: search.value,
            per_page: perPage.value,
            status: statusFilter.value,
            month: monthFilter.value,
            year: yearFilter.value,
            employee_id: employeeFilter.value,
        }, {
            preserveState: true,
            replace: true,
        });
    });

    // Modal functions
    const openGenerateModal = () => {
        generateForm.reset();
        generateForm.month = new Date().getMonth() + 1;
        generateForm.year = new Date().getFullYear();
        showGenerateModal.value = true;
    };

    const openViewModal = (payroll) => {
        currentPayroll.value = payroll;
        showViewModal.value = true;
    };

    const openDeleteModal = (payroll) => {
        currentPayroll.value = payroll;
        showDeleteModal.value = true;
    };

    const closeModals = () => {
        showGenerateModal.value = false;
        showViewModal.value = false;
        showDeleteModal.value = false;
        currentPayroll.value = null;
    };

    // Operations
    const generatePayrolls = () => {
        generateForm.post(route('admin.payroll.generate-payrolls'), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const markAsPaid = (payroll) => {
        router.post(route('admin.payroll.mark-as-paid', payroll.id), {}, {
            onSuccess: () => {
                // Success handled by redirect
            }
        });
    };

    const deletePayroll = () => {
        router.delete(route('admin.payroll.destroy', currentPayroll.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const resetFilters = () => {
        search.value = '';
        statusFilter.value = '';
        monthFilter.value = '';
        yearFilter.value = '';
        employeeFilter.value = '';
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

    const toggleEmployeeSelection = (employeeId) => {
        const index = generateForm.employee_ids.indexOf(employeeId);
        if (index > -1) {
            generateForm.employee_ids.splice(index, 1);
        } else {
            generateForm.employee_ids.push(employeeId);
        }
    };

    const selectAllEmployees = () => {
        if (generateForm.employee_ids.length === props.employees.length) {
            generateForm.employee_ids = [];
        } else {
            generateForm.employee_ids = props.employees.map(e => e.id);
        }
    };
</script>

<template>
    <Head title="Payroll Management" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payroll Management</h2>
                <div class="flex gap-3">
                    <button @click="openGenerateModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Payrolls
                    </button>
                    <Link :href="route('admin.payroll.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Payroll
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-3 lg:grid-cols-6">
                    <!-- Total Payrolls -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payrolls</p>
                                    <p class="text-xl font-bold text-gray-900">{{ stats.total_payrolls }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Month -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">This Month</p>
                                    <p class="text-xl font-bold text-gray-900">{{ stats.current_month_payrolls }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                                    <p class="text-xl font-bold text-gray-900">{{ stats.pending_payments }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paid This Month -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Paid This Month</p>
                                    <p class="text-xl font-bold text-gray-900">{{ stats.paid_this_month }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-indigo-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Month Total</p>
                                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.total_amount_current_month) }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Amount -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Amount</p>
                                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.pending_amount) }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
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
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                            <div class="w-full lg:w-1/3">
                                <SearchInput v-model="search" placeholder="Search employees..." />
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select v-model="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <select v-model="monthFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Months</option>
                                        <option v-for="month in months" :key="month.value" :value="month.value">
                                            {{ month.label }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                    <select v-model="yearFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Years</option>
                                        <option v-for="year in years" :key="year" :value="year">
                                            {{ year }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                                    <select v-model="employeeFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Employees</option>
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                            {{ employee.employee_name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                    <select v-model="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option :value="15">15</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                        <option :value="100">100</option>
                                    </select>
                                </div>
                                <div>
                                    <button @click="resetFilters" class="w-full px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                        Reset
                                    </button>
                                </div>
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
                                        Employee
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Basic Salary
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
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                   <span class="text-white text-sm font-semibold">
                                                       {{ payroll.employee.employee_name.charAt(0).toUpperCase() }}
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ payroll.employee.employee_name }}</div>
                                                <div class="text-sm text-gray-500">{{ payroll.employee.department?.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ getMonthName(payroll.month) }} {{ payroll.year }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(payroll.basic_salary) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.net_salary) }}</div>
                                        <div class="text-sm text-gray-500">Gross: {{ formatCurrency(payroll.gross_salary) }}</div>
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
                                            <button @click="openViewModal(payroll)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <Link :href="route('admin.payroll.generate-payslip', payroll.id)" class="text-purple-600 hover:text-purple-900" title="Generate Payslip">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </Link>
                                            <button v-if="payroll.status === 'pending'" @click="markAsPaid(payroll)" class="text-green-600 hover:text-green-900" title="Mark as Paid">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <Link :href="route('admin.payroll.edit', payroll.id)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button @click="openDeleteModal(payroll)" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="payrolls.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No payrolls found. <Link :href="route('admin.payroll.create')" class="text-blue-600 hover:text-blue-900">Create your first payroll</Link>.
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

        <!-- Generate Payrolls Modal -->
        <div v-if="showGenerateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="generatePayrolls">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Generate Payrolls</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="generate_month" value="Month" />
                                        <select
                                                id="generate_month"
                                                v-model="generateForm.month"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option v-for="month in months" :key="month.value" :value="month.value">
                                                {{ month.label }}
                                            </option>
                                        </select>
                                        <InputError :message="generateForm.errors.month" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="generate_year" value="Year" />
                                        <select
                                                id="generate_year"
                                                v-model="generateForm.year"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option v-for="year in [2024, 2025, 2026]" :key="year" :value="year">
                                                {{ year }}
                                            </option>
                                        </select>
                                        <InputError :message="generateForm.errors.year" class="mt-2" />
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <InputLabel value="Select Employees" />
                                        <button type="button" @click="selectAllEmployees" class="text-sm text-blue-600 hover:text-blue-900">
                                            {{ generateForm.employee_ids.length === employees.length ? 'Deselect All' : 'Select All' }}
                                        </button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2">
                                        <div v-for="employee in employees" :key="employee.id" class="flex items-center space-x-2 py-1">
                                            <input
                                                    type="checkbox"
                                                    :id="`employee_${employee.id}`"
                                                    :checked="generateForm.employee_ids.includes(employee.id)"
                                                    @change="toggleEmployeeSelection(employee.id)"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <label :for="`employee_${employee.id}`" class="text-sm text-gray-700">
                                                {{ employee.employee_name }}
                                            </label>
                                        </div>
                                    </div>
                                    <InputError :message="generateForm.errors.employee_ids" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="generateForm.processing || generateForm.employee_ids.length === 0" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="generateForm.processing">Generating...</span>
                                <span v-else>Generate Payrolls</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal && currentPayroll" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Payroll Details</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ currentPayroll.employee.employee_name }}</h4>
                                    <p class="text-gray-600">{{ currentPayroll.employee.department?.name }}</p>
                                    <p class="text-gray-600">{{ getMonthName(currentPayroll.month) }} {{ currentPayroll.year }}</p>
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
                        <Link :href="route('admin.payroll.generate-payslip', currentPayroll.id)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Generate Payslip
                        </Link>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Close</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal && currentPayroll" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Payroll</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete payroll for "{{ currentPayroll.employee.employee_name }}" for {{ getMonthName(currentPayroll.month) }} {{ currentPayroll.year }}? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="deletePayroll" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>