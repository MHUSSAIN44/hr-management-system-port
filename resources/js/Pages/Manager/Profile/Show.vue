<!-- resources/js/Pages/Manager/Profile/Show.vue -->
<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';
    import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';

    defineProps({
        employee: Object,
    });

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
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

    const getRoleColor = (role) => {
        switch (role) {
            case 'admin':
                return 'bg-purple-100 text-purple-800';
            case 'reporting_manager':
                return 'bg-green-100 text-green-800';
            case 'employee':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getRoleLabel = (role) => {
        switch (role) {
            case 'admin':
                return 'Admin';
            case 'reporting_manager':
                return 'Reporting Manager';
            case 'employee':
                return 'Employee';
            default:
                return role || 'Employee';
        }
    };
</script>

<template>
    <Head :title="`Manager Profile - ${employee?.employee_name}`" />

    <ManagerLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Manager Profile</h2>
                <div class="flex space-x-3">
<!--                    <Link :href="route('manager.profile.edit')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring focus:ring-green-200 transition">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />-->
<!--                        </svg>-->
<!--                        Edit Profile-->
<!--                    </Link>-->
                    <Link :href="route('manager.dashboard')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring focus:ring-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Manager Profile Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center">
                            <!-- Profile Photo -->
                            <div class="mr-6 mb-4 md:mb-0">
                                <div class="h-32 w-32 rounded-full overflow-hidden bg-green-100 flex items-center justify-center border-4 border-green-200">
                                    <span class="text-green-600 text-2xl font-bold">
                                        {{ employee?.employee_name?.split(' ').map(n => n[0]).join('') || 'M' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="flex-grow mb-4 md:mb-0">
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ employee?.title }} {{ employee?.employee_name }}
                                </h2>
                                <p class="text-lg text-green-600 font-semibold">{{ employee?.designation?.name }}</p>
                                <div class="mt-1 flex items-center flex-wrap">
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Department:</span>
                                        <span class="ml-1 text-sm font-medium text-gray-900">{{ employee?.department?.name }}</span>
                                    </div>
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Facility:</span>
                                        <span class="ml-1 text-sm font-medium text-gray-900">{{ employee?.facility?.name }}</span>
                                    </div>
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(employee?.status)">
                                            {{ employee?.status }}
                                        </span>
                                    </div>
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Role:</span>
                                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getRoleColor(employee?.user?.role)">
                                            {{ getRoleLabel(employee?.user?.role) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div>
                                        <span class="text-sm text-gray-500">Email:</span>
                                        <a :href="`mailto:${employee?.email_address}`" class="ml-1 text-sm text-green-600 hover:text-green-900">{{ employee?.email_address }}</a>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Contact:</span>
                                        <a :href="`tel:${employee?.contact}`" class="ml-1 text-sm text-green-600 hover:text-green-900">{{ employee?.contact }}</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="flex flex-wrap md:flex-col justify-end gap-4">
                                <div class="bg-green-50 px-4 py-2 rounded-md border border-green-200">
                                    <p class="text-sm text-green-600">Location</p>
                                    <p class="text-lg font-semibold text-green-900">{{ employee?.location?.name }}</p>
                                </div>
                                <div class="bg-blue-50 px-4 py-2 rounded-md border border-blue-200">
                                    <p class="text-sm text-blue-600">Annual Leave</p>
                                    <p class="text-lg font-semibold text-blue-900">{{ employee?.annual_leave_balance || 0 }} days</p>
                                </div>
                                <div class="bg-purple-50 px-4 py-2 rounded-md border border-purple-200">
                                    <p class="text-sm text-purple-600">Medical Leave</p>
                                    <p class="text-lg font-semibold text-purple-900">{{ employee?.medical_leave_balance || 0 }} days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Personal Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee?.date_of_birth) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.nationality || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.contact || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.whatsapp_number || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">COVID Vaccinated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="employee?.covid_vaccinated ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ employee?.covid_vaccinated ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Management Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Management Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.department?.name || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Designation</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.designation?.name || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Facility</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.facility?.name || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.location?.name || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Management Role</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Reporting Manager
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">First Paid Working Day</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee?.company_first_paid_working_day) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Passport Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Passport Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Passport Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.passport_number || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Passport Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee?.passport_expiry) }}</dd>
                                </div>
                                <div v-if="employee?.passport_file" class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Passport File</dt>
                                    <dd class="mt-1 text-sm">
                                        <a :href="`/storage/${employee.passport_file}`" target="_blank" class="text-green-600 hover:text-green-900 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            View Passport Document
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Visa Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Visa Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Visa Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.visa_number || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Visa Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee?.visa_start_date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Visa Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee?.visa_expiry_date) }}</dd>
                                </div>
                                <div v-if="employee?.visa_file">
                                    <dt class="text-sm font-medium text-gray-500">Visa File</dt>
                                    <dd class="mt-1 text-sm">
                                        <a :href="`/storage/${employee.visa_file}`" target="_blank" class="text-green-600 hover:text-green-900 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            View Visa Document
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div v-if="employee?.driving_license_expiry">
                                    <dt class="text-sm font-medium text-gray-500">Driving License Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.driving_license_expiry) }}</dd>
                                </div>
                                <div v-if="employee?.eid_number">
                                    <dt class="text-sm font-medium text-gray-500">EID Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.eid_number }}</dd>
                                </div>
                                <div v-if="employee?.eid_expiry_date">
                                    <dt class="text-sm font-medium text-gray-500">EID Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.eid_expiry_date) }}</dd>
                                </div>
                                <div v-if="employee?.insurance_issued_on">
                                    <dt class="text-sm font-medium text-gray-500">Insurance Issued</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.insurance_issued_on) }}</dd>
                                </div>
                                <div v-if="employee?.insurance_expiry_date">
                                    <dt class="text-sm font-medium text-gray-500">Insurance Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.insurance_expiry_date) }}</dd>
                                </div>
                                <div v-if="employee?.malpractice_expiry_date">
                                    <dt class="text-sm font-medium text-gray-500">Malpractice Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.malpractice_expiry_date) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Leave Balance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Manager Leave Balance
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Annual Leave Balance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.annual_leave_balance || 0 }} days</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Medical Leave Balance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee?.medical_leave_balance || 0 }} days</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Recent Leave Requests Section -->
                <div class="mt-6" v-if="employee?.leaveRequests?.length > 0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                My Recent Leave Requests
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="leave in employee.leaveRequests" :key="leave.id" class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900 capitalize">{{ leave.leave_type }}</span>
                                                <span class="ml-2 text-xs text-gray-500">{{ leave.days_requested }} days</span>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-800': leave.overall_status === 'pending',
                                                      'bg-green-100 text-green-800': leave.overall_status === 'approved',
                                                      'bg-red-100 text-red-800': leave.overall_status === 'rejected'
                                                  }">
                                                {{ leave.overall_status }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1" v-if="leave.reason">
                                            {{ leave.reason }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents and Assets -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6" v-if="employee?.documents?.length > 0 || employee?.assets?.length > 0">
                    <!-- Documents -->
                    <div v-if="employee?.documents?.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                My Documents
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="document in employee.documents" :key="document.id" class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ document.title }}</div>
                                        <div class="text-sm text-gray-500">Expires: {{ formatDate(document.expiry_date) }}</div>
                                    </div>
                                    <a :href="`/storage/${document.file_path}`" target="_blank" class="text-green-600 hover:text-green-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assets -->
                    <div v-if="employee?.assets?.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-green-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                My Assigned Assets
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="asset in employee.assets" :key="asset.id" class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ asset.asset_name }}</div>
                                        <div class="text-sm text-gray-500">{{ asset.asset_type }} - {{ asset.serial_number }}</div>
                                        <div class="text-xs text-gray-400">Assigned: {{ formatDate(asset.assigned_date) }}</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                      {{ asset.status }}
                                  </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 mt-3">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

            </div>
        </div>
    </ManagerLayout>
</template>