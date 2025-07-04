<!-- resources/js/Pages/Employee/Profile/Show.vue -->

<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
    import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
    import {ref , computed } from 'vue';

    const props = defineProps({
        employee: Object,
    });

    // Helper function to format dates
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

    // Check if any documents are expiring soon
    const hasExpiringDocuments = computed(() => {
        const now = new Date();
        const thirtyDaysFromNow = new Date(Date.now() + 30*24*60*60*1000);

        return [
            props.employee?.eid_expiry_date,
            props.employee?.insurance_expiry_date,
            props.employee?.malpractice_expiry_date,
            props.employee?.driving_license_expiry,
            props.employee?.passport_expiry,
            props.employee?.visa_expiry_date
        ].some(date => date && new Date(date) < thirtyDaysFromNow);
    });

    const imageError = ref(false);

    // Simple array of professional dummy images
    const profileImages = [
        // 'https://cdn-icons-png.flaticon.com/512/3135/3135823.png',
        'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
    ];

    const getProfileImage = (employee) => {
        if (!employee?.id) return profileImages[0];
        return profileImages[employee.id % profileImages.length];
    };

    const handleImageError = () => {
        imageError.value = true;
    };

</script>

<template>
    <Head title="My Profile" />

    <EmployeeLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Profile</h2>
