<!-- resources/js/Pages/Manager/Profile/Edit.vue -->
<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import ManagerLayout from '@/Layouts/ManagerLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        employee: Object,
    });

    const form = useForm({
        contact: props.employee?.contact || '',
        whatsapp_number: props.employee?.whatsapp_number || '',
        email_address: props.employee?.email_address || '',
    });

    const submit = () => {
        form.put(route('manager.profile.update'), {
            preserveScroll: true,
        });
    };
</script>

<template>
    <Head title="Edit Manager Profile" />

    <ManagerLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Manager Profile</h2>
                <SecondaryButton :href="route('manager.profile')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Profile
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Manager Info Header -->
                        <div class="mb-8 p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <span class="text-green-600 text-xl font-bold">
                                        {{ employee?.employee_name?.split(' ').map(n => n[0]).join('') || 'M' }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ employee?.title }} {{ employee?.employee_name }}</h3>
                                    <p class="text-sm text-green-600 font-medium">{{ employee?.designation?.name }}</p>
                                    <p class="text-sm text-gray-500">{{ employee?.department?.name }} - {{ employee?.location?.name }}</p>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Editable Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editable Contact Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Email Address -->
                                    <div>
                                        <InputLabel for="email_address" value="Email Address" />
                                        <TextInput
                                                id="email_address"
                                                v-model="form.email_address"
                                                type="email"
                                                class="mt-1 block w-full"
                                                required
                                                autofocus
                                        />
                                        <InputError :message="form.errors.email_address" class="mt-2" />
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
                                    <div class="md:col-span-2">
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
                                </div>
                            </div>

                            <!-- Non-Editable Information Display -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.5 6.5m3.378 3.378a3 3 0 014.243-4.242 3 3 0 014.242 4.242m-4.242-4.242L17.5 17.5" />
                                    </svg>
                                    Read-Only Information
                                </h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-4">The following information is managed by HR and cannot be edited:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Employee Name:</span>
                                            <span class="ml-2 text-gray-900">{{ employee?.employee_name }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Designation:</span>
                                            <span class="ml-2 text-gray-900">{{ employee?.designation?.name }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Department:</span>
                                            <span class="ml-2 text-gray-900">{{ employee?.department?.name }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Location:</span>
                                            <span class="ml-2 text-gray-900">{{ employee?.location?.name }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Role:</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Reporting Manager
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Status:</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ employee?.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end space-x-4">
                                <SecondaryButton :href="route('manager.profile')">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing" class="bg-green-600 hover:bg-green-700 focus:ring-green-200">
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Manager Profile</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>