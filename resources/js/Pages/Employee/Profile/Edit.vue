<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    employee: Object,
});

const form = useForm({
    phone: props.employee.phone || '',
    address: props.employee.address || '',
    city: props.employee.city || '',
    state: props.employee.state || '',
    zip_code: props.employee.zip_code || '',
    country: props.employee.country || '',
    emergency_contact_name: props.employee.emergency_contact_name || '',
    emergency_contact_phone: props.employee.emergency_contact_phone || '',
    profile_photo: null,
    _method: 'PUT',
});

const photoPreview = ref(props.employee.profile_photo ? `/storage/${props.employee.profile_photo}` : null);
const photoInput = ref(null);

// Update profile photo preview
const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;

    form.profile_photo = photo;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

// Remove profile photo
const removePhoto = () => {
    form.profile_photo = null;
    photoPreview.value = null;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
};

// Submit the form
const submit = () => {
    form.post(route('employee.profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Profile" />

    <EmployeeLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Profile</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Profile Header -->
                                <div class="md:col-span-2">
                                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Edit Your Profile</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Update your personal information and emergency contacts. Your employment details can only be updated by an administrator.
                                    </p>
                                </div>

                                <!-- Profile Photo -->
                                <div class="md:col-span-2 flex items-start space-x-6">
                                    <div>
                                        <div v-if="photoPreview" class="h-24 w-24 rounded-full overflow-hidden bg-gray-100">
                                            <img :src="photoPreview" alt="Profile preview" class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else class="h-24 w-24 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                            <span class="text-gray-500 text-lg font-semibold">
                                                {{ employee.first_name.charAt(0) }}{{ employee.last_name.charAt(0) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel for="profile_photo" value="Profile Photo" />
                                        <input
                                            type="file"
                                            id="profile_photo"
                                            ref="photoInput"
                                            @change="updatePhotoPreview"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                            accept="image/*"
                                        />
                                        <InputError :message="form.errors.profile_photo" class="mt-2" />
                                        <SecondaryButton v-if="photoPreview" type="button" class="mt-2" @click="removePhoto">
                                            Remove Photo
                                        </SecondaryButton>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="md:col-span-2">
                                    <h3 class="text-md font-medium text-gray-900 mb-2">Contact Information</h3>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <InputLabel for="phone" value="Phone Number" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>

                                <!-- Address Section -->
                                <div class="md:col-span-2">
                                    <h3 class="text-md font-medium text-gray-900 mt-4 mb-2">Address</h3>
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <InputLabel for="address" value="Street Address" />
                                    <TextInput
                                        id="address"
                                        v-model="form.address"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.address" class="mt-2" />
                                </div>

                                <!-- City -->
                                <div>
                                    <InputLabel for="city" value="City" />
                                    <TextInput
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.city" class="mt-2" />
                                </div>

                                <!-- State -->
                                <div>
                                    <InputLabel for="state" value="State/Province" />
                                    <TextInput
                                        id="state"
                                        v-model="form.state"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.state" class="mt-2" />
                                </div>

                                <!-- Zip Code -->
                                <div>
                                    <InputLabel for="zip_code" value="Zip/Postal Code" />
                                    <TextInput
                                        id="zip_code"
                                        v-model="form.zip_code"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.zip_code" class="mt-2" />
                                </div>

                                <!-- Country -->
                                <div>
                                    <InputLabel for="country" value="Country" />
                                    <TextInput
                                        id="country"
                                        v-model="form.country"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.country" class="mt-2" />
                                </div>

                                <!-- Emergency Contact Name -->
                                <div>
                                    <InputLabel for="emergency_contact_name" value="Contact Name" />
                                    <TextInput
                                        id="emergency_contact_name"
                                        v-model="form.emergency_contact_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.emergency_contact_name" class="mt-2" />
                                </div>

                                <!-- Emergency Contact Phone -->
                                <div>
                                    <InputLabel for="emergency_contact_phone" value="Contact Phone" />
                                    <TextInput
                                        id="emergency_contact_phone"
                                        v-model="form.emergency_contact_phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.emergency_contact_phone" class="mt-2" />
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end mt-6">
                                <SecondaryButton :href="route('employee.profile')" class="mr-3">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing">
                                    Update Profile
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template> -->

