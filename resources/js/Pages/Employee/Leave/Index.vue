<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        leaves: Object,
        leaveBalance: Object,
    });

    const perPage = ref(15);

    watch([perPage], () => {
        router.get(route('employee.leave.index'), {
            per_page: perPage.value,
        }, {
            preserveState: true,
            replace: true,
        });
    });

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

    const getApprovalSteps = (leave) => {
        const steps = [
            { name: 'Submitted', status: 'completed' },
            { name: 'Manager Review', status: leave.manager_status === 'pending' ? 'current' : leave.manager_status === 'approved' ? 'completed' : 'rejected' },
            { name: 'Admin Review', status: leave.admin_status === 'pending' && leave.manager_status === 'approved' ? 'current' : leave.admin_status === 'approved' ? 'completed' : leave.admin_status === 'rejected' ? 'rejected' : 'upcoming' },
            { name: 'Final Decision', status: leave.overall_status === 'approved' ? 'completed' : leave.overall_status === 'rejected' ? 'rejected' : 'upcoming' }
        ];
        return steps;
    };
</script>

<template>
    <Head title="My Leave Requests" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Leave Requests</h2>
                <Link :href="route('employee.leave.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Apply for Leave
                </Link>
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

                <!-- Leave Requests Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">My Leave History</h3>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Show:</label>
                            <select v-model="perPage" class="text-sm border-gray-300 rounded-md">
                                <option :value="15">15</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div v-for="leave in leaves.data" :key="leave.id" class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:bg-gray-100 transition">
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

                                        <!-- Approval Timeline -->
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Approval Status</p>
                                            <div class="flex items-center space-x-4">
                                                <div v-for="(step, index) in getApprovalSteps(leave)" :key="index" class="flex items-center">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
                                                             :class="{
                                                                 'bg-green-500 text-white': step.status === 'completed',
                                                                 'bg-blue-500 text-white': step.status === 'current',
                                                                 'bg-red-500 text-white': step.status === 'rejected',
                                                                 'bg-gray-300 text-gray-600': step.status === 'upcoming'
                                                             }">
                                                            <svg v-if="step.status === 'completed'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            <svg v-else-if="step.status === 'rejected'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span v-else-if="step.status === 'current'">{{ index + 1 }}</span>
                                                            <span v-else>{{ index + 1 }}</span>
                                                        </div>
                                                        <span class="ml-2 text-xs font-medium text-gray-700">{{ step.name }}</span>
                                                    </div>
                                                    <div v-if="index < getApprovalSteps(leave).length - 1" class="w-8 h-0.5 bg-gray-300 mx-2"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Comments -->
                                        <div v-if="leave.manager_comments || leave.admin_comments" class="space-y-2">
                                            <div v-if="leave.manager_comments" class="bg-blue-50 border-l-4 border-blue-400 p-3">
                                                <p class="text-sm font-medium text-blue-800">Manager Comments</p>
                                                <p class="text-sm text-blue-700">{{ leave.manager_comments }}</p>
                                            </div>
                                            <div v-if="leave.admin_comments" class="bg-purple-50 border-l-4 border-purple-400 p-3">
                                                <p class="text-sm font-medium text-purple-800">Admin Comments</p>
                                                <p class="text-sm text-purple-700">{{ leave.admin_comments }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-4">
                                        <Link :href="route('employee.leave.show', leave.id)" class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
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
                                    <Link :href="route('employee.leave.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Apply for Leave
                                    </Link>
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
    </EmployeeLayout>
</template>