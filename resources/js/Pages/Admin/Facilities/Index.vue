<!-- resources/js/Pages/Admin/Facilities/Index.vue -->
<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import SearchInput from '@/Components/SearchInput.vue';
    import FilterDropdown from '@/Components/FilterDropdown.vue';
    import Pagination from '@/Components/Pagination.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';

    const props = defineProps({
        facilities: Object,
        filters: Object,
    });

    // Search and filters
    const search = ref(props.filters.search || '');
    const perPage = ref(props.filters.per_page || 15);

    // Modals
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showViewModal = ref(false);
    const showDeleteModal = ref(false);

    // Current facility for operations
    const currentFacility = ref(null);

    // Forms
    const createForm = useForm({
        name: '',
        description: '',
    });

    const editForm = useForm({
        name: '',
        description: '',
    });

    // Watch for URL changes
    watch([search, perPage], () => {
        router.get(route('admin.facilities.index'), {
            search: search.value,
            per_page: perPage.value
        }, {
            preserveState: true,
            replace: true,
        });
    });

    // Modal functions
    const openCreateModal = () => {
        createForm.reset();
        showCreateModal.value = true;
    };

    const openEditModal = (facility) => {
        currentFacility.value = facility;
        editForm.name = facility.name;
        editForm.description = facility.description || '';
        showEditModal.value = true;
    };

    const openViewModal = (facility) => {
        currentFacility.value = facility;
        showViewModal.value = true;
    };

    const openDeleteModal = (facility) => {
        currentFacility.value = facility;
        showDeleteModal.value = true;
    };

    const closeModals = () => {
        showCreateModal.value = false;
        showEditModal.value = false;
        showViewModal.value = false;
        showDeleteModal.value = false;
        currentFacility.value = null;
    };

    // CRUD operations
    const createFacility = () => {
        createForm.post(route('admin.facilities.store'), {
            onSuccess: () => {
                closeModals();
                createForm.reset();
            }
        });
    };

    const updateFacility = () => {
        editForm.put(route('admin.facilities.update', currentFacility.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const deleteFacility = () => {
        router.delete(route('admin.facilities.destroy', currentFacility.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const resetFilters = () => {
        search.value = '';
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const getFacilityIcon = (facilityName) => {
        const name = facilityName.toLowerCase();
        if (name.includes('doctor') || name.includes('physician')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />';
        } else if (name.includes('nurse')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />';
        } else if (name.includes('pharmacist')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />';
        } else if (name.includes('driver')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />';
        }
        return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />';
    };
</script>

<template>
    <Head title="Facilities" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Facilities</h2>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Facility
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="w-full md:w-1/3">
                                <SearchInput v-model="search" placeholder="Search facilities..." />
                            </div>
                            <div class="flex gap-4 items-center">
                                <FilterDropdown v-model="perPage" label="Per Page" :options="[15, 25, 50, 100]" />
                                <button @click="resetFilters" class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employees
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Created
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="facility in facilities.data" :key="facility.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getFacilityIcon(facility.name)">
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ facility.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">{{ facility.description || 'No description' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ facility.employees_count }} employees
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(facility.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button @click="openViewModal(facility)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button @click="openEditModal(facility)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="openDeleteModal(facility)" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="facilities.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No facilities found. <button @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create your first facility</button>.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="facilities.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="createFacility">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Facility</h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="create_name" value="Facility Name" />
                                    <TextInput
                                            id="create_name"
                                            v-model="createForm.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="e.g., Doctor, Nurse, Pharmacist"
                                    />
                                    <InputError :message="createForm.errors.name" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="create_description" value="Description (Optional)" />
                                    <textarea
                                            id="create_description"
                                            v-model="createForm.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Brief description of the facility role..."
                                    ></textarea>
                                    <InputError :message="createForm.errors.description" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="createForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="createForm.processing">Creating...</span>
                                <span v-else>Create Facility</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Similar Edit, View, and Delete Modals as above patterns -->
        <!-- ... (following the same structure as previous modals) ... -->

    </AdminLayout>
</template>