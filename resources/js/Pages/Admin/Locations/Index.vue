<!-- resources/js/Pages/Admin/Locations/Index.vue -->
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
        locations: Object,
        reporting_managers: Array,
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

    // Current location for operations
    const currentLocation = ref(null);

    // Forms
    const createForm = useForm({
        name: '',
        address: '',
        reporting_manager_id: '',
    });

    const editForm = useForm({
        name: '',
        address: '',
        reporting_manager_id: '',
    });

    // Watch for URL changes
    watch([search, perPage], () => {
        router.get(route('admin.locations.index'), {
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

    const openEditModal = (location) => {
        currentLocation.value = location;
        editForm.name = location.name;
        editForm.address = location.address || '';
        editForm.reporting_manager_id = location.reporting_manager_id || '';
        showEditModal.value = true;
    };

    const openViewModal = (location) => {
        currentLocation.value = location;
        showViewModal.value = true;
    };

    const openDeleteModal = (location) => {
        currentLocation.value = location;
        showDeleteModal.value = true;
    };

    const closeModals = () => {
        showCreateModal.value = false;
        showEditModal.value = false;
        showViewModal.value = false;
        showDeleteModal.value = false;
        currentLocation.value = null;
    };

    // CRUD operations
    const createLocation = () => {
        createForm.post(route('admin.locations.store'), {
            onSuccess: () => {
                closeModals();
                createForm.reset();
            }
        });
    };

    const updateLocation = () => {
        editForm.put(route('admin.locations.update', currentLocation.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const deleteLocation = () => {
        router.delete(route('admin.locations.destroy', currentLocation.value.id), {
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
</script>

<template>
    <Head title="Locations" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Locations</h2>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Location
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
                                <SearchInput v-model="search" placeholder="Search locations..." />
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

                <!-- Locations Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Location Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Address
                                    </th>
<!--                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                        Reporting Manager-->
<!--                                    </th>-->
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employees
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="location in locations.data" :key="location.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ location.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">{{ location.address || 'No address provided' }}</div>
                                    </td>
<!--                                    <td class="px-6 py-4 whitespace-nowrap">-->
<!--                                        <div v-if="location.reporting_manager" class="text-sm text-gray-900">-->
<!--                                            {{ location.reporting_manager.name }}-->
<!--                                        </div>-->
<!--                                        <div v-else class="text-sm text-gray-500">No manager assigned</div>-->
<!--                                    </td>-->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                           {{ location.employees?.length || 0 }} employees
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button @click="openViewModal(location)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button @click="openEditModal(location)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="openDeleteModal(location)" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="locations.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No locations found. <button @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create your first location</button>.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="locations.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="createLocation">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Location</h3>

                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="create_name" value="Location Name" />
                                            <TextInput
                                                    id="create_name"
                                                    v-model="createForm.name"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    required
                                                    placeholder="e.g., Main Office"
                                            />
                                            <InputError :message="createForm.errors.name" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="create_address" value="Address (Optional)" />
                                            <textarea
                                                    id="create_address"
                                                    v-model="createForm.address"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Complete address..."
                                            ></textarea>
                                            <InputError :message="createForm.errors.address" class="mt-2" />
                                        </div>

<!--                                        <div>-->
<!--                                            <InputLabel for="create_reporting_manager_id" value="Reporting Manager (Optional)" />-->
<!--                                            <select-->
<!--                                                    id="create_reporting_manager_id"-->
<!--                                                    v-model="createForm.reporting_manager_id"-->
<!--                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"-->
<!--                                            >-->
<!--                                                <option value="">Select Reporting Manager</option>-->
<!--                                                <option v-for="manager in reporting_managers" :key="manager.id" :value="manager.id">-->
<!--                                                    {{ manager.name }}-->
<!--                                                </option>-->
<!--                                            </select>-->
<!--                                            <InputError :message="createForm.errors.reporting_manager_id" class="mt-2" />-->
<!--                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="createForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="createForm.processing">Creating...</span>
                                <span v-else>Create Location</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">
                                Cancel
                            </SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="updateLocation">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Location</h3>

                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="edit_name" value="Location Name" />
                                            <TextInput
                                                    id="edit_name"
                                                    v-model="editForm.name"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    required
                                            />
                                            <InputError :message="editForm.errors.name" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="edit_address" value="Address (Optional)" />
                                            <textarea
                                                    id="edit_address"
                                                    v-model="editForm.address"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            ></textarea>
                                            <InputError :message="editForm.errors.address" class="mt-2" />
                                        </div>

<!--                                        <div>-->
<!--                                            <InputLabel for="edit_reporting_manager_id" value="Reporting Manager (Optional)" />-->
<!--                                            <select-->
<!--                                                    id="edit_reporting_manager_id"-->
<!--                                                    v-model="editForm.reporting_manager_id"-->
<!--                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"-->
<!--                                            >-->
<!--                                                <option value="">Select Reporting Manager</option>-->
<!--                                                <option v-for="manager in reporting_managers" :key="manager.id" :value="manager.id">-->
<!--                                                    {{ manager.name }}-->
<!--                                                </option>-->
<!--                                            </select>-->
<!--                                            <InputError :message="editForm.errors.reporting_manager_id" class="mt-2" />-->
<!--                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="editForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="editForm.processing">Updating...</span>
                                <span v-else">Update Location</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">
                                Cancel
                            </SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal && currentLocation" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Location Details</h3>

                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ currentLocation.name }}</h4>
                                        <p class="text-gray-600 mb-4">{{ currentLocation.address || 'No address provided' }}</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<!--                                            <div>-->
<!--                                                <span class="font-medium text-gray-500">Reporting Manager:</span>-->
<!--                                                <span class="ml-2 text-gray-900">{{ currentLocation.reporting_manager?.name || 'Not assigned' }}</span>-->
<!--                                            </div>-->
                                            <div>
                                                <span class="font-medium text-gray-500">Total Employees:</span>
                                                <span class="ml-2 text-gray-900">{{ currentLocation.employees?.length || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employees List -->
                                    <div v-if="currentLocation.employees && currentLocation.employees.length > 0" class="mt-4">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Employees at this location:</h5>
                                        <div class="max-h-32 overflow-y-auto">
                                            <div v-for="employee in currentLocation.employees" :key="employee.id" class="text-sm text-gray-600 py-1">
                                                • {{ employee.employee_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="openEditModal(currentLocation); showViewModal = false;" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Edit Location
                        </button>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">
                            Close
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal && currentLocation" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Location</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ currentLocation.name }}"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="deleteLocation" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">
                            Cancel
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>