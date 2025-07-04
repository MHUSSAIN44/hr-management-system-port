<!-- resources/js/Pages/Employee/Payroll/Show.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

    defineProps({
        payroll: Object,
    });

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
    <Head :title="`Payroll Details - ${getMonthName(payroll.month)} ${payroll.year}`" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payroll Details</h2>
                <div class="flex space-x-3">
                    <Link :href="route('employee.payroll.payslip', payroll.id)" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Payslip
                    </Link>
                    <Link :href="route('employee.payroll.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Payroll
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Payroll Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center justify-between">
                            <!-- Employee Info -->
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="mr-6">
                                    <div class="h-20 w-20 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center">
                                       <span class="text-blue-600 text-xl font-semibold">
                                           {{ payroll?.employee?.employee_name?.split(' ').map(n => n[0]).join('') || 'N/A' }}
                                       </span>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ payroll?.employee?.employee_name }}</h2>
                                    <p class="text-lg text-gray-600">{{ payroll?.employee?.designation?.name }}</p>
                                    <div class="mt-1 flex items-center flex-wrap">
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Department:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ payroll?.employee?.department?.name }}</span>
                                        </div>
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Location:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ payroll?.employee?.location?.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payroll Status -->
                            <div class="text-right">
                                <div class="mb-2">
                                   <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(payroll?.status)">
                                       {{ payroll?.status }}
                                   </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ getMonthName(payroll?.month) }} {{ payroll?.year }}
                                </div>
                                <div v-if="payroll?.payment_date" class="text-xs text-gray-500 mt-1">
                                    Paid on {{ formatDate(payroll.payment_date) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Earnings -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Earnings
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Basic Salary</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.basic_salary || 0) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Accommodation Allowance</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.accommodation || 0) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Other Allowances</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.allowances || 0) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Overtime Amount</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.overtime_amount || 0) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-base font-semibold text-gray-800">Total Earnings</dt>
                                    <dd class="text-base font-semibold text-green-600">{{ formatCurrency(payroll?.gross_salary || 0) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-red-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-red-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                Deductions
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Deductions</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.deductions || 0) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-base font-semibold text-gray-800">Net Deductions</dt>
                                    <dd class="text-base font-semibold text-red-600">{{ formatCurrency(payroll?.deductions || 0) }}</dd>
                                </div>

                                <!-- Additional info about deductions -->
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500">
                                        <strong>Note:</strong> Deductions may include taxes, insurance premiums, and other statutory requirements.
                                    </p>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-blue-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Net Pay
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Gross Salary</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll?.gross_salary || 0) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Deductions</dt>
                                    <dd class="text-sm font-medium text-red-600">-{{ formatCurrency(payroll?.deductions || 0) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-xl font-bold text-gray-800">Net Salary</dt>
                                    <dd class="text-xl font-bold text-blue-600">{{ formatCurrency(payroll?.net_salary || 0) }}</dd>
                                </div>

                                <!-- Payment Status -->
                                <div class="mt-4 p-4 rounded-lg" :class="payroll?.status === 'paid' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'">
                                    <div class="flex items-center">
                                        <svg v-if="payroll?.status === 'paid'" class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg v-else class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium" :class="payroll?.status === 'paid' ? 'text-green-800' : 'text-yellow-800'">
                                                {{ payroll?.status === 'paid' ? 'Payment Completed' : 'Payment Pending' }}
                                            </p>
                                            <p v-if="payroll?.payment_date" class="text-xs" :class="payroll?.status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
                                                {{ payroll?.status === 'paid' ? 'Paid on' : 'Expected on' }} {{ formatDate(payroll.payment_date) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Payroll Summary Card -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payroll Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Pay Period</p>
                            <p class="text-lg font-semibold text-gray-900">{{ getMonthName(payroll?.month) }} {{ payroll?.year }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Total Earnings</p>
                            <p class="text-lg font-semibold text-green-600">{{ formatCurrency(payroll?.gross_salary || 0) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Deductions</p>
                            <p class="text-lg font-semibold text-red-600">{{ formatCurrency(payroll?.deductions || 0) }}</p>
                        </div>
                        <div class="text-center border-l border-blue-300">
                            <p class="text-sm text-gray-600">Take-Home Pay</p>
                            <p class="text-xl font-bold text-blue-600">{{ formatCurrency(payroll?.net_salary || 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>