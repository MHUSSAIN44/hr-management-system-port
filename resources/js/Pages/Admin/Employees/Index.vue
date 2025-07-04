<!-- resources/js/Pages/Admin/Employees/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import DeleteModal from '@/Components/DeleteModal.vue';
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
    const showDeleteModal = ref(false);
    const employeeToDelete = ref(null);


    // Watch for changes to filter state and update the URL
    watch([search, department, location, status, sortField, sortDirection, perPage], () => {
        router.get(route('admin.employees.index'), {
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

    // Delete employee confirmation
    const confirmDelete = (employee) => {
        employeeToDelete.value = employee;
        showDeleteModal.value = true;
    };

    // Delete employee action
    const deleteEmployee = () => {
        if (employeeToDelete.value) {
            router.delete(route('admin.employees.destroy', employeeToDelete.value.id), {
                onSuccess: () => {
                    showDeleteModal.value = false;
                    employeeToDelete.value = null;
                }
            });
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


    const role = ref(props.filters.role || '');

    const handleRoleChange = () => {
        if (form.user_role === 'reporting_manager') {
            form.reporting_manager_id = '';
        }
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

    const promoteToManager = (employee) => {
        if (confirm(`Are you sure you want to promote ${employee.employee_name} to Reporting Manager?`)) {
            router.post(route('admin.employees.promote', employee.id));
        }
    };

    const demoteToEmployee = (employee) => {
        if (confirm(`Are you sure you want to demote ${employee.employee_name} to Employee?`)) {
            router.post(route('admin.employees.demote', employee.id));
        }
    };

</script>

<template>
    <Head title="Employees" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employees</h2>
                <Link :href="route('admin.employees.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Employee
                </Link>
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
                                <SearchInput v-model="search" placeholder="Search employees..." />
                            </div>

                            <!-- Filters -->
                            <div class="flex flex-wrap gap-4 items-center">
                                <FilterDropdown v-model="department" label="Department" :options="departments" option-value="id" option-label="name" allow-empty />
                                <FilterDropdown v-model="location" label="Location" :options="locations" option-value="id" option-label="name" allow-empty />
                                <FilterDropdown v-model="status" label="Status" :options="statuses" allow-empty />
<!--                                <FilterDropdown v-model="perPage" label="Per Page" :options="[15, 25, 50, 100]" />-->
                                <FilterDropdown v-model="role" label="Role" :options="roles" allow-empty />

                                <button @click="resetFilters" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none transition">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Table -->
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="employee in employees.data" :key="employee.id" class="hover:bg-gray-50">
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
                                        {{ employee.department.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ employee.designation.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ employee.location.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(employee.status)">
                                            {{ employee.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getRoleColor(employee.user.role)">
                                            {{ getRoleLabel(employee.user.role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link :href="route('admin.employees.show', employee.id)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link :href="route('admin.employees.edit', employee.id)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
<!--                                            <button-->
<!--                                                    v-if="employee.user.role === 'employee'"-->
<!--                                                    @click="promoteToManager(employee)"-->
<!--                                                    class="text-green-600 hover:text-green-900"-->
<!--                                                    title="Promote to Manager"-->
<!--                                            >-->
<!--                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />-->
<!--                                                </svg>-->
<!--                                            </button>-->

<!--                                            <button-->
<!--                                                    v-if="employee.user.role === 'reporting_manager'"-->
<!--                                                    @click="demoteToEmployee(employee)"-->
<!--                                                    class="text-orange-600 hover:text-orange-900"-->
<!--                                                    title="Demote to Employee"-->
<!--                                            >-->
<!--                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />-->
<!--                                                </svg>-->
<!--                                            </button>-->
                                            <button @click="confirmDelete(employee)" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="employees.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No employees found. Try adjusting your search criteria or <Link :href="route('admin.employees.create')" class="text-blue-600 hover:text-blue-900">add a new employee</Link>.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="employees.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DeleteModal
                v-if="showDeleteModal"
                @close="showDeleteModal = false"
                @confirm="deleteEmployee"
                :title="`Delete Employee: ${employeeToDelete?.employee_name}`"
                :message="`Are you sure you want to delete this employee? This action cannot be undone and will permanently remove all employee data including attendance and payroll records.`"
        />
    </AdminLayout>
</template>