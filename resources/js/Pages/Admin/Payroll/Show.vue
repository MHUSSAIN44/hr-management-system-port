<!-- resources/js/Pages/Admin/Payroll/Show.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';

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
    <Head :title="`Payroll - ${payroll.employee.employee_name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payroll Details</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.payroll.generate-payslip', payroll.id)" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Payslip
                    </Link>
                    <Link :href="route('admin.payroll.edit', payroll.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </Link>
                    <Link :href="route('admin.payroll.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
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
                                    <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-500 text-xl font-semibold">
                                            {{ payroll.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ payroll.employee.employee_name }}</h2>
                                    <p class="text-lg text-gray-600">{{ payroll.employee.designation?.name }}</p>
                                    <div class="mt-1 flex items-center flex-wrap">
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Department:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ payroll.employee.department?.name }}</span>
                                        </div>
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Location:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ payroll.employee.location?.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payroll Status -->
                            <div class="text-right">
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(payroll.status)">
                                        {{ payroll.status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ getMonthName(payroll.month) }} {{ payroll.year }}
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
                            <h3 class="text-lg font-medium text-green-800">Earnings</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Basic Salary</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.basic_salary) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Accommodation</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.accommodation) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Other Allowances</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.allowances) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Overtime</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.overtime_amount) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-base font-semibold text-gray-800">Total Earnings</dt>
                                    <dd class="text-base font-semibold text-green-600">{{ formatCurrency(payroll.gross_salary) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-red-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-red-800">Deductions</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Deductions</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.deductions) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-base font-semibold text-gray-800">Net Deductions</dt>
                                    <dd class="text-base font-semibold text-red-600">{{ formatCurrency(payroll.deductions) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-blue-800">Net Pay</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Gross Salary</dt>
                                    <dd class="text-sm text-gray-900">{{ formatCurrency(payroll.gross_salary) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Deductions</dt>
                                    <dd class="text-sm text-red-600">-{{ formatCurrency(payroll.deductions) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-4">
                                    <dt class="text-xl font-bold text-gray-800">Net Salary</dt>
                                    <dd class="text-xl font-bold text-blue-600">{{ formatCurrency(payroll.net_salary) }}</dd>
                                </div>
                                <div v-if="payroll.payment_date" class="text-center mt-4">
                                    <p class="text-sm text-gray-500">Paid on</p>
                                    <p class="text-sm font-medium text-gray-900">{{ formatDate(payroll.payment_date) }}</p>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>