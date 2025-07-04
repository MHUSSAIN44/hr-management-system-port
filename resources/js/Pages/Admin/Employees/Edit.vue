<!-- resources/js/Pages/Admin/Employees/Edit.vue -->
<script setup>
    import { ref } from 'vue';
    import { Head, useForm, Link } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import DocumentUploader from '@/Components/DocumentUploader.vue';

    const props = defineProps({
        employee: Object,
        departments: Array,
        designations: Array,
        facilities: Array,
        locations: Array,
        reporting_managers: Array,
    });

    // Helper function to format date for HTML date input (YYYY-MM-DD)
    const formatDateForInput = (dateString) => {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            return date.toISOString().split('T')[0];
        } catch (error) {
            return '';
        }
    };

    // Helper function to display formatted date
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString();
        } catch (error) {
            return 'N/A';
        }
    };

    const form = useForm({
        title: props.employee.title || 'Mr.',
        employee_name: props.employee.employee_name || '',
        email_address: props.employee.email_address || '',
        department_id: props.employee.department_id || '',
        designation_id: props.employee.designation_id || '',
        facility_id: props.employee.facility_id || '',
        location_id: props.employee.location_id || '',
        contact: props.employee.contact || '',
        whatsapp_number: props.employee.whatsapp_number || '',
        company_first_paid_working_day: formatDateForInput(props.employee.company_first_paid_working_day),
        nationality: props.employee.nationality || '',
        passport_number: props.employee.passport_number || '',
        passport_expiry: formatDateForInput(props.employee.passport_expiry),
        passport_file: null,
        visa_number: props.employee.visa_number || '',
        visa_start_date: formatDateForInput(props.employee.visa_start_date),
        visa_expiry_date: formatDateForInput(props.employee.visa_expiry_date),
        visa_file: null,
        date_of_birth: formatDateForInput(props.employee.date_of_birth),
        driving_license_expiry: formatDateForInput(props.employee.driving_license_expiry),
        covid_vaccinated: props.employee.covid_vaccinated || false,
        insurance_issued_on: formatDateForInput(props.employee.insurance_issued_on),
        insurance_expiry_date: formatDateForInput(props.employee.insurance_expiry_date),
        eid_number: props.employee.eid_number || '',
        eid_expiry_date: formatDateForInput(props.employee.eid_expiry_date),
        malpractice_expiry_date: formatDateForInput(props.employee.malpractice_expiry_date),
        status: props.employee.status || 'active',
        reporting_manager_id: props.employee.reporting_manager_id || '',
        user_role: props.employee.user?.role || 'employee',
        documents: [], // For new documents
        _method: 'PUT',
    });

    const passportInput = ref(null);
    const visaInput = ref(null);

    // Handle role change
    const handleRoleChange = () => {
        if (form.user_role === 'reporting_manager') {
            form.reporting_manager_id = '';
        }
    };

    // Submit the form
    const submit = () => {
        form.post(route('admin.employees.update', props.employee.id), {
            preserveScroll: true,
        });
    };
</script>

