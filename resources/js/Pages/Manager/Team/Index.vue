<!-- resources/js/Pages/Manager/Team/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        employees: Object,
        departments: Array,
        locations: Array,
        statuses: Array,
        filters: Object,
    });

    // Set up reactive state
    const search = ref(props.filters.search || '');
    const department = ref(props.filters.department || '');
    const location = ref(props.filters.location || '');
    const status = ref(props.filters.status || '');
    const sortField = ref(props.filters.sort_field || 'created_at');
    const sortDirection = ref(props.filters.sort_direction || 'desc');
    const perPage = ref(props.filters.per_page || 15);

    // Watch for changes to filter state and update the URL
    watch([search, department, location, status, sortField, sortDirection, perPage], () => {
        router.get(route('manager.team'), {
            search: search.value,
            department: department.value,
            location: location.value,
            status: status.value,
            sort_field: sortField.value,
            sort_direction: sortDirection.value,
            per_page: perPage.value
        }, {
            preserveState: true,
            replace: true,
        });
    }, { deep: true });

    // Handle sorting
    const sort = (field) => {
        if (sortField.value === field) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortField.value = field;
            sortDirection.value = 'asc';
        }
    };

    // Reset filters
    const resetFilters = () => {
        search.value = '';
        department.value = '';
        location.value = '';
        status.value = '';
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

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const getRoleColor = (role) => {
        switch (role) {
            case 'admin':
                return 'bg-purple-100 text-purple-800';
            case 'reporting_manager':
                return 'bg-blue-100 text-blue-800';
            case 'employee':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getRoleLabel = (role) => {
        switch (role) {
            case 'admin':
                return 'Admin';
            case 'reporting_manager':
                return 'Manager';
            case 'employee':
                return 'Employee';
            default:
                return role;
        }
    };
</script>

<template>
    <Head title="My Team" />

    <ManagerLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Team</h2>
                <div class="text-sm text-gray-600">
                    Total Team Members: {{ employees.data ? employees.data.length : employees.length }}
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters & Search -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Search -->
                            <div class="w-full md:w-1/3">
                                <SearchInput v-model="search" placeholder="Search team members..." />
                            </div>

                            <!-- Filters -->
                            <div class="flex flex-wrap gap-4 items-center">
                                <FilterDropdown v-model="department" label="Department" :options="departments" option-value="id" option-label="name" allow-empty />
                                <FilterDropdown v-model="location" label="Location" :options="locations" option-value="id" option-label="name" allow-empty />
                                <FilterDropdown v-model="status" label="Status" :options="statuses" allow-empty />
                                <FilterDropdown v-model="perPage" label="Per Page" :options="[15, 25, 50, 100]" />

                                <button @click="resetFilters" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none transition">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('employee_name')">
                                        <div class="flex items-center">
                                            Name
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" :class="{ 'transform rotate-180': sortField === 'employee_name' && sortDirection === 'desc' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="sortField === 'employee_name'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="employee in (employees.data || employees)" :key="employee.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ employee.title }} {{ employee.employee_name }}</div>
                                                <div class="text-sm text-gray-500">{{ employee.email_address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ employee.department?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ employee.designation?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ employee.location?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(employee.status)">
                                            {{ employee.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getRoleColor(employee.user?.role)">
                                            {{ getRoleLabel(employee.user?.role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link :href="route('manager.team.show', employee.id)" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
<!--                                            <Link :href="route('manager.attendance.show', employee.id)" class="text-green-600 hover:text-green-900" title="View Attendance">-->
<!--                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />-->
<!--                                                </svg>-->
<!--                                            </Link>-->
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="(!employees.data && employees.length === 0) || (employees.data && employees.data.length === 0)">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No team members found. Try adjusting your search criteria.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (if using paginated data) -->
                        <div class="mt-6" v-if="employees.links">
                            <Pagination :links="employees.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>