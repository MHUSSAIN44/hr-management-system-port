<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

    const props = defineProps({
        leave: Object,
    });

    const formatDate = (dateString) => {
        return dateString ? new Date(dateString).toLocaleDateString() : 'N/A';
    };

    const formatDateTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleString() : 'N/A';
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

    // Fixed: Use props.leave instead of just leave
    const getApprovalSteps = () => {
        const steps = [
            {
                name: 'Submitted',
                status: 'completed',
                date: props.leave.created_at,
                description: 'Leave request submitted'
            },
            {
                name: 'Manager Review',
                status: props.leave.manager_status === 'pending' ? 'current' : props.leave.manager_status === 'approved' ? 'completed' : 'rejected',
                date: props.leave.manager_approved_at,
                description: props.leave.manager_status === 'approved' ? 'Approved by manager' : props.leave.manager_status === 'rejected' ? 'Rejected by manager' : 'Pending manager approval'
            },
            {
                name: 'Admin Review',
                status: props.leave.admin_status === 'pending' && props.leave.manager_status === 'approved' ? 'current' : props.leave.admin_status === 'approved' ? 'completed' : props.leave.admin_status === 'rejected' ? 'rejected' : 'upcoming',
                date: props.leave.admin_approved_at,
                description: props.leave.admin_status === 'approved' ? 'Approved by admin' : props.leave.admin_status === 'rejected' ? 'Rejected by admin' : props.leave.manager_status === 'approved' ? 'Pending admin approval' : 'Waiting for manager approval'
            },
            {
                name: 'Final Decision',
                status: props.leave.overall_status === 'approved' ? 'completed' : props.leave.overall_status === 'rejected' ? 'rejected' : 'upcoming',
                date: props.leave.overall_status === 'approved' ? props.leave.admin_approved_at : null,
                description: props.leave.overall_status === 'approved' ? 'Leave approved' : props.leave.overall_status === 'rejected' ? 'Leave rejected' : 'Pending final decision'
            }
        ];
        return steps;
    };
</script>

<template>
    <Head :title="`Leave Request - ${leave.leave_type}`" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Request Details</h2>
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
                <!-- Leave Request Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="mr-6">
                                    <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-500 text-xl font-semibold">
                                            {{ leave.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ leave.employee.employee_name }}</h2>
                                    <p class="text-lg text-gray-600">{{ leave.employee.designation?.name }}</p>
                                    <div class="mt-1 flex items-center flex-wrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mr-3" :class="getLeaveTypeColor(leave.leave_type)">
                                            {{ leave.leave_type }} Leave
                                        </span>
                                        <span class="text-sm text-gray-500">{{ leave.days_requested }} days</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-lg font-medium" :class="getStatusColor(leave.overall_status)">
                                        {{ leave.overall_status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Applied on {{ formatDate(leave.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Leave Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-blue-800">Leave Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                            {{ leave.leave_type }} Leave
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(leave.start_date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(leave.end_date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ leave.days_requested }} days</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Application Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(leave.created_at) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Approval Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800">Approval Status</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Manager Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(leave.manager_status)">
                                            {{ leave.manager_status }}
                                        </span>
                                        <span v-if="leave.manager_approved_at" class="ml-2 text-xs text-gray-500">
                                            on {{ formatDateTime(leave.manager_approved_at) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Admin Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(leave.admin_status)">
                                            {{ leave.admin_status }}
                                        </span>
                                        <span v-if="leave.admin_approved_at" class="ml-2 text-xs text-gray-500">
                                            on {{ formatDateTime(leave.admin_approved_at) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Overall Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(leave.overall_status)">
                                            {{ leave.overall_status }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Reason for Leave</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed">{{ leave.reason }}</p>
                    </div>
                </div>

                <!-- Approval Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-purple-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-purple-800">Approval Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li v-for="(step, stepIdx) in getApprovalSteps()" :key="step.name" class="relative pb-8">
                                    <div v-if="stepIdx !== getApprovalSteps().length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                                  :class="{
                                                      'bg-green-500': step.status === 'completed',
                                                      'bg-blue-500': step.status === 'current',
                                                      'bg-red-500': step.status === 'rejected',
                                                      'bg-gray-300': step.status === 'upcoming'
                                                  }">
                                                <svg v-if="step.status === 'completed'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else-if="step.status === 'rejected'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else-if="step.status === 'current'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span v-else class="h-2.5 w-2.5 bg-gray-400 rounded-full"></span>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ step.name }}</p>
                                                <p class="text-sm text-gray-500">{{ step.description }}</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time v-if="step.date">{{ formatDateTime(step.date) }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div v-if="leave.manager_comments || leave.admin_comments" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-yellow-800">Comments & Feedback</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Manager Comments -->
                        <div v-if="leave.manager_comments" class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-blue-800">Manager Comments</h4>
                                    <p class="mt-1 text-sm text-blue-700">{{ leave.manager_comments }}</p>
                                    <p v-if="leave.manager_approved_at" class="mt-1 text-xs text-blue-600">
                                        {{ formatDateTime(leave.manager_approved_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Comments -->
                        <div v-if="leave.admin_comments" class="bg-purple-50 border-l-4 border-purple-400 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-purple-800">Admin Comments</h4>
                                    <p class="mt-1 text-sm text-purple-700">{{ leave.admin_comments }}</p>
                                    <p v-if="leave.admin_approved_at" class="mt-1 text-xs text-purple-600">
                                        {{ formatDateTime(leave.admin_approved_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('employee.leave.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                View All Leave Requests
                            </Link>

                            <Link :href="route('employee.leave.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Apply for New Leave
                            </Link>

                            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important Information</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Leave requests require approval from your manager and admin</li>
                                   <li>For urgent matters, please contact your manager directly</li>
                                    <li v-if="leave.overall_status === 'pending'">Your request is currently being processed</li>
                                    <li v-if="leave.overall_status === 'approved'">Please ensure proper handover before your leave starts</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>

<style scoped>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }
    }
</style>