<!--                <Link :href="route('employee.profile.edit')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring focus:ring-blue-200 transition">-->
<!--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />-->
<!--                    </svg>-->
<!--                    Edit Profile-->
<!--                </Link>-->
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Employee Profile Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap md:flex-nowrap items-center">
                            <!-- Profile Photo -->
                            <!-- Profile Photo -->
                            <div class="mr-6 mb-4 md:mb-0">
                                <div class="relative h-32 w-32 rounded-full overflow-hidden bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg ring-4 ring-white">
                                    <!-- Professional placeholder image -->
                                    <img
                                            :src="getProfileImage(employee)"
                                            :alt="`${employee?.employee_name} profile photo`"
                                            class="h-full w-full object-cover"
                                            @error="handleImageError"
                                            v-if="!imageError"
                                    />

                                    <!-- Fallback to initials if image fails to load -->
                                    <div
                                            v-if="imageError"
                                            class="h-full w-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-blue-600"
                                    >
            <span class="text-white text-4xl font-bold tracking-wider">
                {{ employee?.employee_name?.split(' ').map(n => n[0]).join('').toUpperCase() || 'N/A' }}
            </span>
                                    </div>

                                    <!-- Online status indicator -->
                                    <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-2 border-white rounded-full flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="flex-grow mb-4 md:mb-0">
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ employee.title || '' }} {{ employee.employee_name || 'N/A' }}
                                </h2>
                                <p class="text-lg text-gray-600">{{ employee.designation?.name || 'N/A' }}</p>
                                <div class="mt-1 flex items-center flex-wrap">
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Department:</span>
                                        <span class="ml-1 text-sm font-medium text-gray-900">{{ employee.department?.name || 'N/A' }}</span>
                                    </div>
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Facility:</span>
                                        <span class="ml-1 text-sm font-medium text-gray-900">{{ employee.facility?.name || 'N/A' }}</span>
                                    </div>
                                    <div class="mr-4 py-1">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(employee.status)">
                                            {{ employee.status || 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div v-if="employee.email_address">
                                        <span class="text-sm text-gray-500">Email:</span>
                                        <a :href="`mailto:${employee.email_address}`" class="ml-1 text-sm text-blue-600 hover:text-blue-900">{{ employee.email_address }}</a>
                                    </div>
                                    <div v-if="employee.contact">
                                        <span class="text-sm text-gray-500">Contact:</span>
                                        <a :href="`tel:${employee.contact}`" class="ml-1 text-sm text-blue-600 hover:text-blue-900">{{ employee.contact }}</a>
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

                <!-- Document Expiry Alert -->
                <div v-if="hasExpiringDocuments" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Document Renewal Required</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Some of your documents are expiring soon. Please contact HR to update them.</p>
                                <ul class="list-disc pl-5 mt-2 space-y-1">
                                    <li v-if="employee.eid_expiry_date && new Date(employee.eid_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)">
                                        EID expires on {{ formatDate(employee.eid_expiry_date) }}
                                    </li>
                                    <li v-if="employee.insurance_expiry_date && new Date(employee.insurance_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)">
                                        Insurance expires on {{ formatDate(employee.insurance_expiry_date) }}
                                    </li>
                                    <li v-if="employee.malpractice_expiry_date && new Date(employee.malpractice_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)">
                                        Malpractice insurance expires on {{ formatDate(employee.malpractice_expiry_date) }}
                                    </li>
                                    <li v-if="employee.driving_license_expiry && new Date(employee.driving_license_expiry) < new Date(Date.now() + 30*24*60*60*1000)">
                                        Driving license expires on {{ formatDate(employee.driving_license_expiry) }}
                                    </li>
                                    <li v-if="employee.passport_expiry && new Date(employee.passport_expiry) < new Date(Date.now() + 30*24*60*60*1000)">
                                        Passport expires on {{ formatDate(employee.passport_expiry) }}
                                    </li>
                                    <li v-if="employee.visa_expiry_date && new Date(employee.visa_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)">
                                        Visa expires on {{ formatDate(employee.visa_expiry_date) }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.date_of_birth) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.nationality || 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.contact || 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.whatsapp_number || 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">COVID Vaccinated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="employee.covid_vaccinated ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ employee.covid_vaccinated ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Employment Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.department?.name || 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Designation</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.designation?.name || 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Facility</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.facility?.name || 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.location?.name || 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reporting Manager</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.reporting_manager?.name || 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">First Paid Working Day</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.company_first_paid_working_day) }}</dd>
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
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.passport_number || 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Passport Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.passport_expiry">
                                            {{ formatDate(employee.passport_expiry) }}
                                            <span v-if="new Date(employee.passport_expiry) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.passport_expiry) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>
                                <div v-if="employee.passport_file" class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Passport File</dt>
                                    <dd class="mt-1 text-sm">
                                        <a :href="`/storage/${employee.passport_file}`" target="_blank" class="text-blue-600 hover:text-blue-900">
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
                                    <dd class="mt-1 text-sm text-gray-900">{{ employee.visa_number || 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Visa Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(employee.visa_start_date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Visa Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.visa_expiry_date">
                                            {{ formatDate(employee.visa_expiry_date) }}
                                            <span v-if="new Date(employee.visa_expiry_date) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.visa_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>
                                <div v-if="employee.visa_file">
                                    <dt class="text-sm font-medium text-gray-500">Visa File</dt>
                                    <dd class="mt-1 text-sm">
                                        <a :href="`/storage/${employee.visa_file}`" target="_blank" class="text-blue-600 hover:text-blue-900">
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
                                <!-- Driving License Expiry -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Driving License Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.driving_license_expiry">
                                            {{ formatDate(employee.driving_license_expiry) }}
                                            <span v-if="new Date(employee.driving_license_expiry) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.driving_license_expiry) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- EID Number -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">EID Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.eid_number">{{ employee.eid_number }}</span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- EID Expiry -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">EID Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.eid_expiry_date">
                                            {{ formatDate(employee.eid_expiry_date) }}
                                            <span v-if="new Date(employee.eid_expiry_date) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.eid_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- Insurance Issued On -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Insurance Issued Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.insurance_issued_on">{{ formatDate(employee.insurance_issued_on) }}</span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- Insurance Expiry -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Insurance Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.insurance_expiry_date">
                                            {{ formatDate(employee.insurance_expiry_date) }}
                                            <span v-if="new Date(employee.insurance_expiry_date) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.insurance_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- Malpractice Expiry -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Malpractice Insurance Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.malpractice_expiry_date">
                                            {{ formatDate(employee.malpractice_expiry_date) }}
                                            <span v-if="new Date(employee.malpractice_expiry_date) < new Date()" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expired
                                            </span>
                                            <span v-else-if="new Date(employee.malpractice_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Expiring Soon
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Not provided</span>
                                    </dd>
                                </div>

                                <!-- Last Working Day -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Working Day</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span v-if="employee.last_working_day">
                                            {{ formatDate(employee.last_working_day) }}
                                            <span v-if="employee.status === 'terminated'" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Terminated
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400 italic">Active Employee</span>
                                    </dd>
                                </div>

                                <!-- Medical Leave Balance -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Medical Leave Balance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="font-medium">{{ employee.medical_leave_balance || 0 }} days</span>
                                        <span v-if="employee.medical_leave_balance < 5" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            Low Balance
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Leave Balance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Leave Balance</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Annual Leave Balance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="font-medium">{{ employee.annual_leave_balance || 0 }} days</span>
                                        <span v-if="employee.annual_leave_balance < 5" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            Low Balance
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Medical Leave Balance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="font-medium">{{ employee.medical_leave_balance || 0 }} days</span>
                                        <span v-if="employee.medical_leave_balance < 5" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Low Balance
                                       </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="mt-6">
                <div v-if="employee?.documents?.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Documents</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 gap-4">
                            <div v-for="document in employee.documents" :key="document.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ document.title }}</div>
                                    <div class="text-sm text-gray-500">Expires: {{ formatDate(document.expiry_date) }}</div>
                                </div>
                                <a :href="`/storage/${document.file_path}`" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Assets Section -->
                <div v-if="employee.assets?.length > 0" class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">My Assigned Assets</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="asset in employee.assets" :key="asset.id" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-purple-500 rounded-full flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ asset.asset_name }}</h4>
                                                    <p class="text-xs text-gray-500">{{ asset.asset_type }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-600">Serial: {{ asset.serial_number }}</p>
                                                <p class="text-xs text-gray-500">Assigned: {{ formatDate(asset.assigned_date) }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                           {{ asset.status }}
                                       </span>
                                    </div>
                                </div>
                            </div>

                            <!-- No Assets Message -->
                            <div v-if="employee.assets?.length === 0" class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No Assets Assigned</h3>
                                <p class="mt-1 text-sm text-gray-500">You don't have any company assets assigned to you.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Reminders Section -->
                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Important Reminders</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Critical Document Expiry Reminders (30 days) -->
                                <div v-if="employee.passport_expiry && new Date(employee.passport_expiry) < new Date(Date.now() + 30*24*60*60*1000)" class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-red-800">Urgent: Passport Renewal Required</p>
                                        <p class="text-xs text-red-600">Your passport expires on {{ formatDate(employee.passport_expiry) }}. Please contact HR immediately.</p>
                                    </div>
                                </div>

                                <div v-if="employee.visa_expiry_date && new Date(employee.visa_expiry_date) < new Date(Date.now() + 30*24*60*60*1000)" class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-red-800">Critical: Visa Renewal Required</p>
                                        <p class="text-xs text-red-600">Your visa expires on {{ formatDate(employee.visa_expiry_date) }}. Contact HR immediately to avoid legal issues.</p>
                                    </div>
                                </div>

                                <!-- Warning Document Expiry Reminders (60 days) -->
                                <div v-if="employee.eid_expiry_date && new Date(employee.eid_expiry_date) < new Date(Date.now() + 60*24*60*60*1000)" class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-yellow-800">EID Renewal Reminder</p>
                                        <p class="text-xs text-yellow-600">Your Emirates ID expires on {{ formatDate(employee.eid_expiry_date) }}. Please plan for renewal.</p>
                                    </div>
                                </div>

                                <div v-if="employee.driving_license_expiry && new Date(employee.driving_license_expiry) < new Date(Date.now() + 60*24*60*60*1000)" class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-yellow-800">Driving License Renewal</p>
                                        <p class="text-xs text-yellow-600">Your driving license expires on {{ formatDate(employee.driving_license_expiry) }}. Remember to renew on time.</p>
                                    </div>
                                </div>

                                <!-- Leave Balance Reminders -->
                                <div v-if="employee.annual_leave_balance > 20" class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-blue-800">High Leave Balance</p>
                                        <p class="text-xs text-blue-600">You have {{ employee.annual_leave_balance }} days of annual leave.</p>
                                    </div>
                                </div>

                                <div v-if="employee.annual_leave_balance < 5" class="flex items-center p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-orange-800">Low Leave Balance</p>
                                        <p class="text-xs text-orange-600">You only have {{ employee.annual_leave_balance }} days of annual leave remaining. Your leave balance will be replenished monthly.</p>
                                    </div>
                                </div>

                                <!-- No reminders message -->
                                <div v-if="!hasExpiringDocuments && employee.annual_leave_balance <= 20 && employee.annual_leave_balance >= 5" class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">All Good!</h3>
                                    <p class="mt-1 text-sm text-gray-500">No urgent actions required at this time. All your documents are up to date.</p>
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
    </EmployeeLayout>
</template>