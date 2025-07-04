<script setup>
    import { ref, computed } from 'vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        leaveBalance: Object,
        employee: Object,
    });

    const form = useForm({
        leave_type: '',
        start_date: '',
        end_date: '',
        reason: '',
    });

    const calculateDays = computed(() => {
        if (form.start_date && form.end_date) {
            const start = new Date(form.start_date);
            const end = new Date(form.end_date);
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }
        return 0;
    });

    const getAvailableBalance = computed(() => {
        switch (form.leave_type) {
            case 'annual':
                return props.leaveBalance.annual_available;
            case 'medical':
                return props.leaveBalance.medical_available;
            case 'emergency':
                return 'No limit';
            default:
                return 0;
        }
    });

    const canApplyLeaveType = computed(() => {
        switch (form.leave_type) {
            case 'annual':
                return props.leaveBalance.can_apply_annual;
            case 'medical':
            case 'emergency':
                return true;
            default:
                return false;
        }
    });

    // Get minimum date based on leave type
    const getMinStartDate = computed(() => {
        const today = new Date();

        switch (form.leave_type) {
            case 'annual':
                // Annual leave: can only apply 30 days from current date
                const annualMinDate = new Date();
                annualMinDate.setDate(today.getDate() + 30);
                return annualMinDate.toISOString().split('T')[0];
            case 'medical':
                // Medical leave: can apply for previous dates (no restriction)
                return null; // No minimum date restriction
            case 'emergency':
                // Emergency leave: from today onwards
                return today.toISOString().split('T')[0];
            default:
                return today.toISOString().split('T')[0];
        }
    });

    const getMinEndDate = computed(() => {
        if (form.start_date) {
            return form.start_date;
        }
        return getMinStartDate.value;
    });

    const isValidDateRange = computed(() => {
        if (!form.start_date || !form.end_date) return true;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const startDate = new Date(form.start_date);
        const endDate = new Date(form.end_date);

        // End date should be >= start date
        if (endDate < startDate) {
            return false;
        }

        switch (form.leave_type) {
            case 'annual':
                // Annual leave: must be 30 days from today
                const annualMinDate = new Date();
                annualMinDate.setDate(today.getDate() + 30);
                annualMinDate.setHours(0, 0, 0, 0);
                return startDate >= annualMinDate;
            case 'medical':
                // Medical leave: can be any date (past, present, future)
                return true;
            case 'emergency':
                // Emergency leave: from today onwards
                return startDate >= today;
            default:
                return startDate >= today;
        }
    });

    const getDateValidationMessage = computed(() => {
        if (!form.leave_type) return '';

        switch (form.leave_type) {
            case 'annual':
                const today = new Date();
                const minDate = new Date();
                minDate.setDate(today.getDate() + 30);
                return `You can only apply for annual leave from ${minDate.toLocaleDateString()} onwards (30 days from current date).`;
            case 'medical':
                return 'Medical leave can be applied for any date (past, present, or future).';
            case 'emergency':
                return 'Emergency leave can only be applied from today onwards.';
            default:
                return '';
        }
    });

    const submit = () => {
        form.post(route('employee.leave.store'), {
            preserveScroll: true,
        });
    };

    const formatDate = (dateString) => {
        return dateString ? new Date(dateString).toLocaleDateString() : 'N/A';
    };
</script>

