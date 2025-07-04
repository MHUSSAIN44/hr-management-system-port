<script setup>
    import { ref } from 'vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        leave: Object,
    });

    // Modal states
    const showApproveModal = ref(false);
    const showRejectModal = ref(false);

    // Forms
    const approveForm = useForm({
        comments: ''
    });

    const rejectForm = useForm({
        comments: ''
    });

    const openApproveModal = () => {
        approveForm.reset();
        showApproveModal.value = true;
    };

    const openRejectModal = () => {
        rejectForm.reset();
        showRejectModal.value = true;
    };

    const closeModals = () => {
        showApproveModal.value = false;
        showRejectModal.value = false;
    };

    const approveLeave = () => {
        approveForm.post(route('admin.leave.approve', props.leave.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const rejectLeave = () => {
        rejectForm.post(route('admin.leave.reject', props.leave.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

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

    const getApprovalSteps = () => {
        const steps = [
            {
                name: 'Submitted',
                status: 'completed',
                date: props.leave.created_at,
                description: 'Leave request submitted',
                actor: props.leave.employee.employee_name
            },
            {
                name: 'Manager Review',
                status: props.leave.manager_status === 'pending' ? 'current' : props.leave.manager_status === 'approved' ? 'completed' : 'rejected',
                date: props.leave.manager_approved_at,
                description: props.leave.manager_status === 'approved' ? 'Approved by manager' : props.leave.manager_status === 'rejected' ? 'Rejected by manager' : 'Pending manager approval',
                actor: 'Manager'
            },
            {
                name: 'Admin Review',
                status: props.leave.admin_status === 'pending' && props.leave.manager_status === 'approved' ? 'current' : props.leave.admin_status === 'approved' ? 'completed' : props.leave.admin_status === 'rejected' ? 'rejected' : 'upcoming',
                date: props.leave.admin_approved_at,
                description: props.leave.admin_status === 'approved' ? 'Approved by admin' : props.leave.admin_status === 'rejected' ? 'Rejected by admin' : props.leave.manager_status === 'approved' ? 'Pending admin approval' : 'Waiting for manager approval',
                actor: 'Admin'
            },
            {
                name: 'Final Decision',
                status: props.leave.overall_status === 'approved' ? 'completed' : props.leave.overall_status === 'rejected' ? 'rejected' : 'upcoming',
                date: props.leave.overall_status === 'approved' ? props.leave.admin_approved_at : null,
                description: props.leave.overall_status === 'approved' ? 'Leave approved' : props.leave.overall_status === 'rejected' ? 'Leave rejected' : 'Pending final decision',
                actor: 'System'
            }
        ];
        return steps;
    };

    const canApproveOrReject = () => {
        return props.leave.manager_status === 'approved' && props.leave.admin_status === 'pending';
    };

    const calculateLeaveBalance = () => {
        const employee = props.leave.employee;
        return {
            annual_available: employee.annual_leave_balance || 0,
            medical_available: employee.medical_leave_balance || 0,
            days_requested: props.leave.days_requested
        };
    };
</script>

<template>
    <Head :title="`Leave Request - ${leave.employee.employee_name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Request Details</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.leave.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Leave Management
                    </Link>
                    <button v-if="canApproveOrReject()" @click="openApproveModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                    <button v-if="canApproveOrReject()" @click="openRejectModal" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Employee & Leave Request Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="mr-6">
                                    <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                       <span class="text-gray-500 text-2xl font-semibold">
                                           {{ leave.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                       </span>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ leave.employee.employee_name }}</h2>
                                    <p class="text-lg text-gray-600">{{ leave.employee.designation?.name }}</p>
                                    <div class="mt-1 flex items-center flex-wrap">
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Department:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ leave.employee.department?.name }}</span>
                                        </div>
                                        <div class="mr-4 py-1">
                                            <span class="text-sm text-gray-500">Location:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ leave.employee.location?.name }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center flex-wrap">
                                       <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mr-3" :class="getLeaveTypeColor(leave.leave_type)">
                                           {{ leave.leave_type }} Leave
                                       </span>
                                        <span class="text-sm text-gray-500">{{ leave.days_requested }} days</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="mb-2">
                                   <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium" :class="getStatusColor(leave.overall_status)">
                                       {{ leave.overall_status }}
                                   </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Applied on {{ formatDate(leave.created_at) }}
                                </div>
                                <div v-if="canApproveOrReject()" class="mt-2">
                                   <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                       Pending Your Action
                                   </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Leave Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Leave Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-blue-800">Leave Information</h3>
                            </div>
                            <div class="p-6">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                                        <dd class="mt-1">
                                           <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                               {{ leave.leave_type }} Leave
                                           </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ leave.days_requested }} days</dd>
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
                                        <dt class="text-sm font-medium text-gray-500">Application Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(leave.created_at) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900">#{{ leave.employee.id }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Reason for Leave -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Reason for Leave</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ leave.reason }}</p>
                            </div>
                        </div>

                        <!-- Approval Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                                        <p class="text-xs text-gray-400">{{ step.actor }}</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time v-if="step.date">{{ formatDateTime(step.date) }}</time>
                                                        <span v-else class="text-gray-400">Pending</span>
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
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Employee Leave Balance -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-green-800">Leave Balance</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ leave.employee.annual_leave_balance || 0 }}</div>
                                    <div class="text-sm text-gray-500">Annual Leave Available</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ leave.employee.medical_leave_balance || 0 }}</div>
                                    <div class="text-sm text-gray-500">Medical Leave Available</div>
                                </div>
                                <div v-if="leave.leave_type !== 'emergency'" class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-700">Balance Check</div>
                                    <div class="text-xs text-gray-600 mt-1">
                                       <span v-if="leave.leave_type === 'annual'">
                                           Requested: {{ leave.days_requested }} days<br>
                                           Available: {{ leave.employee.annual_leave_balance || 0 }} days
                                       </span>
                                        <span v-else-if="leave.leave_type === 'medical'">
                                           Requested: {{ leave.days_requested }} days<br>
                                           Available: {{ leave.employee.medical_leave_balance || 0 }} days
                                       </span>
                                    </div>
                                    <div class="mt-2">
                                       <span v-if="leave.leave_type === 'annual' && leave.days_requested <= (leave.employee.annual_leave_balance || 0)" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                           ✓ Sufficient Balance
                                       </span>
                                        <span v-else-if="leave.leave_type === 'medical' && leave.days_requested <= (leave.employee.medical_leave_balance || 0)" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                           ✓ Sufficient Balance
                                       </span>
                                        <span v-else-if="leave.leave_type !== 'emergency'" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                           ⚠️ Insufficient Balance
                                       </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <Link :href="route('admin.leave.index')" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    All Leave Requests
                                </Link>

                                <Link :href="route('admin.leave.report')" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Leave Report
                                </Link>

                                <button onclick="window.print()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Print Details
                                </button>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 bg-indigo-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-indigo-800">Employee Details</h3>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ leave.employee.department?.name || 'Not assigned' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Designation</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ leave.employee.designation?.name || 'Not assigned' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ leave.employee.location?.name || 'Not assigned' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Contact</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ leave.employee.contact || 'Not provided' }}</dd>
                                    </div>
                                    <div v-if="leave.employee.visa_start_date">
                                        <dt class="text-sm font-medium text-gray-500">Visa Start Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(leave.employee.visa_start_date) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Action Required Notice -->
                        <div v-if="canApproveOrReject()" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Action Required</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This leave request has been approved by the manager and is waiting for your final decision.</p>
                                    </div>
                                    <div class="mt-4 flex space-x-2">
                                        <button @click="openApproveModal" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium">
                                            Approve
                                        </button>
                                        <button @click="openRejectModal" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="approveLeave">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Approve Leave Request</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to approve this {{ leave.leave_type }} leave request from {{ leave.employee.employee_name }}?
                                        </p>
                                        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-700">Leave Details:</div>
                                                <div class="text-gray-600">{{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}</div>
                                                <div class="text-gray-600">{{ leave.days_requested }} days</div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <InputLabel for="approve_comments" value="Comments (Optional)" />
                                            <textarea
                                                    id="approve_comments"
                                                    v-model="approveForm.comments"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Add any comments for the employee..."
                                            ></textarea>
                                            <InputError :message="approveForm.errors.comments" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="approveForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="approveForm.processing">Approving...</span>
                                <span v-else>Approve Leave</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="rejectLeave">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Reject Leave Request</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to reject this {{ leave.leave_type }} leave request from {{ leave.employee.employee_name }}?
                                        </p>
                                        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-700">Leave Details:</div>
                                                <div class="text-gray-600">{{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}</div>
                                                <div class="text-gray-600">{{ leave.days_requested }} days</div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <InputLabel for="reject_comments" value="Reason for Rejection *" />
                                            <textarea
                                                    id="reject_comments"
                                                    v-model="rejectForm.comments"
                                                    rows="4"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Please provide a detailed reason for rejecting this leave request..."
                                                    required
                                            ></textarea>
                                            <InputError :message="rejectForm.errors.comments" class="mt-2" />
                                            <p class="mt-1 text-xs text-gray-500">This reason will be shared with the employee.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="rejectForm.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                <span v-if="rejectForm.processing">Rejecting...</span>
                                <span v-else">Reject Leave</span>
                            </button>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
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