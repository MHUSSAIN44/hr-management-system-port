<!-- resources/js/Pages/Admin/Departments/Show.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';

    defineProps({
        department: Object,
    });

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };
</script>

<template>
    <Head :title="`Department - ${department.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Department Details</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.departments.edit', department.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </Link>
                    <Link :href="route('admin.departments.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Department Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ department.name }}</h1>
                                <p class="text-gray-600" v-if="department.description">{{ department.description }}</p>
                                <p class="text-gray-500" v-else>No description provided</p>
                                <div class="mt-4 text-sm text-gray-500">
                                    <p>Created: {{ formatDate(department.created_at) }}</p>
                                    <p>Last Updated: {{ formatDate(department.updated_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-600">{{ department.employees?.length || 0 }}</div>
                                    <div class="text-sm text-gray-500">Total Employees</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees in Department -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="department.employees?.length > 0">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Employees in this Department</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="employee in department.employees" :key="employee.id" class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">
                                            {{ employee.employee_name.split(' ').map(n => n[0]).join('') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ employee.employee_name }}</div>
                                    <div class="text-sm text-gray-500">{{ employee.user.email }}</div>
                                </div>
                                <Link :href="route('admin.employees.show', employee.id)" class="text-blue-600 hover:text-blue-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Employees -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No employees</h3>
                        <p class="mt-1 text-sm text-gray-500">No employees have been assigned to this department yet.</p>
                        <div class="mt-6">
                            <Link :href="route('admin.employees.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Employee
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>