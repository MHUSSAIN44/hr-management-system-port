<!--// resources/js/Pages/Employee/Attendance/Index.vue-->
<script setup>
    import { ref, onMounted, computed } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        attendance: Object,
        todayAttendance: Object,
        monthlyStats: Object,
        employee: Object,
        filters: Object,
    });

    // Reactive data
    const isLoading = ref(false);
    const locationLoading = ref(false);
    const currentLocation = ref(null);
    const locationError = ref('');
    const checkInError = ref('');
    const successMessage = ref('');
    const shift = ref('morning');
    const notes = ref('');
    const showCheckOutModal = ref(false);
    const checkOutError = ref('');
    const checkOutLoading = ref(false);

    // Filters
    const selectedMonth = ref(props.filters.month || new Date().toISOString().slice(0, 7));
    const selectedStatus = ref(props.filters.status || '');
    const selectedShift = ref(props.filters.shift || '');

    // Computed properties
    const canCheckIn = computed(() => {
        return !props.todayAttendance || !props.todayAttendance.check_in_time;
    });

    const canCheckOut = computed(() => {
        return props.todayAttendance &&
            props.todayAttendance.check_in_time &&
            !props.todayAttendance.check_out_time &&
            props.todayAttendance.status === 'checked_in';
    });

    const isCheckedIn = computed(() => {
        return props.todayAttendance && props.todayAttendance.status === 'checked_in';
    });

    // Methods
    const getCurrentLocation = () => {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported by this browser.'));
                return;
            }

            locationLoading.value = true;
            locationError.value = '';

            const options = {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    try {
                        const { latitude, longitude, accuracy } = position.coords;

                        // Get address from coordinates
                        const address = await getAddressFromCoordinates(latitude, longitude);

                        currentLocation.value = {
                            latitude,
                            longitude,
                            accuracy,
                            address
                        };

                        locationLoading.value = false;
                        resolve(currentLocation.value);
                    } catch (error) {
                        locationLoading.value = false;
                        reject(error);
                    }
                },
                (error) => {
                    locationLoading.value = false;
                    let errorMessage = 'Unable to get your location.';

                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enable location permissions in your browser settings.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable. Please check your GPS settings.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out. Please try again.';
                            break;
                    }

                    reject(new Error(errorMessage));
                },
                options
            );
        });
    };

    const getAddressFromCoordinates = async (lat, lng) => {
        try {
            const response = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=5d01ea6d9065400381fd1a688770790e`);
            const data = await response.json();

            if (data.results && data.results.length > 0) {
                return data.results[0].formatted;
            }

            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        } catch (error) {
            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    };

    const checkIn = async () => {
        if (isLoading.value) return;

        try {
            isLoading.value = true;
            locationError.value = '';
            checkInError.value = '';
            successMessage.value = '';

            // Get current location
            const location = await getCurrentLocation();

            // Make API call using fetch instead of Inertia
            const response = await fetch(route('employee.attendance.checkin'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    latitude: location.latitude,
                    longitude: location.longitude,
                    accuracy: location.accuracy,
                    address: location.address,
                    shift: shift.value,
                })
            });

            const data = await response.json();

            if (data.success) {
                successMessage.value = data.message;
                // Refresh the page data
                router.reload({ only: ['todayAttendance', 'monthlyStats'] });

                // Clear success message after 5 seconds
                setTimeout(() => {
                    successMessage.value = '';
                }, 5000);

            } else {
                // Handle validation and other errors
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat();
                    checkInError.value = errorMessages.join(' ');
                } else {
                    checkInError.value = data.message || 'Check-in failed. Please try again.';
                }
            }

        } catch (error) {
            console.error('Check-in error:', error);

            if (error.message.includes('location')) {
                locationError.value = error.message;
            } else {
                checkInError.value = 'Network error. Please check your connection and try again.';
            }
        } finally {
            isLoading.value = false;
        }
    };

    const openCheckOutModal = () => {
        showCheckOutModal.value = true;
        notes.value = '';
    };

    const checkOut = async () => {
        if (checkOutLoading.value) return;

        try {
            checkOutLoading.value = true;
            locationError.value = '';
            checkOutError.value = '';
            successMessage.value = '';

            // Get current location
            const location = await getCurrentLocation();

            // Make API call using fetch
            const response = await fetch(route('employee.attendance.checkout'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    latitude: location.latitude,
                    longitude: location.longitude,
                    accuracy: location.accuracy,
                    address: location.address,
                    notes: notes.value,
                })
            });

            const data = await response.json();

            if (data.success) {
                successMessage.value = data.message;
                showCheckOutModal.value = false;
                notes.value = '';

                // Refresh the page data
                router.reload({ only: ['todayAttendance', 'monthlyStats'] });

                // Clear success message after 5 seconds
                setTimeout(() => {
                    successMessage.value = '';
                }, 5000);

            } else {
                // Handle validation and other errors
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat();
                    checkOutError.value = errorMessages.join(' ');
                } else {
                    checkOutError.value = data.message || 'Check-out failed. Please try again.';
                }
            }

        } catch (error) {
            console.error('Check-out error:', error);

            if (error.message.includes('location')) {
                locationError.value = error.message;
            } else {
                checkOutError.value = 'Network error. Please check your connection and try again.';
            }
        } finally {
            checkOutLoading.value = false;
        }
    };

    const applyFilters = () => {
        router.get(route('employee.attendance.index'), {
            month: selectedMonth.value,
            status: selectedStatus.value,
            shift: selectedShift.value,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };

    const formatTime = (dateString) => {
        return dateString ? new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'present':
                return 'bg-green-100 text-green-800';
            case 'absent':
                return 'bg-red-100 text-red-800';
            case 'late':
                return 'bg-yellow-100 text-yellow-800';
            case 'half_day':
                return 'bg-orange-100 text-orange-800';
            case 'checked_in':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getShiftColor = (shift) => {
        switch (shift) {
            case 'morning':
                return 'bg-blue-100 text-blue-800';
            case 'evening':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    // Check location permissions on mount
    onMounted(() => {
        if ('permissions' in navigator) {
            navigator.permissions.query({ name: 'geolocation' }).then((result) => {
                if (result.state === 'denied') {
                    locationError.value = 'Location permission is denied. Please enable it in your browser settings.';
                }
            });
        }
    });
</script>

<template>
    <Head title="My Attendance" />

    <EmployeeLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Attendance</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Today's Attendance & Check-in/out Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Attendance</h3>

                        <!-- Success Message Alert -->
                        <div v-if="successMessage" class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ successMessage }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="successMessage = ''" class="text-green-400 hover:text-green-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Check-in Error Alert -->
                        <div v-if="checkInError" class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ checkInError }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="checkInError = ''" class="text-red-400 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Location Error Alert -->
                        <div v-if="locationError" class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">{{ locationError }}</p>
                                    <div class="mt-2">
                                        <button @click="locationError = ''; checkIn()" class="text-sm text-yellow-800 hover:text-yellow-600 underline">
                                            Try Again
                                        </button>
                                    </div>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="locationError = ''" class="text-yellow-400 hover:text-yellow-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Check-out Error Alert -->
                        <div v-if="checkOutError" class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ checkOutError }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="checkOutError = ''" class="text-red-400 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Current Status -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Current Status</h4>
                                <div v-if="todayAttendance" class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(todayAttendance.status)">
                                            {{ todayAttendance.status }}
                                        </span>
                                    </div>
                                    <div v-if="todayAttendance.shift" class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Shift:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getShiftColor(todayAttendance.shift)">
                                            {{ todayAttendance.shift }}
                                        </span>
                                    </div>
                                    <div v-if="todayAttendance.check_in_time" class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Check In:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatTime(todayAttendance.check_in_time) }}</span>
                                    </div>
                                    <div v-if="todayAttendance.check_out_time" class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Check Out:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatTime(todayAttendance.check_out_time) }}</span>
                                    </div>
                                    <div v-if="todayAttendance.total_hours" class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Total Hours:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ todayAttendance.total_hours }} hrs</span>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <p class="text-sm text-gray-500">No attendance record for today</p>
                                </div>
                            </div>

                            <!-- Check-in/out Actions -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Actions</h4>

                                <!-- Check In Section -->
                                <div v-if="canCheckIn" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Shift</label>
                                        <select v-model="shift" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="full-time">Full Time</option>
                                            <option value="morning">Morning Shift</option>
                                            <option value="evening">Evening Shift</option>
                                        </select>
                                    </div>
                                    <button
                                            @click="checkIn"
                                            :disabled="isLoading || locationLoading"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="isLoading || locationLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ isLoading ? 'Checking In...' : locationLoading ? 'Getting Location...' : 'Check In' }}
                                    </button>
                                </div>

                                <!-- Check Out Section -->
                                <div v-else-if="canCheckOut" class="space-y-3">
                                    <div class="text-center mb-3">
                                        <div class="text-2xl font-bold text-green-600">{{ formatTime(todayAttendance.check_in_time) }}</div>
                                        <div class="text-sm text-gray-500">Checked in</div>
                                    </div>
                                    <button
                                            @click="openCheckOutModal"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Check Out
                                    </button>
                                </div>

                                <!-- Already Complete -->
                                <div v-else-if="todayAttendance && todayAttendance.check_out_time" class="text-center py-4">
                                    <div class="text-green-600 mb-2">
                                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Day Complete!</p>
                                    <p class="text-xs text-gray-500">{{ todayAttendance.total_hours }} hours worked</p>
                                </div>

                                <!-- Not checked in -->
                                <div v-else class="text-center py-4">
                                    <p class="text-sm text-gray-500">Ready to start your day?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Days</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ monthlyStats.total_days }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Present Days</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ monthlyStats.present_days }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Late Days</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ monthlyStats.late_days }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Hours</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ Math.round(monthlyStats.total_hours * 100) / 100 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Attendance History</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                <input
                                        v-model="selectedMonth"
                                        type="month"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        @change="applyFilters"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                        v-model="selectedStatus"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        @change="applyFilters"
                                >
                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="checked_in">Checked In</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                                <select
                                        v-model="selectedShift"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        @change="applyFilters"
                                >
                                    <option value="">All Shifts</option>
                                    <option value="full-time">Full Time</option>
                                    <option value="morning">Morning</option>
                                    <option value="evening">Evening</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button
                                        @click="selectedMonth = new Date().toISOString().slice(0, 7); selectedStatus = ''; selectedShift = ''; applyFilters();"
                                        class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300"
                                >
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Attendance History</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="record in attendance.data" :key="record.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatDate(record.date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                           <span v-if="record.shift" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getShiftColor(record.shift)">
                                               {{ record.shift }}
                                           </span>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatTime(record.check_in_time) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatTime(record.check_out_time) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ record.total_hours ? record.total_hours + ' hrs' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(record.status)">
                                               {{ record.status }}
                                           </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('employee.attendance.show', record.id)" class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="attendance.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No attendance records found for the selected period.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="attendance.data.length > 0" class="mt-6">
                            <Pagination :links="attendance.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check Out Modal -->
        <div v-if="showCheckOutModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCheckOutModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Check Out</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to check out? This will mark the end of your work day.
                                    </p>
                                    <div class="mt-4">
                                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                        <textarea
                                                id="notes"
                                                v-model="notes"
                                                rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Add any notes about your work day..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                                @click="checkOut"
                                :disabled="isLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isLoading ? 'Checking Out...' : 'Check Out' }}
                        </button>
                        <button
                                @click="showCheckOutModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>

<style scoped>
    /* Add mobile responsive styles */
    @media (max-width: 640px) {
        .grid-cols-1.md\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .grid-cols-1.md\:grid-cols-4 {
            grid-template-columns: 1fr;
        }

        .overflow-x-auto table {
            font-size: 0.875rem;
        }
    }
</style>