<!-- resources/js/Pages/Admin/Assets/Index.vue -->
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
        assets: Object,
        assetTypes: Array,
        statuses: Array,
        stats: Object,
        filters: Object,
        employees: Array,
    });

    // Predefined asset types
    const predefinedAssetTypes = [
        'Laptop',
        'Desktop Computer',
        'Mobile Phone',
        'Tablet',
        'Printer',
        'Camera',
        'Headphones',
        'Mouse',
        'Keyboard',
        'External Hard Drive',
        'USB Drive',
        'Vehicle/Car',
        'Furniture',
        'Other Equipment'
    ];

    // Search and filters
    const search = ref(props.filters.search || '');
    const perPage = ref(props.filters.per_page || 15);
    const statusFilter = ref(props.filters.status || '');
    const assetTypeFilter = ref(props.filters.asset_type || '');

    // Modals
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showViewModal = ref(false);
    const showDeleteModal = ref(false);
    const showAssignModal = ref(false);

    // Current asset for operations
    const currentAsset = ref(null);

    // Forms
    const createForm = useForm({
        asset_type: '',
        asset_name: '',
        serial_number: '',
        description: '',
        status: 'available',
        assigned_to: '',
        assigned_date: '',
    });

    const editForm = useForm({
        asset_type: '',
        asset_name: '',
        serial_number: '',
        description: '',
        status: '',
        assigned_to: '',
        assigned_date: '',
    });

    const assignForm = useForm({
        employee_id: '',
    });

    // Watch for URL changes
    watch([search, perPage, statusFilter, assetTypeFilter], () => {
        router.get(route('admin.assets.index'), {
            search: search.value,
            per_page: perPage.value,
            status: statusFilter.value,
            asset_type: assetTypeFilter.value,
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

    const openEditModal = (asset) => {
        currentAsset.value = asset;
        editForm.asset_type = asset.asset_type;
        editForm.asset_name = asset.asset_name;
        editForm.serial_number = asset.serial_number;
        editForm.description = asset.description || '';
        editForm.status = asset.status;
        editForm.assigned_to = asset.assigned_to || '';
        editForm.assigned_date = asset.assigned_date || '';
        showEditModal.value = true;
    };

    const openViewModal = (asset) => {
        currentAsset.value = asset;
        showViewModal.value = true;
    };

    const openDeleteModal = (asset) => {
        currentAsset.value = asset;
        showDeleteModal.value = true;
    };

    const openAssignModal = (asset) => {
        currentAsset.value = asset;
        assignForm.reset();
        showAssignModal.value = true;
    };

    const closeModals = () => {
        showCreateModal.value = false;
        showEditModal.value = false;
        showViewModal.value = false;
        showDeleteModal.value = false;
        showAssignModal.value = false;
        currentAsset.value = null;
    };

    // CRUD operations
    const createAsset = () => {
        createForm.post(route('admin.assets.store'), {
            onSuccess: () => {
                closeModals();
                createForm.reset();
            }
        });
    };

    const updateAsset = () => {
        editForm.put(route('admin.assets.update', currentAsset.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const deleteAsset = () => {
        router.delete(route('admin.assets.destroy', currentAsset.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const assignAsset = () => {
        assignForm.post(route('admin.assets.assign', currentAsset.value.id), {
            onSuccess: () => {
                closeModals();
            }
        });
    };

    const unassignAsset = (asset) => {
        router.post(route('admin.assets.unassign', asset.id), {}, {
            onSuccess: () => {
                // Success handled by redirect
            }
        });
    };

    const resetFilters = () => {
        search.value = '';
        statusFilter.value = '';
        assetTypeFilter.value = '';
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'available':
                return 'bg-green-100 text-green-800';
            case 'assigned':
                return 'bg-blue-100 text-blue-800';
            case 'maintenance':
                return 'bg-yellow-100 text-yellow-800';
            case 'retired':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getAssetIcon = (assetType) => {
        const type = assetType.toLowerCase();
        if (type.includes('laptop') || type.includes('computer')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />';
        } else if (type.includes('mobile') || type.includes('phone')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z" />';
        } else if (type.includes('car') || type.includes('vehicle')) {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />';
        }
        return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />';
    };
</script>

<template>
    <Head title="Assets" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assets Management</h2>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Asset
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Responsive Statistics Cards -->
                <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-3 lg:grid-cols-5">
                    <!-- Total Assets -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assets</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Available</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.available }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.assigned }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.maintenance }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retired -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Retired</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.retired }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                            <div class="w-full md:w-1/3">
                                <SearchInput v-model="search" placeholder="Search assets..." />
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select v-model="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Statuses</option>
                                        <option v-for="status in statuses" :key="status" :value="status">
                                            {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select v-model="assetTypeFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Types</option>
                                        <option v-for="type in predefinedAssetTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                    <select v-model="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option :value="15">15</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                        <option :value="100">100</option>
                                    </select>
                                </div>
                                <div>
                                    <button @click="resetFilters" class="w-full px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assets Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Asset
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type & Serial
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assigned To
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assigned Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="asset in assets.data" :key="asset.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getAssetIcon(asset.asset_type)">
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ asset.asset_name }}</div>
                                                <div class="text-sm text-gray-500">{{ asset.description || 'No description' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ asset.asset_type }}</div>
                                        <div class="text-sm text-gray-500">{{ asset.serial_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(asset.status)">
                                            {{ asset.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div v-if="asset.assigned_employee">
                                            <div class="font-medium">{{ asset.assigned_employee.employee_name }}</div>
                                            <div class="text-gray-500">{{ asset.assigned_employee.user?.email }}</div>
                                        </div>
                                        <span v-else class="text-gray-500">Not assigned</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(asset.assigned_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button @click="openViewModal(asset)" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button v-if="asset.status === 'available'" @click="openAssignModal(asset)" class="text-green-600 hover:text-green-900" title="Assign">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </button>
                                            <button v-if="asset.status === 'assigned'" @click="unassignAsset(asset)" class="text-orange-600 hover:text-orange-900" title="Unassign">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                                </svg>
                                            </button>
                                            <button @click="openEditModal(asset)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="openDeleteModal(asset)" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="assets.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No assets found. <button @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create your first asset</button>.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="assets.links" />
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
                    <form @submit.prevent="createAsset">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Asset</h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="create_asset_type" value="Asset Type" />
                                    <select
                                            id="create_asset_type"
                                            v-model="createForm.asset_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required
                                    >
                                        <option value="">Select Asset Type</option>
                                        <option v-for="type in predefinedAssetTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <InputError :message="createForm.errors.asset_type" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="create_asset_name" value="Asset Name" />
                                    <TextInput
                                            id="create_asset_name"
                                            v-model="createForm.asset_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="e.g., Dell Latitude 5520"
                                    />
                                    <InputError :message="createForm.errors.asset_name" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="create_serial_number" value="Serial Number" />
                                    <TextInput
                                            id="create_serial_number"
                                            v-model="createForm.serial_number"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="e.g., SN123456789"
                                    />
                                    <InputError :message="createForm.errors.serial_number" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="create_description" value="Description (Optional)" />
                                    <textarea
                                            id="create_description"
                                            v-model="createForm.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Brief description of the asset..."
                                    ></textarea>
                                    <InputError :message="createForm.errors.description" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="create_status" value="Status" />
                                    <select
                                            id="create_status"
                                            v-model="createForm.status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            required
                                    >
                                        <option value="available">Available</option>
                                        <option value="assigned">Assigned</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="retired">Retired</option>
                                    </select>
                                    <InputError :message="createForm.errors.status" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="createForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="createForm.processing">Creating...</span>
                                <span v-else">Create Asset</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="updateAsset">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Asset</h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="edit_asset_type" value="Asset Type" />
                                    <select
                                            id="edit_asset_type"
                                            v-model="editForm.asset_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            required
                                    >
                                        <option value="">Select Asset Type</option>
                                        <option v-for="type in predefinedAssetTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <InputError :message="editForm.errors.asset_type" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="edit_asset_name" value="Asset Name" />
                                    <TextInput
                                            id="edit_asset_name"
                                            v-model="editForm.asset_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                    />
                                    <InputError :message="editForm.errors.asset_name" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="edit_serial_number" value="Serial Number" />
                                    <TextInput
                                            id="edit_serial_number"
                                            v-model="editForm.serial_number"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                    />
                                    <InputError :message="editForm.errors.serial_number" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="edit_description" value="Description (Optional)" />
                                    <textarea
                                            id="edit_description"
                                            v-model="editForm.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    ></textarea>
                                    <InputError :message="editForm.errors.description" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="edit_status" value="Status" />
                                    <select
                                            id="edit_status"
                                            v-model="editForm.status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            required
                                    >
                                        <option value="available">Available</option>
                                        <option value="assigned">Assigned</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="retired">Retired</option>
                                    </select>
                                    <InputError :message="editForm.errors.status" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="editForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="editForm.processing">Updating...</span>
                                <span v-else">Update Asset</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal && currentAsset" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Asset Details</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ currentAsset.asset_name }}</h4>
                                    <p class="text-gray-600 mb-2">{{ currentAsset.description || 'No description provided' }}</p>
                                </div>
                                <div class="text-right">
                                   <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusColor(currentAsset.status)">
                                       {{ currentAsset.status }}
                                   </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-500">Asset Type:</span>
                                    <span class="ml-2 text-gray-900">{{ currentAsset.asset_type }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-500">Serial Number:</span>
                                    <span class="ml-2 text-gray-900">{{ currentAsset.serial_number }}</span>
                                </div>
                                <div v-if="currentAsset.assigned_employee">
                                    <span class="font-medium text-gray-500">Assigned To:</span>
                                    <span class="ml-2 text-gray-900">{{ currentAsset.assigned_employee.employee_name }}</span>
                                </div>
                                <div v-if="currentAsset.assigned_date">
                                    <span class="font-medium text-gray-500">Assigned Date:</span>
                                    <span class="ml-2 text-gray-900">{{ formatDate(currentAsset.assigned_date) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-500">Created:</span>
                                    <span class="ml-2 text-gray-900">{{ formatDate(currentAsset.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="openEditModal(currentAsset); showViewModal = false;" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Edit Asset
                        </button>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Close</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal && currentAsset" class="fixed inset-0 z-50 overflow-y-auto">
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Asset</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ currentAsset.asset_name }}"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="deleteAsset" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Modal -->
        <div v-if="showAssignModal && currentAsset" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="assignAsset">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign Asset</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Assign "{{ currentAsset.asset_name }}" to an employee.
                            </p>
                            <div>
                                <InputLabel for="assign_employee" value="Select Employee" />
                                <select
                                        id="assign_employee"
                                        v-model="assignForm.employee_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        required
                                >
                                    <option value="">Choose an employee...</option>
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                        {{ employee.employee_name }} - {{ employee.department?.name || 'N/A' }}
                                    </option>
                                </select>
                                <InputError :message="assignForm.errors.employee_id" class="mt-2" />
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton type="submit" :disabled="assignForm.processing" class="w-full sm:w-auto sm:ml-3">
                                <span v-if="assignForm.processing">Assigning...</span>
                                <span v-else>Assign Asset</span>
                            </PrimaryButton>
                            <SecondaryButton @click="closeModals" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>