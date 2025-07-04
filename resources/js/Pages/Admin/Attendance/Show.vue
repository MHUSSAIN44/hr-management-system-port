// resources/js/Pages/Admin/Attendance/Show.vue
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';

    const props = defineProps({
        attendance: Object,
    });

    const formatDate = (dateString) => {
        return dateString ? new Date(dateString).toLocaleDateString() : 'N/A';
    };

    const formatTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
    };

    const formatDateTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleString() : 'N/A';
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'present':
                return 'bg-green-100 text-green-800';
            case 'absent':
                return 'bg-red-100 text-red-800';
            case 'late':
                return 'bg-yellow-100 text-yellow-800';
            case 'half_day':
                return 'bg-orange-100 text-orange-800';
            case 'checked_in':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getShiftColor = (shift) => {
        switch (shift) {
            case 'morning':
                return 'bg-blue-100 text-blue-800';
            case 'evening':
                return 'bg-purple-100 text-purple-800';
            case 'full-time':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getLocationAccuracy = (accuracy) => {
        if (!accuracy) return 'Unknown';
        if (accuracy <= 10) return 'Excellent';
        if (accuracy <= 50) return 'Good';
        if (accuracy <= 100) return 'Fair';
        return 'Poor';
    };

    const getAccuracyColor = (accuracy) => {
        if (!accuracy) return 'bg-gray-100 text-gray-800';
        if (accuracy <= 10) return 'bg-green-100 text-green-800';
        if (accuracy <= 50) return 'bg-blue-100 text-blue-800';
        if (accuracy <= 100) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    };
</script>

<template>
    <Head :title="`Attendance - ${attendance.employee.employee_name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance Details</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.attendance.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Attendance
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Employee & Attendance Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="mr-6">
                                    <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-500 text-xl font-semibold">
                                            {{ attendance.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ attendance.employee.employee_name }}</h2>
                                    <p class="text-lg text-gray-600">{{ attendance.employee.designation?.name }}</p>
                                    <div class="mt-1 flex items-center flex-wrap space-x-4">
                                        <div>
                                            <span class="text-sm text-gray-500">Department:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ attendance.employee.department?.name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Location:</span>
                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ attendance.employee.location?.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-lg font-medium" :class="getStatusColor(attendance.status)">
                                        {{ attendance.status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ formatDate(attendance.date) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Time Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-blue-800">Time Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(attendance.date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Shift</dt>
                                    <dd class="mt-1">
                                        <span v-if="attendance.shift" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getShiftColor(attendance.shift)">
                                            {{ attendance.shift === 'full-time' ? 'Full Time' : attendance.shift }}
                                        </span>
                                        <span v-else class="text-sm text-gray-500">Not specified</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Check In Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatTime(attendance.check_in_time) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Check Out Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatTime(attendance.check_out_time) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Hours</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ attendance.total_hours ? attendance.total_hours + ' hours' : 'Not calculated' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(attendance.status)">
                                            {{ attendance.status }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800">Location Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <!-- Check In Location -->
                                <div v-if="attendance.check_in_address">
                                    <dt class="text-sm font-medium text-gray-500">Check In Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ attendance.check_in_address }}</dd>
                                    <div class="mt-1 text-xs text-gray-500">
                                        <div>Coordinates: {{ attendance.check_in_latitude }}, {{ attendance.check_in_longitude }}</div>
                                        <div>Accuracy:
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getAccuracyColor(attendance.check_in_accuracy)">
                                                {{ getLocationAccuracy(attendance.check_in_accuracy) }}
                                            </span>
                                            (±{{ Math.round(attendance.check_in_accuracy || 0) }}m)
                                        </div>
                                    </div>
                                </div>

                                <!-- Check Out Location -->
                                <div v-if="attendance.check_out_address">
                                    <dt class="text-sm font-medium text-gray-500">Check Out Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ attendance.check_out_address }}</dd>
                                    <div class="mt-1 text-xs text-gray-500">
                                        <div>Coordinates: {{ attendance.check_out_latitude }}, {{ attendance.check_out_longitude }}</div>
                                        <div>Accuracy:
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getAccuracyColor(attendance.check_out_accuracy)">
                                                {{ getLocationAccuracy(attendance.check_out_accuracy) }}
                                            </span>
                                            (±{{ Math.round(attendance.check_out_accuracy || 0) }}m)
                                        </div>
                                    </div>
                                </div>

                                <!-- IP Information -->
                                <div v-if="attendance.check_in_ip">
                                    <dt class="text-sm font-medium text-gray-500">Check In IP</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ attendance.check_in_ip }}</dd>
                                </div>

                                <div v-if="attendance.check_out_ip">
                                    <dt class="text-sm font-medium text-gray-500">Check Out IP</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ attendance.check_out_ip }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div v-if="attendance.notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-yellow-800">Notes</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed">{{ attendance.notes }}</p>
                    </div>
                </div>

                <!-- Device Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Device Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div v-if="attendance.check_in_user_agent">
                                <dt class="text-sm font-medium text-gray-500">Check In Device</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ attendance.check_in_user_agent }}</dd>
                            </div>
                            <div v-if="attendance.check_out_user_agent">
                                <dt class="text-sm font-medium text-gray-500">Check Out Device</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ attendance.check_out_user_agent }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Record Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(attendance.created_at) }}</dd>
                            </div>
                            <div v-if="attendance.updated_at !== attendance.created_at">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(attendance.updated_at) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>