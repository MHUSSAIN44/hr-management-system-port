<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

// Props containing real data from the backend
const props = defineProps({
    employeeData: Object,
    attendance: Object,
    recentActivity: Array,
    upcomingLeaves: Array,
});

// Handle check-in action
const checkIn = () => {
    router.post(route('employee.attendance.check-in'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the page to show updated data
            router.reload();
        }
    });
};

// Handle check-out action
const checkOut = () => {
    router.post(route('employee.attendance.check-out'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the page to show updated data
            router.reload();
        }
    });
};
</script>

<template>
    <Head title="Employee Dashboard" />

    <EmployeeLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Employee Profile Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="h-20 w-20 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-2xl font-bold">
                                {{ employeeData.name.split(' ').map(n => n[0]).join('') }}
                            </div>
                            <div class="ml-6">
                                <h3 class="text-xl font-medium text-gray-900">{{ employeeData.name }}</h3>
                                <p class="text-sm text-gray-500">{{ employeeData.role }} • {{ employeeData.department }}</p>
                                <p class="text-sm text-gray-500">Employee since {{ new Date(employeeData.joinDate).toLocaleDateString() }}</p>
                            </div>
                            <div class="ml-auto">
                                <Link :href="route('employee.profile')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                                    View Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Attendance Today -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Attendance</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="text-lg font-semibold capitalize" :class="{
                                        'text-success-600': attendance.today.status === 'present',
                                        'text-danger-600': attendance.today.status === 'absent',
                                        'text-warning-600': attendance.today.status === 'leave',
                                        'text-gray-600': attendance.today.status === 'not_recorded',
                                    }">
                                        {{ attendance.today.status === 'not_recorded' ? 'Not Recorded' : attendance.today.status }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Check-in</p>
                                    <p class="text-lg font-semibold">{{ attendance.today.checkIn || 'Not checked in' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Check-out</p>
                                    <p class="text-lg font-semibold">{{ attendance.today.checkOut || 'Not checked out' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-4">
                                <button
                                    @click="checkIn"
                                    class="inline-flex items-center px-4 py-2 bg-success-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-success-700"
                                    :disabled="attendance.today.checkIn !== null">
                                    Check In
                                </button>
                                <button
                                    @click="checkOut"
                                    class="inline-flex items-center px-4 py-2 bg-danger-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-danger-700"
                                    :disabled="attendance.today.checkIn === null || attendance.today.checkOut !== null">
                                    Check Out
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Balance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Balance</h3>
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 rounded-full bg-warning-100 flex items-center justify-center text-warning-600 text-2xl font-bold">
                                    {{ employeeData.leaveBalance }}
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Days Remaining</p>
                                    <p class="text-lg font-semibold">Annual Leave</p>
                                </div>
                            </div>
                            <Link :href="route('employee.leave.create')" class="inline-flex items-center px-4 py-2 bg-warning-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-warning-700">
                                Apply for Leave
                            </Link>
                        </div>
                    </div>

                    <!-- Monthly Attendance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ employeeData.currentMonth }} Attendance
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">Present</span>
                                        <span class="text-sm font-medium text-gray-700">{{ attendance.thisMonth.present }} days</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-success-600 h-2.5 rounded-full" :style="{ width: `${(attendance.thisMonth.present / attendance.thisMonth.total) * 100}%` }"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">Absent</span>
                                        <span class="text-sm font-medium text-gray-700">{{ attendance.thisMonth.absent }} days</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-danger-600 h-2.5 rounded-full" :style="{ width: `${(attendance.thisMonth.absent / attendance.thisMonth.total) * 100}%` }"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">Leave</span>
                                        <span class="text-sm font-medium text-gray-700">{{ attendance.thisMonth.leave }} days</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-warning-600 h-2.5 rounded-full" :style="{ width: `${(attendance.thisMonth.leave / attendance.thisMonth.total) * 100}%` }"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('employee.attendance.index')" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    View complete attendance →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming & Activity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recent Activity -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                            <ul v-if="recentActivity.length > 0" class="divide-y divide-gray-200">
                                <li v-for="activity in recentActivity" :key="activity.id" class="py-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                            <svg v-if="activity.type === 'attendance'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            <svg v-else-if="activity.type === 'leave'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ activity.action }}</p>
                                            <p class="text-sm text-gray-500">{{ activity.time }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div v-else class="py-4 text-center text-gray-500">
                                No recent activity
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Leaves -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Leaves</h3>
                            <div v-if="upcomingLeaves.length === 0" class="py-4 text-center text-gray-500">
                                No upcoming leaves scheduled
                            </div>
                            <ul v-else class="divide-y divide-gray-200">
                                <li v-for="leave in upcomingLeaves" :key="leave.id" class="py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-md bg-warning-100 flex items-center justify-center text-warning-600">
                                                {{ new Date(leave.date).getDate() }}
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ new Date(leave.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{ leave.reason }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="px-2 py-1 text-xs rounded-full" :class="{
                                                'bg-success-100 text-success-800': leave.status === 'Approved',
                                                'bg-warning-100 text-warning-800': leave.status === 'Pending',
                                                'bg-danger-100 text-danger-800': leave.status === 'Rejected',
                                            }">
                                                {{ leave.status }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="mt-4">
                                <Link :href="route('employee.leave.index')" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    View all leave history →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>