<template>
    <Head title="Apply for Leave" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apply for Leave</h2>
                <Link :href="route('employee.leave.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Leave Requests
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Leave Balance Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-blue-800">Your Leave Balance</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Annual Leave -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Annual Leave</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="leaveBalance.can_apply_annual ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ leaveBalance.can_apply_annual ? 'Eligible' : 'Not Eligible' }}
                                        </span>
                                    </div>
                                    <div class="text-2xl font-bold text-blue-600">{{ leaveBalance.annual_available }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Accrued: {{ leaveBalance.annual_accrued }} | Used: {{ leaveBalance.annual_used }}
                                    </div>
                                    <div v-if="!leaveBalance.can_apply_annual" class="text-xs text-red-600 mt-2">
                                        Available after 30 days from visa start date
                                    </div>
                                </div>

                                <!-- Medical Leave -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Medical Leave</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Available
                                        </span>
                                    </div>
                                    <div class="text-2xl font-bold text-red-600">{{ leaveBalance.medical_available }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Used: {{ leaveBalance.medical_used }}/14 days this year
                                    </div>
                                </div>

                                <!-- Emergency Leave -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Emergency Leave</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                            No Limit
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">Available from current date onwards</div>
                                </div>

                                <!-- Important Notes -->
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Important Notes</h4>
                                    <ul class="text-xs text-yellow-700 space-y-1">
                                        <li>• Annual leave: 2.5 days per month from visa start</li>
                                        <li>• Annual leave: Can apply 30 days from current date</li>
                                        <li>• Medical leave: Maximum 14 days per year, any date allowed</li>
                                        <li>• Emergency leave: No balance required, from today onwards</li>
                                        <li>• All leaves require manager approval</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Application Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Leave Application Form</h3>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="submit" class="space-y-6">
                                    <!-- Leave Type -->
                                    <div>
                                        <InputLabel for="leave_type" value="Leave Type" />
                                        <select
                                                id="leave_type"
                                                v-model="form.leave_type"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="">Select Leave Type</option>
                                            <option value="annual" :disabled="!leaveBalance.can_apply_annual">Annual Leave</option>
                                            <option value="medical">Medical Leave</option>
                                            <option value="emergency">Emergency Leave</option>
                                        </select>
                                        <InputError :message="form.errors.leave_type" class="mt-2" />
                                        <div v-if="form.leave_type && !canApplyLeaveType" class="mt-2 text-sm text-red-600">
                                            You are not eligible to apply for this leave type.
                                        </div>
                                    </div>

                                    <!-- Available Balance Display -->
                                    <div v-if="form.leave_type" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-blue-800">Available Balance:</span>
                                            <span class="text-lg font-bold text-blue-600">{{ getAvailableBalance }} days</span>
                                        </div>
                                    </div>

                                    <!-- Date Restriction Information -->
                                    <div v-if="form.leave_type" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-sm font-medium text-gray-800">Date Selection Rules</h4>
                                                <p class="text-sm text-gray-600 mt-1">{{ getDateValidationMessage }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <InputLabel for="start_date" value="Start Date" />
                                            <TextInput
                                                    id="start_date"
                                                    v-model="form.start_date"
                                                    type="date"
                                                    class="mt-1 block w-full"
                                                    :min="getMinStartDate"
                                                    required
                                            />
                                            <InputError :message="form.errors.start_date" class="mt-2" />
                                            <div v-if="form.leave_type === 'annual'" class="text-xs text-blue-600 mt-1">
                                                ℹ️ Annual leave can only be applied 30 days from current date
                                            </div>
                                            <div v-if="form.leave_type === 'medical'" class="text-xs text-green-600 mt-1">
                                                ℹ️ Medical leave can be applied for any date
                                            </div>
                                            <div v-if="form.leave_type === 'emergency'" class="text-xs text-orange-600 mt-1">
                                                ℹ️ Emergency leave from today onwards only
                                            </div>
                                        </div>

                                        <div>
                                            <InputLabel for="end_date" value="End Date" />
                                            <TextInput
                                                    id="end_date"
                                                    v-model="form.end_date"
                                                    type="date"
                                                    class="mt-1 block w-full"
                                                    :min="getMinEndDate"
                                                    required
                                            />
                                            <InputError :message="form.errors.end_date" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Days Calculation -->
                                    <div v-if="calculateDays > 0" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">Total Days Requested:</span>
                                            <span class="text-lg font-bold" :class="form.leave_type !== 'emergency' && calculateDays > getAvailableBalance ? 'text-red-600' : 'text-green-600'">
                                                {{ calculateDays }} days
                                            </span>
                                        </div>
                                        <div v-if="form.leave_type !== 'emergency' && calculateDays > getAvailableBalance" class="mt-2 text-sm text-red-600">
                                            ⚠️ You don't have sufficient leave balance for this request.
                                        </div>
                                    </div>

                                    <!-- Reason -->
                                    <div>
                                        <InputLabel for="reason" value="Reason for Leave" />
                                        <textarea
                                                id="reason"
                                                v-model="form.reason"
                                                rows="4"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Please provide a detailed reason for your leave request..."
                                                required
                                        ></textarea>
                                        <InputError :message="form.errors.reason" class="mt-2" />
                                    </div>

                                    <!-- Validation Warnings -->
                                    <div v-if="!isValidDateRange && form.start_date && form.end_date" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Invalid Date Selection</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p v-if="form.leave_type === 'annual'">Annual leave can only be applied 30 days from current date onwards.</p>
                                                    <p v-else-if="form.leave_type === 'emergency'">Emergency leave can only be applied from today onwards.</p>
                                                    <p v-else>Please ensure end date is not before start date.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex items-center justify-end space-x-4">
                                        <SecondaryButton :href="route('employee.leave.index')">
                                            Cancel
                                        </SecondaryButton>
                                        <PrimaryButton
                                                :disabled="form.processing || !canApplyLeaveType || !isValidDateRange || (form.leave_type !== 'emergency' && calculateDays > getAvailableBalance)"
                                        >
                                            <span v-if="form.processing">Submitting...</span>
                                            <span v-else>Submit Leave Request</span>
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>