<!-- resources/js/Pages/Admin/Payroll/Create.vue -->
<script setup>
    import { ref, computed } from 'vue';
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

    const form = useForm({
        employee_id: '',
        basic_salary: '',
        accommodation: '',
        allowances: '',
        overtime_amount: '',
        deductions: '',
        month: new Date().getMonth() + 1,
        year: new Date().getFullYear(),
    });

    const grossSalary = computed(() => {
        const basic = parseFloat(form.basic_salary) || 0;
        const accommodation = parseFloat(form.accommodation) || 0;
        const allowances = parseFloat(form.allowances) || 0;
        const overtime = parseFloat(form.overtime_amount) || 0;
        return basic + accommodation + allowances + overtime;
    });

    const netSalary = computed(() => {
        const deductions = parseFloat(form.deductions) || 0;
        return grossSalary.value - deductions;
    });

    const submit = () => {
        form.post(route('admin.payroll.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'AED',
            minimumFractionDigits: 2
        }).format(amount);
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
    <Head title="Create Payroll" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Payroll</h2>
                <SecondaryButton :href="route('admin.payroll.index')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Payroll
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Employee and Period Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Employee & Period Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Employee -->
                                    <div>
                                        <InputLabel for="employee_id" value="Employee" />
                                        <select
                                                id="employee_id"
                                                v-model="form.employee_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                                autofocus
                                        >
                                            <option value="">Select Employee</option>
                                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                                {{ employee.employee_name }} - {{ employee.department?.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.employee_id" class="mt-2" />
                                    </div>

                                    <!-- Month -->
                                    <div>
                                        <InputLabel for="month" value="Month" />
                                        <select
                                                id="month"
                                                v-model="form.month"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option v-for="month in 12" :key="month" :value="month">
                                                {{ getMonthName(month) }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.month" class="mt-2" />
                                    </div>

                                    <!-- Year -->
                                    <div>
                                        <InputLabel for="year" value="Year" />
                                        <select
                                                id="year"
                                                v-model="form.year"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option v-for="year in [2024, 2025, 2026]" :key="year" :value="year">
                                                {{ year }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.year" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Salary Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Basic Salary -->
                                    <div>
                                        <InputLabel for="basic_salary" value="Basic Salary" />
                                        <TextInput
                                                id="basic_salary"
                                                v-model="form.basic_salary"
                                                type="number"
                                                step="0.01"
                                                class="mt-1 block w-full"
                                                required
                                                placeholder="0.00"
                                        />
                                        <InputError :message="form.errors.basic_salary" class="mt-2" />
                                    </div>

                                    <!-- Accommodation -->
                                    <div>
                                        <InputLabel for="accommodation" value="Accommodation Allowance" />
                                        <TextInput
                                                id="accommodation"
                                                v-model="form.accommodation"
                                                type="number"
                                                step="0.01"
                                                class="mt-1 block w-full"
                                                placeholder="0.00"
                                        />
                                        <InputError :message="form.errors.accommodation" class="mt-2" />
                                    </div>

                                    <!-- Other Allowances -->
                                    <div>
                                        <InputLabel for="allowances" value="Other Allowances" />
                                        <TextInput
                                                id="allowances"
                                                v-model="form.allowances"
                                                type="number"
                                                step="0.01"
                                                class="mt-1 block w-full"
                                                placeholder="0.00"
                                        />
                                        <InputError :message="form.errors.allowances" class="mt-2" />
                                    </div>

                                    <!-- Overtime Amount -->
                                    <div>
                                        <InputLabel for="overtime_amount" value="Overtime Amount" />
                                        <TextInput id="overtime_amount"
                                                   v-model="form.overtime_amount"
                                                   type="number"
                                                   step="0.01"
                                                   class="mt-1 block w-full"
                                                   placeholder="0.00"
                                        />
                                        <InputError :message="form.errors.overtime_amount" class="mt-2" />
                                    </div>

                                    <!-- Deductions -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="deductions" value="Deductions" />
                                        <TextInput
                                                id="deductions"
                                                v-model="form.deductions"
                                                type="number"
                                                step="0.01"
                                                class="mt-1 block w-full"
                                                placeholder="0.00"
                                        />
                                        <InputError :message="form.errors.deductions" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Summary -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Salary Summary</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-600">Basic Salary:</span>
                                                <span class="text-sm text-gray-900">{{ formatCurrency(parseFloat(form.basic_salary) || 0) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-600">Accommodation:</span>
                                                <span class="text-sm text-gray-900">{{ formatCurrency(parseFloat(form.accommodation) || 0) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-600">Allowances:</span>
                                                <span class="text-sm text-gray-900">{{ formatCurrency(parseFloat(form.allowances) || 0) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-600">Overtime:</span>
                                                <span class="text-sm text-gray-900">{{ formatCurrency(parseFloat(form.overtime_amount) || 0) }}</span>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-600">Deductions:</span>
                                                <span class="text-sm text-gray-900">{{ formatCurrency(parseFloat(form.deductions) || 0) }}</span>
                                            </div>
                                            <div class="flex justify-between border-t pt-2">
                                                <span class="text-sm font-semibold text-gray-700">Gross Salary:</span>
                                                <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(grossSalary) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-lg font-bold text-gray-800">Net Salary:</span>
                                                <span class="text-lg font-bold text-green-600">{{ formatCurrency(netSalary) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end space-x-4">
                                <SecondaryButton :href="route('admin.payroll.index')">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Payroll</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>