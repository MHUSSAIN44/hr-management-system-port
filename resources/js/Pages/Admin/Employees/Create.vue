<!-- resources/js/Pages/Admin/Employees/Create.vue -->
<script setup>
    import { ref } from 'vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import DocumentUploader from '@/Components/DocumentUploader.vue';

    const props = defineProps({
        departments: Array,
        designations: Array,
        facilities: Array,
        locations: Array,
        reporting_managers: Array,
    });

    const form = useForm({
        title: 'Mr.',
        employee_name: '',
        email_address: '',
        department_id: '',
        designation_id: '',
        facility_id: '',
        location_id: '',
        contact: '',
        whatsapp_number: '',
        company_first_paid_working_day: '',
        nationality: '',
        passport_number: '',
        passport_expiry: '',
        passport_file: null,
        visa_number: '',
        visa_start_date: '',
        visa_expiry_date: '',
        visa_file: null,
        date_of_birth: '',
        driving_license_expiry: '',
        covid_vaccinated: false,
        insurance_issued_on: '',
        insurance_expiry_date: '',
        eid_number: '',
        eid_expiry_date: '',
        malpractice_expiry_date: '',
        reporting_manager_id: '',
        user_role: 'employee', // Add this field and set default to 'employee'
        documents: [],
    });

    const passportInput = ref(null);
    const visaInput = ref(null);

    // Add the handleRoleChange function
    const handleRoleChange = () => {
        if (form.user_role === 'reporting_manager') {
            form.reporting_manager_id = '';
        }
    };

    const submit = () => {
        form.post(route('admin.employees.store'), {
            preserveScroll: true,
            onError: (errors) => console.log('Error:', errors),
            onSuccess: () => form.reset(),
        });
    };
</script>

<template>
    <Head title="Create Employee" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Employee</h2>
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
                                        <TextInput
                                                id="date_of_birth"
                                                v-model="form.date_of_birth"
                                                type="date"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.date_of_birth" class="mt-2" />
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

                                    <!-- First Paid Working Day -->
                                    <div>
                                        <InputLabel for="company_first_paid_working_day" value="First Paid Working Day" />
                                        <TextInput
                                                id="company_first_paid_working_day"
                                                v-model="form.company_first_paid_working_day"
                                                type="date"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.company_first_paid_working_day" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Role Selection Section -->
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
                                                {{ manager.employee_name || manager.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.reporting_manager_id" class="mt-2" />
                                    </div>

                                    <!-- Manager Info (when creating reporting manager) -->
<!--                                    <div v-if="form.user_role === 'reporting_manager'" class="md:col-span-2">-->
<!--                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">-->
<!--                                            <div class="flex">-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">-->
<!--                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />-->
<!--                                                    </svg>-->
<!--                                                </div>-->
<!--                                                <div class="ml-3">-->
<!--                                                    <h3 class="text-sm font-medium text-blue-800">Reporting Manager Privileges</h3>-->
<!--                                                    <div class="mt-2 text-sm text-blue-700">-->
<!--                                                        <ul class="list-disc pl-5 space-y-1">-->
<!--                                                            <li>Can approve/reject leave requests from their team</li>-->
<!--                                                            <li>Can request overtime hours for their facility</li>-->
<!--                                                            <li>Can manage attendance for their location</li>-->
<!--                                                            <li>Does not report to anyone (no reporting manager)</li>-->
<!--                                                        </ul>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
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
                                        <TextInput
                                                id="passport_expiry"
                                                v-model="form.passport_expiry"
                                                type="date"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.passport_expiry" class="mt-2" />
                                    </div>

                                    <!-- Passport File -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="passport_file" value="Passport File (Optional)" />
                                        <input
                                                type="file"
                                                id="passport_file"
                                                ref="passportInput"
                                                @change="form.passport_file = $event.target.files[0]"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*,application/pdf"
                                        />
                                        <InputError :message="form.errors.passport_file" class="mt-2" />
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
                                        <TextInput
                                                id="visa_start_date"
                                                v-model="form.visa_start_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.visa_start_date" class="mt-2" />
                                    </div>

                                    <!-- Visa Expiry Date -->
                                    <div>
                                        <InputLabel for="visa_expiry_date" value="Visa Expiry Date" />
                                        <TextInput
                                                id="visa_expiry_date"
                                                v-model="form.visa_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                                required
                                        />
                                        <InputError :message="form.errors.visa_expiry_date" class="mt-2" />
                                    </div>

                                    <!-- Visa File -->
                                    <div>
                                        <InputLabel for="visa_file" value="Visa File (Optional)" />
                                        <input
                                                type="file"
                                                id="visa_file"
                                                ref="visaInput"
                                                @change="form.visa_file = $event.target.files[0]"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*,application/pdf"
                                        />
                                        <InputError :message="form.errors.visa_file" class="mt-2" />
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
                                        <TextInput
                                                id="driving_license_expiry"
                                                v-model="form.driving_license_expiry"
                                                type="date"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.driving_license_expiry" class="mt-2" />
                                    </div>

                                    <!-- Insurance Issued On -->
                                    <div>
                                        <InputLabel for="insurance_issued_on" value="Insurance Issued On (Optional)" />
                                        <TextInput
                                                id="insurance_issued_on"
                                                v-model="form.insurance_issued_on"
                                                type="date"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.insurance_issued_on" class="mt-2" />
                                    </div>

                                    <!-- Insurance Expiry -->
                                    <div>
                                        <InputLabel for="insurance_expiry_date" value="Insurance Expiry Date (Optional)" />
                                        <TextInput
                                                id="insurance_expiry_date"
                                                v-model="form.insurance_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.insurance_expiry_date" class="mt-2" />
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
                                        <TextInput
                                                id="eid_expiry_date"
                                                v-model="form.eid_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.eid_expiry_date" class="mt-2" />
                                    </div>

                                    <!-- Malpractice Expiry -->
                                    <div>
                                        <InputLabel for="malpractice_expiry_date" value="Malpractice Expiry Date (Optional)" />
                                        <TextInput
                                                id="malpractice_expiry_date"
                                                v-model="form.malpractice_expiry_date"
                                                type="date"
                                                class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.malpractice_expiry_date" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Documents -->
                            <div class="mb-8">
                                <DocumentUploader
                                        :model-value="form.documents"
                                        @update:model-value="form.documents = $event"
                                        :errors="form.errors"
                                />
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end space-x-4">
                                <SecondaryButton :href="route('admin.employees.index')">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Employee</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>