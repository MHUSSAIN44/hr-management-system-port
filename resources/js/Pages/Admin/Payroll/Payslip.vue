<!-- resources/js/Pages/Admin/Payroll/Payslip.vue -->
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

    const getMonthName = (month) => {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return monthNames[month - 1] || month;
    };

    const printPayslip = () => {
        window.print();
    };
</script>

<template>
    <Head :title="`Payslip - ${payroll.employee.employee_name} - ${getMonthName(payroll.month)} ${payroll.year}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center print:hidden">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Generate Payslip</h2>
                <div class="flex space-x-3">
                    <button @click="printPayslip" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Payslip
                    </button>
                    <Link :href="route('admin.payroll.show', payroll.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
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

        <div class="py-12 print:py-0">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 print:max-w-full print:px-0">
                <!-- Payslip Document -->
                <div class="bg-white shadow-sm print:shadow-none print:border-0" style="min-height: 11in;">
                    <div class="p-8 print:p-6">
                        <!-- Company Header -->
                        <div class="text-center border-b-2 border-gray-300 pb-6 mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Al-Biruni Health</h1>
                            <p class="text-gray-600">Office 111 CEO building executive
                                Offices, DIP-1, Dubai</p>
                            <p class="text-gray-600">Phone: 800 (25247864) | Email: reception@abhhc.ae</p>
                        </div>

                        <!-- Payslip Header -->
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">SALARY SLIP</h2>
                                <p class="text-lg text-gray-700">{{ getMonthName(payroll.month) }} {{ payroll.year }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Generated on</p>
                                <p class="text-lg font-semibold text-gray-900">{{ formatDate(new Date()) }}</p>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="grid grid-cols-2 gap-8 mb-8 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Employee Details</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Name:</span>
                                        <span class="text-sm text-gray-900">{{ payroll.employee.employee_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Employee ID:</span>
                                        <span class="text-sm text-gray-900">EMP-{{ String(payroll.employee.id).padStart(4, '0') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Designation:</span>
                                        <span class="text-sm text-gray-900">{{ payroll.employee.designation?.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Department:</span>
                                        <span class="text-sm text-gray-900">{{ payroll.employee.department?.name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Pay Period:</span>
                                        <span class="text-sm text-gray-900">{{ getMonthName(payroll.month) }} {{ payroll.year }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Payment Date:</span>
                                        <span class="text-sm text-gray-900">{{ formatDate(payroll.payment_date) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Status:</span>
                                        <span class="text-sm font-semibold" :class="payroll.status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
                                           {{ payroll.status.toUpperCase() }}
                                       </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Location:</span>
                                        <span class="text-sm text-gray-900">{{ payroll.employee.location?.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Breakdown -->
                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <!-- Earnings -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-300 pb-2">EARNINGS</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-1">
                                        <span class="text-sm text-gray-700">Basic Salary</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.basic_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-sm text-gray-700">Accommodation Allowance</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.accommodation) }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-sm text-gray-700">Other Allowances</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.allowances) }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-sm text-gray-700">Overtime Amount</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.overtime_amount) }}</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-2 mt-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-semibold text-gray-900">TOTAL EARNINGS</span>
                                            <span class="text-sm font-bold text-green-600">{{ formatCurrency(payroll.gross_salary) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deductions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-300 pb-2">DEDUCTIONS</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-1">
                                        <span class="text-sm text-gray-700">Total Deductions</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(payroll.deductions) }}</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-2 mt-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-semibold text-gray-900">TOTAL DEDUCTIONS</span>
                                            <span class="text-sm font-bold text-red-600">{{ formatCurrency(payroll.deductions) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Net Pay Summary -->
                        <div class="bg-blue-50 p-6 rounded-lg mb-8">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Total Earnings</p>
                                    <p class="text-xl font-bold text-green-600">{{ formatCurrency(payroll.gross_salary) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Total Deductions</p>
                                    <p class="text-xl font-bold text-red-600">{{ formatCurrency(payroll.deductions) }}</p>
                                </div>
                                <div class="text-center border-l-2 border-blue-300">
                                    <p class="text-sm text-gray-600">NET PAY</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(payroll.net_salary) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t-2 border-gray-300 pt-6">
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm text-gray-600 mb-4">This is a computer generated payslip and does not require signature.</p>
                                    <p class="text-xs text-gray-500">
                                        <strong>Note:</strong> Please keep this payslip for your records.
                                        For any queries regarding your salary, please contact the HR department.
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="mt-8">
                                        <div class="border-t border-gray-400 w-48 ml-auto mb-2"></div>
                                        <p class="text-sm text-gray-600">Authorized Signature</p>
                                        <p class="text-xs text-gray-500">HR Department</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Print Footer -->
                        <div class="text-center mt-8 text-xs text-gray-400 print:block hidden">
                            <p>Generated on {{ formatDate(new Date()) }} | Company Confidential</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>


</template>