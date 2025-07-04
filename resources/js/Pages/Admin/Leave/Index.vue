<!-- resources/js/Pages/Admin/Leave/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        leaves: Object,
        stats: Object,
        filters: Object,
    });

    // Search and filters
    const search = ref(props.filters.employee || '');
    const perPage = ref(props.filters.per_page || 15);
    const statusFilter = ref(props.filters.status || '');
    const leaveTypeFilter = ref(props.filters.leave_type || '');
    const dateFrom = ref(props.filters.date_from || '');
    const dateTo = ref(props.filters.date_to || '');

    // Modal states
    const showApproveModal = ref(false);
    const showRejectModal = ref(false);
    const currentLeave = ref(null);

    // Forms
    const approveForm = useForm({
        comments: ''
    });

    const rejectForm = useForm({
        comments: ''
    });

    // Watch for URL changes
    watch([search, perPage, statusFilter, leaveTypeFilter, dateFrom, dateTo], () => {
        router.get(route('admin.leave.index'), {
            employee: search.value,
            per_page: perPage.value,
            status: statusFilter.value,
            leave_type: leaveTypeFilter.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
        }, {
            preserveState: true,
            replace: true,
        });
    });

    const openApproveModal = (leave) => {
        currentLeave.value = leave;
        approveForm.reset();
        showApproveModal.value = true;
    };

    const openRejectModal = (leave) => {
        currentLeave.value = leave;
        rejectForm.reset();
        showRejectModal.value = true;
    };

    const closeModals = () => {
        showApproveModal.value = false;
        showRejectModal.value = false;
        currentLeave.value = null;
    };

    const approveLeave = () => {
        approveForm.post(route('admin.leave.approve', currentLeave.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const rejectLeave = () => {
        rejectForm.post(route('admin.leave.reject', currentLeave.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const resetFilters = () => {
        search.value = '';
        statusFilter.value = '';
        leaveTypeFilter.value = '';
        dateFrom.value = '';
        dateTo.value = '';
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
    <Head title="Leave Management" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Management</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.leave.report')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Leave Report
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-3 lg:grid-cols-5">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Requests</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.rejected }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Approval</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.pending_admin_approval }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                                <SearchInput v-model="search" placeholder="Search employee..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                <select v-model="leaveTypeFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Types</option>
                                    <option value="annual">Annual</option>
                                    <option value="medical">Medical</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input v-model="dateFrom" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input v-model="dateTo" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                <select v-model="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                            </div>
                            <button @click="resetFilters" class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Leave Requests Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="leave in leaves.data" :key="leave.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                   <span class="text-white text-sm font-semibold">
                                                       {{ leave.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ leave.employee.employee_name }}</div>
                                                <div class="text-sm text-gray-500">{{ leave.employee.designation?.name }} - {{ leave.employee.department?.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                           {{ leave.leave_type }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ formatDate(leave.start_date) }}</div>
                                        <div class="text-gray-500">to {{ formatDate(leave.end_date) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ leave.days_requested }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(leave.manager_status)">
                                           {{ leave.manager_status }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(leave.admin_status)">
                                           {{ leave.admin_status }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(leave.overall_status)">
                                           {{ leave.overall_status }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link :href="route('admin.leave.show', leave.id)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <button v-if="leave.manager_status === 'approved' && leave.admin_status === 'pending'"
                                                    @click="openApproveModal(leave)"
                                                    class="text-green-600 hover:text-green-900" title="Approve">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button v-if="leave.manager_status === 'approved' && leave.admin_status === 'pending'"
                                                    @click="openRejectModal(leave)"
                                                    class="text-red-600 hover:text-red-900" title="Reject">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="leaves.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No leave requests found.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="leaves.links" />
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
                                            Are you sure you want to approve this leave request from {{ currentLeave?.employee.employee_name }}?
                                        </p>
                                        <div class="mt-4">
                                            <label for="approve_comments" class="block text-sm font-medium text-gray-700">Comments (Optional)</label>
                                            <textarea
                                                    id="approve_comments"
                                                    v-model="approveForm.comments"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Add any comments..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="approveForm.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                <span v-if="approveForm.processing">Approving...</span>
                                <span v-else>Approve</span>
                            </button>
                            <button type="button" @click="closeModals" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
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
                                            Are you sure you want to reject this leave request from {{ currentLeave?.employee.employee_name }}?
                                        </p>
                                        <div class="mt-4">
                                            <label for="reject_comments" class="block text-sm font-medium text-gray-700">Reason for Rejection *</label>
                                            <textarea
                                                    id="reject_comments"
                                                    v-model="rejectForm.comments"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Please provide reason for rejection..."
                                                    required
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="rejectForm.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                <span v-if="rejectForm.processing">Rejecting...</span>
                                <span v-else>Reject</span>
                            </button>
                            <button type="button" @click="closeModals" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>