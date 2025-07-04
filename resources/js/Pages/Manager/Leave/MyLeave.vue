<script setup>
    import { ref, computed } from 'vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';
    import Pagination from '@/Components/Pagination.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        leaves: Object,
        leaveBalance: Object,
        employee: Object,
    });

    const perPage = ref(15);
    const showApplyModal = ref(false);

    const applyForm = useForm({
        leave_type: '',
        start_date: '',
        end_date: '',
        reason: '',
    });

    const calculateDays = computed(() => {
        if (applyForm.start_date && applyForm.end_date) {
            const start = new Date(applyForm.start_date);
            const end = new Date(applyForm.end_date);
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }
        return 0;
    });

    const getAvailableBalance = computed(() => {
        switch (applyForm.leave_type) {
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
        switch (applyForm.leave_type) {
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

        switch (applyForm.leave_type) {
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
        if (applyForm.start_date) {
            return applyForm.start_date;
        }
        return getMinStartDate.value;
    });

    const isValidDateRange = computed(() => {
        if (!applyForm.start_date || !applyForm.end_date) return true;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const startDate = new Date(applyForm.start_date);
        const endDate = new Date(applyForm.end_date);

        // End date should be >= start date
        if (endDate < startDate) {
            return false;
        }

        switch (applyForm.leave_type) {
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
        if (!applyForm.leave_type) return '';

        switch (applyForm.leave_type) {
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

    const openApplyModal = () => {
        applyForm.reset();
        showApplyModal.value = true;
    };

    const closeModal = () => {
        showApplyModal.value = false;
        applyForm.reset();
    };

    const submitLeave = () => {
        applyForm.post(route('manager.leave.store'), {
            onSuccess: () => {
                closeModal();
            }
        });
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'approved':
                return 'bg-green-100 text-green-800';
            case 'rejected':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getLeaveTypeColor = (type) => {
        switch (type) {
            case 'annual':
                return 'bg-blue-100 text-blue-800';
            case 'medical':
                return 'bg-red-100 text-red-800';
            case 'emergency':
                return 'bg-orange-100 text-orange-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };
</script>

<template>
    <Head title="My Leave Requests" />

    <ManagerLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Leave Requests</h2>
                <div class="flex space-x-3">
                    <button @click="openApplyModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Apply for Leave
                    </button>
                    <Link :href="route('manager.leave.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Team Requests
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Leave Balance Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Annual Leave -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Annual Leave</p>
                                    <p class="text-3xl font-bold text-blue-600">{{ leaveBalance.annual_available }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Accrued: {{ leaveBalance.annual_accrued }} | Used: {{ leaveBalance.annual_used }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span v-if="leaveBalance.can_apply_annual" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Eligible
                                </span>
                                <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⏳ Not Eligible Yet
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Leave -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Medical Leave</p>
                                    <p class="text-3xl font-bold text-red-600">{{ leaveBalance.medical_available }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Used: {{ leaveBalance.medical_used }}/14 days this year
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Available
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Leave -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-orange-500">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Emergency Leave</p>
                                    <p class="text-xl font-bold text-orange-600">No Limit</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Available from current date onwards
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Always Available
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">My Leave History</h3>
                        <p class="text-sm text-gray-600">Note: As a manager, your leave requests go directly to admin for approval.</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div v-for="leave in leaves.data" :key="leave.id" class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                                {{ leave.leave_type }} Leave
                                            </span>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(leave.overall_status)">
                                                {{ leave.overall_status }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ leave.days_requested }} days</span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Duration</p>
                                                <p class="text-sm text-gray-600">{{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Applied On</p>
                                                <p class="text-sm text-gray-600">{{ formatDate(leave.created_at) }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700">Reason</p>
                                            <p class="text-sm text-gray-600">{{ leave.reason }}</p>
                                        </div>

                                        <!-- Approval Status -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Manager Status</p>
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" :class="getStatusColor(leave.manager_status)">
                                                    {{ leave.manager_status }} (Auto-approved)
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Admin Status</p>
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" :class="getStatusColor(leave.admin_status)">
                                                    {{ leave.admin_status }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Comments -->
                                        <div v-if="leave.admin_comments" class="bg-purple-50 border-l-4 border-purple-400 p-3">
                                            <p class="text-sm font-medium text-purple-800">Admin Comments</p>
                                            <p class="text-sm text-purple-700">{{ leave.admin_comments }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- No Leave Requests -->
                            <div v-if="leaves.data.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No leave requests</h3>
                                <p class="mt-1 text-sm text-gray-500">You haven't applied for any leaves yet.</p>
                                <div class="mt-6">
                                    <button @click="openApplyModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Apply for Leave
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="leaves.data.length > 0" class="mt-6">
                            <Pagination :links="leaves.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apply Leave Modal -->
        <div v-if="showApplyModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form @submit.prevent="submitLeave">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Apply for Leave</h3>
                            <p class="text-sm text-gray-600 mb-6">As a manager, your leave request will go directly to admin for approval.</p>

                            <div class="space-y-4">
                                <!-- Leave Type -->
                                <div>
                                    <InputLabel for="leave_type" value="Leave Type" />
                                    <select
                                            id="leave_type"
                                            v-model="applyForm.leave_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            required
                                    >
                                        <option value="">Select Leave Type</option>
                                        <option value="annual" :disabled="!leaveBalance.can_apply_annual">Annual Leave</option>
                                        <option value="medical">Medical Leave</option>
                                        <option value="emergency">Emergency Leave</option>
                                    </select>
                                    <InputError :message="applyForm.errors.leave_type" class="mt-2" />
                                    <div v-if="applyForm.leave_type && !canApplyLeaveType" class="mt-2 text-sm text-red-600">
                                        You are not eligible to apply for this leave type.
                                    </div>
                                </div>

                                <!-- Available Balance Display -->
                                <div v-if="applyForm.leave_type" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-blue-800">Available Balance:</span>
                                        <span class="text-lg font-bold text-blue-600">{{ getAvailableBalance }} days</span>
                                    </div>
                                </div>

                                <!-- Date Restriction Information -->
                                <div v-if="applyForm.leave_type" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="start_date" value="Start Date" />
                                        <TextInput
                                                id="start_date"
                                                v-model="applyForm.start_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                                :min="getMinStartDate"
                                                required
                                        />
                                        <InputError :message="applyForm.errors.start_date" class="mt-2" />
                                        <div v-if="applyForm.leave_type === 'annual'" class="text-xs text-blue-600 mt-1">
                                            ℹ️ Annual leave can only be applied 30 days from current date
                                        </div>
                                        <div v-if="applyForm.leave_type === 'medical'" class="text-xs text-green-600 mt-1">
                                            ℹ️ Medical leave can be applied for any date
                                        </div>
                                        <div v-if="applyForm.leave_type === 'emergency'" class="text-xs text-orange-600 mt-1">
                                            ℹ️ Emergency leave from today onwards only
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel for="end_date" value="End Date" />
                                        <TextInput
                                                id="end_date"
                                                v-model="applyForm.end_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                                :min="getMinEndDate"
                                                required
                                        />
                                        <InputError :message="applyForm.errors.end_date" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Days Calculation -->
                                <div v-if="calculateDays > 0" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Total Days Requested:</span>
                                        <span class="text-lg font-bold" :class="applyForm.leave_type !== 'emergency' && calculateDays > getAvailableBalance ? 'text-red-600' : 'text-green-600'">
                                            {{ calculateDays }} days
                                        </span>
                                    </div>
                                    <div v-if="applyForm.leave_type !== 'emergency' && calculateDays > getAvailableBalance" class="mt-2 text-sm text-red-600">
                                        ⚠️ You don't have sufficient leave balance for this request.
                                    </div>
                                </div>

                                <!-- Reason -->
                                <div>
                                    <InputLabel for="reason" value="Reason for Leave" />
                                    <textarea
                                            id="reason"
                                            v-model="applyForm.reason"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Please provide a detailed reason for your leave request..."
                                            required
                                    ></textarea>
                                    <InputError :message="applyForm.errors.reason" class="mt-2" />
                                </div>

                                <!-- Validation Warnings -->
                                <div v-if="!isValidDateRange && applyForm.start_date && applyForm.end_date" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Invalid Date Selection</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p v-if="applyForm.leave_type === 'annual'">Annual leave can only be applied 30 days from current date onwards.</p>
                                                <p v-else-if="applyForm.leave_type === 'emergency'">Emergency leave can only be applied from today onwards.</p>
                                                <p v-else>Please ensure end date is not before start date.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton
                                    type="submit"
                                    :disabled="applyForm.processing || !canApplyLeaveType || !isValidDateRange || (applyForm.leave_type !== 'emergency' && calculateDays > getAvailableBalance)"
                                    class="w-full sm:w-auto sm:ml-3"
                            >
                                <span v-if="applyForm.processing">Submitting...</span>
                                <span v-else>Submit Leave Request</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModal" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>