<!-- resources/js/Pages/Manager/Dashboard.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';

    defineProps({
        stats: Object,
        teamMembers: Array,
        pendingLeaves: Array,
        todayAttendance: Array,
        recentActivities: Object,
        managedLocation: Object,
        managerEmployee: Object,
    });

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-yellow-100 text-yellow-800';
            case 'terminated':
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
    <Head title="Manager Dashboard" />

    <ManagerLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manager Dashboard</h2>
                    <p class="text-sm text-gray-600 mt-1" v-if="managedLocation">Managing {{ managedLocation.name }}</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('manager.team')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        View Team
                    </Link>
                </div>
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
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Team Members</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_team_members }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Members</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.active_team_members }}</dd>
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Leaves</dt>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Overtime Requests</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.overtime_requests }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <Link :href="route('manager.leave.index')" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <div class="font-semibold">Review Leaves</div>
                    </Link>

                    <Link :href="route('manager.team')" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div class="font-semibold">Manage Team</div>
                    </Link>

                    <Link :href="route('manager.attendance.index')" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="font-semibold">View Attendance</div>
                    </Link>

                    <Link :href="route('manager.overtime.index')" class="bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg p-4 text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="font-semibold">Request Overtime</div>
                    </Link>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Pending Leave Requests -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Pending Leave Requests</h3>
                                <Link :href="route('manager.leave.index')" class="text-sm text-blue-600 hover:text-blue-900">View all</Link>
                            </div>
                            <div class="space-y-3">
                                <div v-for="leave in pendingLeaves" :key="leave.id" class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ leave.employee.user.name }}</div>
                                        <div class="text-sm text-gray-500">
                                           <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getLeaveTypeColor(leave.leave_type)">
                                               {{ leave.leave_type }}
                                           </span>
                                            - {{ leave.days_requested }} days
                                        </div>
                                        <div class="text-xs text-gray-400">{{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}</div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <Link :href="route('manager.leave.index')" class="text-blue-600 hover:text-blue-900 text-sm">Review</Link>
                                    </div>
                                </div>
                                <div v-if="pendingLeaves.length === 0" class="text-center py-4 text-gray-500">
                                    No pending leave requests
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Team Attendance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Today's Attendance</h3>
                                <Link :href="route('manager.attendance.index')" class="text-sm text-blue-600 hover:text-blue-900">View all</Link>
                            </div>
                            <div class="space-y-3">
                                <div v-for="attendance in todayAttendance" :key="attendance.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                               <span class="text-white text-xs font-semibold">
                                                   {{ attendance.employee.user.name.split(' ').map(n => n[0]).join('') }}
                                               </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ attendance.employee.user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ attendance.shift }} shift</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">{{ attendance.check_in_time || 'Not checked in' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="attendance.status === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                           {{ attendance.status }}
                                       </span>
                                    </div>
                                </div>
                                <div v-if="todayAttendance.length === 0" class="text-center py-4 text-gray-500">
                                    No attendance records for today
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Overview -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Team Overview</h3>
                                <Link :href="route('manager.team')" class="text-sm text-blue-600 hover:text-blue-900">View team</Link>
                            </div>
                            <div class="space-y-3">
                                <div v-for="member in teamMembers.slice(0, 5)" :key="member.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                               <span class="text-sm font-medium text-gray-700">
                                                   {{ member.employee_name.split(' ').map(n => n[0]).join('') }}
                                               </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ member.employee_name }}</div>
                                            <div class="text-sm text-gray-500">{{ member.designation.name }} - {{ member.facility.name }}</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(member.status)">
                                       {{ member.status }}
                                   </span>
                                </div>
                                <div v-if="teamMembers.length === 0" class="text-center py-4 text-gray-500">
                                    No team members assigned
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activities</h3>
                            <div class="space-y-4">
                                <!-- New Team Members -->
                                <div v-if="recentActivities.new_team_members && recentActivities.new_team_members.length > 0">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">New Team Members (Last 7 days)</h4>
                                    <div class="space-y-2">
                                        <div v-for="member in recentActivities.new_team_members" :key="member.id" class="text-sm text-gray-600">
                                            <span class="font-medium">{{ member.employee_name }}</span> joined as {{ member.designation.name }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Leave Requests -->
                                <div v-if="recentActivities.recent_leave_requests && recentActivities.recent_leave_requests.length > 0">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Leave Requests</h4>
                                    <div class="space-y-2">
                                        <div v-for="leave in recentActivities.recent_leave_requests" :key="leave.id" class="text-sm text-gray-600">
                                            <span class="font-medium">{{ leave.employee.user.name }}</span> requested {{ leave.leave_type }} leave
                                            <span class="text-xs text-gray-400">({{ formatDate(leave.created_at) }})</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="(!recentActivities.new_team_members || recentActivities.new_team_members.length === 0) && (!recentActivities.recent_leave_requests || recentActivities.recent_leave_requests.length === 0)" class="text-center py-4 text-gray-500">
                                    No recent activities
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>