<!-- resources/js/Pages/Employee/Dashboard.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

    defineProps({
        employee: Object,
        stats: Object,
        recentLeaves: Array,
        thisMonthAttendance: Array,
        recentPayroll: Array,
        expiringDocuments: Array,
        todayAttendance: Object,
    });

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const formatTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
    };

    const getLeaveStatusColor = (status) => {
        switch (status) {
            case 'approved':
                return 'bg-green-100 text-green-800';
            case 'rejected':
                return 'bg-red-100 text-red-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
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

    const getAttendanceRate = () => {
        if (!thisMonthAttendance || thisMonthAttendance.length === 0) return 0;
        const presentDays = thisMonthAttendance.filter(att => att.status === 'present').length;
        return Math.round((presentDays / thisMonthAttendance.length) * 100);
    };
</script>

<template>
    <Head title="Employee Dashboard" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Welcome back, {{ employee.employee_name }}!</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ employee.designation.name }} - {{ employee.department.name }}</p>
                </div>
                <Link :href="route('employee.profile')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    View Profile
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Annual Leave Balance</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.annual_leave_balance }} days</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Medical Leave Balance</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.medical_leave_balance }} days</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.pending_leave_requests }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Documents Expiring</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.documents_expiring_soon }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <Link :href="route('employee.leave.create')" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <div class="font-semibold">Request Leave</div>
                    </Link>

                    <Link :href="route('employee.attendance.index')" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="font-semibold">Check Attendance</div>
                    </Link>

                    <Link :href="route('employee.payroll.index')" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        <div class="font-semibold">View Payroll</div>
                    </Link>
                    <Link :href="route('employee.documents.index')" class="bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="font-semibold">My Documents</div>
                    </Link>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Today's Attendance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Attendance</h3>
                            <div v-if="todayAttendance" class="space-y-3">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ formatDate(todayAttendance.date) }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ todayAttendance.shift === 'full-time' ? 'Full Time' : todayAttendance.shift }} shift
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">
                                            In: {{ formatTime(todayAttendance.check_in_time) }}
                                        </div>
                                        <div class="text-sm text-gray-900">
                                            Out: {{ formatTime(todayAttendance.check_out_time) }}
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getLeaveStatusColor(todayAttendance.status)">
                                           {{ todayAttendance.status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <Link v-if="!todayAttendance.check_in_time" :href="route('employee.attendance.checkin')" class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition text-sm">
                                        Check In
                                    </Link>
                                    <Link v-if="todayAttendance.check_in_time && !todayAttendance.check_out_time" :href="route('employee.attendance.checkout')" class="flex-1 bg-red-600 text-white text-center py-2 px-4 rounded-md hover:bg-red-700 transition text-sm">
                                        Check Out
                                    </Link>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No attendance record for today</p>
                                <Link :href="route('employee.attendance.checkin')" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                                    Check In Now
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Leave Requests -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Recent Leave Requests</h3>
                                <Link :href="route('employee.leave.index')" class="text-sm text-blue-600 hover:text-blue-900">View all</Link>
                            </div>
                            <div class="space-y-3">
                                <div v-for="leave in recentLeaves" :key="leave.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                           <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                               {{ leave.leave_type }}
                                           </span>
                                            <span class="text-sm font-medium text-gray-900">{{ leave.days_requested }} days</span>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}</div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getLeaveStatusColor(leave.overall_status)">
                                       {{ leave.overall_status }}
                                   </span>
                                </div>
                                <div v-if="recentLeaves.length === 0" class="text-center py-4 text-gray-500">
                                    No leave requests yet
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Attendance Summary -->


                </div>

                <!-- Bottom Section -->
                <div class="mt-6">
                    <!-- Recent Payroll -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Recent Payroll</h3>
                                <Link :href="route('employee.payroll.index')" class="text-sm text-blue-600 hover:text-blue-900">View all</Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month/Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Salary</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="payroll in recentPayroll" :key="payroll.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payroll.month }}/{{ payroll.year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ payroll.gross_salary }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ payroll.net_salary }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                           <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="payroll.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                               {{ payroll.status }}
                                           </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('employee.payroll.show', payroll.id)" class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="recentPayroll.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No payroll records available
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>