<template>
    <Head title="Edit Employee" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Employee</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.employees.show', employee.id)" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <!-- Personal Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Personal Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Title -->
                                    <div>
                                        <InputLabel for="title" value="Title" />
                                        <select
                                                id="title"
                                                v-model="form.title"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Ms.">Ms.</option>
                                            <option value="Dr.">Dr.</option>
                                        </select>
                                        <InputError :message="form.errors.title" class="mt-2" />
                                    </div>

                                    <!-- Employee Name -->
                                    <div>
                                        <InputLabel for="employee_name" value="Employee Name" />
                                        <TextInput
                                                id="employee_name"
                                                v-model="form.employee_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.employee_name" class="mt-2" />
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <InputLabel for="email_address" value="Email Address" />
                                        <TextInput
                                                id="email_address"
                                                v-model="form.email_address"
                                                type="email"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.email_address" class="mt-2" />
                                    </div>

                                    <!-- Date of Birth -->
                                    <div>
                                        <InputLabel for="date_of_birth" value="Date of Birth" />
                                        <input
                                                id="date_of_birth"
                                                v-model="form.date_of_birth"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        />
                                        <InputError :message="form.errors.date_of_birth" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.date_of_birth">
                                            Current: {{ formatDate(employee.date_of_birth) }}
                                        </p>
                                    </div>

                                    <!-- Contact -->
                                    <div>
                                        <InputLabel for="contact" value="Contact Number" />
                                        <TextInput
                                                id="contact"
                                                v-model="form.contact"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.contact" class="mt-2" />
                                    </div>

                                    <!-- WhatsApp -->
                                    <div>
                                        <InputLabel for="whatsapp_number" value="WhatsApp Number" />
                                        <TextInput
                                                id="whatsapp_number"
                                                v-model="form.whatsapp_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.whatsapp_number" class="mt-2" />
                                    </div>

                                    <!-- Nationality -->
                                    <div>
                                        <InputLabel for="nationality" value="Nationality" />
                                        <TextInput
                                                id="nationality"
                                                v-model="form.nationality"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.nationality" class="mt-2" />
                                    </div>

                                    <!-- COVID Vaccinated -->
                                    <div>
                                        <div class="flex items-center">
                                            <input
                                                    id="covid_vaccinated"
                                                    v-model="form.covid_vaccinated"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <InputLabel for="covid_vaccinated" value="COVID Vaccinated" class="ml-2" />
                                        </div>
                                        <InputError :message="form.errors.covid_vaccinated" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Employment Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Employment Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Department -->
                                    <div>
                                        <InputLabel for="department_id" value="Department" />
                                        <select
                                                id="department_id"
                                                v-model="form.department_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="">Select Department</option>
                                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                                {{ department.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.department_id" class="mt-2" />
                                    </div>

                                    <!-- Designation -->
                                    <div>
                                        <InputLabel for="designation_id" value="Designation" />
                                        <select
                                                id="designation_id"
                                                v-model="form.designation_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="">Select Designation</option>
                                            <option v-for="designation in designations" :key="designation.id" :value="designation.id">
                                                {{ designation.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.designation_id" class="mt-2" />
                                    </div>

                                    <!-- Facility -->
                                    <div>
                                        <InputLabel for="facility_id" value="Facility" />
                                        <select
                                                id="facility_id"
                                                v-model="form.facility_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="">Select Facility</option>
                                            <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
                                                {{ facility.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.facility_id" class="mt-2" />
                                    </div>

                                    <!-- Location -->
                                    <div>
                                        <InputLabel for="location_id" value="Location" />
                                        <select
                                                id="location_id"
                                                v-model="form.location_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="">Select Location</option>
                                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                                {{ location.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.location_id" class="mt-2" />
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <InputLabel for="status" value="Employment Status" />
                                        <select
                                                id="status"
                                                v-model="form.status"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        >
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="terminated">Terminated</option>
                                        </select>
                                        <InputError :message="form.errors.status" class="mt-2" />
                                    </div>

                                    <!-- First Paid Working Day -->
                                    <div>
                                        <InputLabel for="company_first_paid_working_day" value="First Paid Working Day" />
                                        <input
                                                id="company_first_paid_working_day"
                                                v-model="form.company_first_paid_working_day"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        />
                                        <InputError :message="form.errors.company_first_paid_working_day" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.company_first_paid_working_day">
                                            Current: {{ formatDate(employee.company_first_paid_working_day) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Assignment Section -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Role Assignment</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Employee Role -->
                                    <div>
                                        <InputLabel for="user_role" value="Employee Role" />
                                        <select
                                                id="user_role"
                                                v-model="form.user_role"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                                @change="handleRoleChange"
                                        >
                                            <option value="employee">Regular Employee</option>
                                            <option value="reporting_manager">Reporting Manager</option>
                                        </select>
                                        <InputError :message="form.errors.user_role" class="mt-2" />
                                        <p class="mt-1 text-sm text-gray-500">
                                            <span v-if="form.user_role === 'employee'">Regular employee who reports to a manager</span>
                                            <span v-else-if="form.user_role === 'reporting_manager'">Manager who can manage employees and approve leaves</span>
                                        </p>
                                    </div>

                                    <!-- Reporting Manager (only for employees) -->
                                    <div v-if="form.user_role === 'employee'">
                                        <InputLabel for="reporting_manager_id" value="Reporting Manager" />
                                        <select
                                                id="reporting_manager_id"
                                                v-model="form.reporting_manager_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        >
                                            <option value="">Select Reporting Manager</option>
                                            <option v-for="manager in reporting_managers" :key="manager.id" :value="manager.id">
                                                {{ manager.employee?.employee_name || manager.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.reporting_manager_id" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Passport Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Passport Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Passport Number -->
                                    <div>
                                        <InputLabel for="passport_number" value="Passport Number" />
                                        <TextInput
                                                id="passport_number"
                                                v-model="form.passport_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.passport_number" class="mt-2" />
                                    </div>

                                    <!-- Passport Expiry -->
                                    <div>
                                        <InputLabel for="passport_expiry" value="Passport Expiry Date" />
                                        <input
                                                id="passport_expiry"
                                                v-model="form.passport_expiry"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        />
                                        <InputError :message="form.errors.passport_expiry" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.passport_expiry">
                                            Current: {{ formatDate(employee.passport_expiry) }}
                                        </p>
                                    </div>

                                    <!-- Current Passport File -->
                                    <div v-if="employee.passport_file" class="md:col-span-2">
                                        <InputLabel value="Current Passport File" />
                                        <div class="mt-1 flex items-center space-x-2">
                                            <a :href="`/storage/${employee.passport_file}`" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                View Current Passport Document
                                            </a>
                                        </div>
                                    </div>

                                    <!-- New Passport File -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="passport_file" value="Update Passport File (Optional)" />
                                        <input
                                                type="file"
                                                id="passport_file"
                                                ref="passportInput"
                                                @change="form.passport_file = $event.target.files[0]"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*,application/pdf"
                                        />
                                        <InputError :message="form.errors.passport_file" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep current file</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Visa Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Visa Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Visa Number -->
                                    <div>
                                        <InputLabel for="visa_number" value="Visa Number" />
                                        <TextInput
                                                id="visa_number"
                                                v-model="form.visa_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.visa_number" class="mt-2" />
                                    </div>

                                    <!-- Visa Start Date -->
                                    <div>
                                        <InputLabel for="visa_start_date" value="Visa Start Date" />
                                        <input
                                                id="visa_start_date"
                                                v-model="form.visa_start_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        />
                                        <InputError :message="form.errors.visa_start_date" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.visa_start_date">
                                            Current: {{ formatDate(employee.visa_start_date) }}
                                        </p>
                                    </div>

                                    <!-- Visa Expiry Date -->
                                    <div>
                                        <InputLabel for="visa_expiry_date" value="Visa Expiry Date" />
                                        <input
                                                id="visa_expiry_date"
                                                v-model="form.visa_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                required
                                        />
                                        <InputError :message="form.errors.visa_expiry_date" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.visa_expiry_date">
                                            Current: {{ formatDate(employee.visa_expiry_date) }}
                                        </p>
                                    </div>

                                    <!-- Current Visa File -->
                                    <div v-if="employee.visa_file">
                                        <InputLabel value="Current Visa File" />
                                        <div class="mt-1 flex items-center space-x-2">
                                            <a :href="`/storage/${employee.visa_file}`" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                View Current Visa Document
                                            </a>
                                        </div>
                                    </div>

                                    <!-- New Visa File -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="visa_file" value="Update Visa File (Optional)" />
                                        <input
                                                type="file"
                                                id="visa_file"
                                                ref="visaInput"
                                                @change="form.visa_file = $event.target.files[0]"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*,application/pdf"
                                        />
                                        <InputError :message="form.errors.visa_file" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep current file</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Additional Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Driving License Expiry -->
                                    <div>
                                        <InputLabel for="driving_license_expiry" value="Driving License Expiry (Optional)" />
                                        <input
                                                id="driving_license_expiry"
                                                v-model="form.driving_license_expiry"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <InputError :message="form.errors.driving_license_expiry" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.driving_license_expiry">
                                            Current: {{ formatDate(employee.driving_license_expiry) }}
                                        </p>
                                    </div>

                                    <!-- Insurance Issued On -->
                                    <div>
                                        <InputLabel for="insurance_issued_on" value="Insurance Issued On (Optional)" />
                                        <input
                                                id="insurance_issued_on"
                                                v-model="form.insurance_issued_on"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <InputError :message="form.errors.insurance_issued_on" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.insurance_issued_on">
                                            Current: {{ formatDate(employee.insurance_issued_on) }}
                                        </p>
                                    </div>

                                    <!-- Insurance Expiry -->
                                    <div>
                                        <InputLabel for="insurance_expiry_date" value="Insurance Expiry Date (Optional)" />
                                        <input
                                                id="insurance_expiry_date"
                                                v-model="form.insurance_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <InputError :message="form.errors.insurance_expiry_date" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.insurance_expiry_date">
                                            Current: {{ formatDate(employee.insurance_expiry_date) }}
                                        </p>
                                    </div>

                                    <!-- EID Number -->
                                    <div>
                                        <InputLabel for="eid_number" value="EID Number (Optional)" />
                                        <TextInput
                                                id="eid_number"
                                                v-model="form.eid_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.eid_number" class="mt-2" />
                                    </div>

                                    <!-- EID Expiry -->
                                    <div>
                                        <InputLabel for="eid_expiry_date" value="EID Expiry Date (Optional)" />
                                        <input
                                                id="eid_expiry_date"
                                                v-model="form.eid_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <InputError :message="form.errors.eid_expiry_date" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.eid_expiry_date">
                                            Current: {{ formatDate(employee.eid_expiry_date) }}
                                        </p>
                                    </div>

                                    <!-- Malpractice Expiry -->
                                    <div>
                                        <InputLabel for="malpractice_expiry_date" value="Malpractice Expiry Date (Optional)" />
                                        <input
                                                id="malpractice_expiry_date"
                                                v-model="form.malpractice_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <InputError :message="form.errors.malpractice_expiry_date" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500" v-if="employee.malpractice_expiry_date">
                                            Current: {{ formatDate(employee.malpractice_expiry_date) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Documents -->
                            <div v-if="employee.documents && employee.documents.length > 0" class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Current Documents</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="document in employee.documents" :key="document.id" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ document.title }}</h4>
                                            <span class="text-xs text-gray-500">
                                                {{ document.expiry_date ? formatDate(document.expiry_date) : 'No expiry' }}
                                            </span>
                                        </div>
                                        <a :href="`/storage/${document.file_path}`" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            View Document
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Add New Documents -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6">Add New Documents</h3>
                                <DocumentUploader v-model="form.documents" :errors="form.errors" />
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end space-x-4">
                                <SecondaryButton :href="route('admin.employees.show', employee.id)">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Employee</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>