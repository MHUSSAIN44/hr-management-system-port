<!--// resources/js/Pages/Admin/Attendance/Index.vue-->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        attendance: Object,
        stats: Object,
        locations: Array,
        filters: Object,
    });

    // Search and filters
    const search = ref(props.filters.employee || '');
    const perPage = ref(props.filters.per_page || 15);
    const statusFilter = ref(props.filters.status || '');
    const shiftFilter = ref(props.filters.shift || '');
    const locationFilter = ref(props.filters.location_id || '');
    const departmentFilter = ref(props.filters.department_id || '');
    const dateFrom = ref(props.filters.date_from || '');
    const dateTo = ref(props.filters.date_to || '');
    const monthFilter = ref(props.filters.month || '');

    // Watch for URL changes
    watch([search, perPage, statusFilter, shiftFilter, locationFilter, departmentFilter, dateFrom, dateTo, monthFilter], () => {
        router.get(route('admin.attendance.index'), {
            employee: search.value,
            per_page: perPage.value,
            status: statusFilter.value,
            shift: shiftFilter.value,
            location_id: locationFilter.value,
            department_id: departmentFilter.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            month: monthFilter.value,
        }, {
            preserveState: true,
            replace: true,
        });
    });

    const resetFilters = () => {
        search.value = '';
        statusFilter.value = '';
        shiftFilter.value = '';
        locationFilter.value = '';
        departmentFilter.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        monthFilter.value = '';
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const formatTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
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
    <Head title="Attendance Management" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance Management</h2>
                <div class="flex space-x-3">
<!--                    <Link :href="route('admin.attendance.map')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />-->
<!--                        </svg>-->
<!--                        Map View-->
<!--                    </Link>-->
<!--                    <Link :href="route('admin.attendance.live-tracking')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />-->
<!--                        </svg>-->
<!--                        Live Tracking-->
<!--                    </Link>-->
<!--                    <Link :href="route('admin.attendance.report')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />-->
<!--                        </svg>-->
<!--                        Reports-->
<!--                    </Link>-->
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
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Records</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.total_records }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Present Today</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.present_today }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Absent Today</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.absent_today }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Late Today</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.late_today }}</p>
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

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Checked In Now</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.checked_in_now }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Attendance Records</h3>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                                <SearchInput v-model="search" placeholder="Search employee..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="checked_in">Checked In</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                                <select v-model="shiftFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Shifts</option>
                                    <option value="full-time">Full Time</option>
                                    <option value="morning">Morning</option>
                                    <option value="evening">Evening</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select v-model="locationFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Locations</option>
                                    <option v-for="location in locations" :key="location.id" :value="location.id">
                                        {{ location.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                <input v-model="monthFilter" type="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input v-model="dateFrom" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input v-model="dateTo" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                <select v-model="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 flex items-end">
                                <button @click="resetFilters" class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location Accuracy</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="record in attendance.data" :key="record.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                       <span class="text-white text-sm font-semibold">
                                                           {{ record.employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                                       </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ record.employee.employee_name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ record.employee.department?.name }} - {{ record.employee.location?.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(record.date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                           <span v-if="record.shift" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getShiftColor(record.shift)">
                                               {{ record.shift }}
                                           </span>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatTime(record.check_in_time) }}</div>
                                        <div v-if="record.check_in_address" class="text-xs text-gray-500 truncate max-w-xs" :title="record.check_in_address">
                                            📍 {{ record.check_in_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatTime(record.check_out_time) }}</div>
                                        <div v-if="record.check_out_address" class="text-xs text-gray-500 truncate max-w-xs" :title="record.check_out_address">
                                            📍 {{ record.check_out_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ record.total_hours ? record.total_hours + ' hrs' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getAccuracyColor(record.check_in_accuracy)">
                                               {{ getLocationAccuracy(record.check_in_accuracy) }}
                                           </span>
                                        <div v-if="record.check_in_accuracy" class="text-xs text-gray-500">
                                            ±{{ Math.round(record.check_in_accuracy) }}m
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(record.status)">
                                               {{ record.status }}
                                           </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link :href="route('admin.attendance.show', record.id)" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <button v-if="record.check_in_latitude && record.check_in_longitude" @click="openLocationModal(record)" class="text-green-600 hover:text-green-900" title="View on Map">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="attendance.data.length === 0">
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        No attendance records found.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="attendance.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>