<!-- resources/js/Pages/Admin/Departments/Edit.vue -->
<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        department: Object,
    });

    const form = useForm({
        name: props.department.name,
        description: props.department.description || '',
    });

    const submit = () => {
        form.put(route('admin.departments.update', props.department.id));
    };
</script>

<template>
    <Head title="Edit Department" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Department</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Department Name -->
                                <div>
                                    <InputLabel for="name" value="Department Name" />
                                    <TextInput
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            autofocus
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <InputLabel for="description" value="Description (Optional)" />
                                    <textarea
                                            id="description"
                                            v-model="form.description"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    ></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end mt-6 space-x-4">
                                <SecondaryButton :href="route('admin.departments.show', department.id)">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Department</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>