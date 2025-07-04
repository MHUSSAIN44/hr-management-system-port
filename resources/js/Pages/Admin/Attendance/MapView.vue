// resources/js/Pages/Admin/Attendance/MapView.vue
<script setup>
    import { ref, onMounted, nextTick } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';

    const props = defineProps({
        attendance: Array,
        locations: Array,
        filters: Object,
    });

    const mapContainer = ref(null);
    const map = ref(null);
    const markers = ref([]);
    const selectedDate = ref(props.filters.date || new Date().toISOString().split('T')[0]);
    const selectedLocation = ref(props.filters.location_id || '');

    onMounted(async () => {
        await loadGoogleMaps();
        initializeMap();
    });

    const loadGoogleMaps = () => {
        return new Promise((resolve) => {
            if (window.google && window.google.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry`;
            script.async = true;
            script.defer = true;
            script.onload = resolve;
            document.head.appendChild(script);
        });
    };

    const initializeMap = () => {
        if (!window.google || !mapContainer.value) return;

        // Default center (you can adjust this)
        const defaultCenter = { lat: 25.2048, lng: 55.2708 }; // Dubai coordinates

        map.value = new google.maps.Map(mapContainer.value, {
            zoom: 11,
            center: defaultCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });

        addMarkersToMap();
    };

    const addMarkersToMap = () => {
        if (!map.value) return;

        // Clear existing markers
        markers.value.forEach(marker => marker.setMap(null));
        markers.value = [];

        const bounds = new google.maps.LatLngBounds();

        // Add office location markers
        props.locations.forEach(location => {
            if (location.latitude && location.longitude) {
                const officeMarker = new google.maps.Marker({
                    position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                    map: map.value,
                    title: `Office: ${location.name}`,
                    icon: {
                        url: 'data:image/svg+xml;base64,' + btoa(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#3B82F6" width="32" height="32">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    `),
                        scaledSize: new google.maps.Size(32, 32),
                        anchor: new google.maps.Point(16, 32)
                    }
                });

                // Add radius circle for office locations
                const circle = new google.maps.Circle({
                    strokeColor: '#3B82F6',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#3B82F6',
                    fillOpacity: 0.1,
                    map: map.value,
                    center: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                    radius: location.allowed_radius || 100
                });

                const officeInfoWindow = new google.maps.InfoWindow({
                    content: `
                    <div class="p-2">
                        <h3 class="font-semibold text-blue-800">${location.name}</h3>
                        <p class="text-sm text-gray-600">Office Location</p>
                        <p class="text-xs text-gray-500">Allowed radius: ${location.allowed_radius || 100}m</p>
                    </div>
                `
                });

                officeMarker.addListener('click', () => {
                    officeInfoWindow.open(map.value, officeMarker);
                });

                markers.value.push(officeMarker);
                bounds.extend({ lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) });
            }
        });

        // Add attendance markers
        props.attendance.forEach(record => {
            if (record.check_in_location) {
                // Check-in marker
                const checkInMarker = new google.maps.Marker({
                    position: record.check_in_location,
                    map: map.value,
                    title: `${record.employee_name} - Check In`,
                    icon: {
                        url: 'data:image/svg+xml;base64,' + btoa(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981" width="24" height="24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    `),
                        scaledSize: new google.maps.Size(24, 24),
                        anchor: new google.maps.Point(12, 24)
                    }
                });

                const checkInInfoWindow = new google.maps.InfoWindow({
                    content: `
                    <div class="p-3 min-w-64">
                        <h3 class="font-semibold text-green-800">${record.employee_name}</h3>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm"><span class="font-medium">Check In:</span> ${record.check_in_time}</p>
                            <p class="text-sm"><span class="font-medium">Shift:</span> ${record.shift}</p>
                            <p class="text-sm"><span class="font-medium">Status:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-${getStatusColor(record.status)}-100 text-${getStatusColor(record.status)}-800">
                                    ${record.status}
                                </span>
                            </p>
                            <p class="text-xs text-gray-600">${record.check_in_location.address}</p>
                        </div>
                    </div>
                `
                });

                checkInMarker.addListener('click', () => {
                    checkInInfoWindow.open(map.value, checkInMarker);
                });

                markers.value.push(checkInMarker);
                bounds.extend(record.check_in_location);
            }

            if (record.check_out_location) {
                // Check-out marker
                const checkOutMarker = new google.maps.Marker({
                    position: record.check_out_location,
                    map: map.value,
                    title: `${record.employee_name} - Check Out`,
                    icon: {
                        url: 'data:image/svg+xml;base64,' + btoa(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#EF4444" width="24" height="24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    `),
                        scaledSize: new google.maps.Size(24, 24),
                        anchor: new google.maps.Point(12, 24)
                    }
                });

                const checkOutInfoWindow = new google.maps.InfoWindow({
                    content: `
                    <div class="p-3 min-w-64">
                        <h3 class="font-semibold text-red-800">${record.employee_name}</h3>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm"><span class="font-medium">Check Out:</span> ${record.check_out_time}</p>
                            <p class="text-sm"><span class="font-medium">Total Hours:</span> ${record.total_hours || 'N/A'}</p>
                            <p class="text-xs text-gray-600">${record.check_out_location.address}</p>
                        </div>
                    </div>
                `
                });

                checkOutMarker.addListener('click', () => {
                    checkOutInfoWindow.open(map.value, checkOutMarker);
                });

                markers.value.push(checkOutMarker);
                bounds.extend(record.check_out_location);
            }

            // Connect check-in and check-out with a line if both exist
            if (record.check_in_location && record.check_out_location) {
                const path = new google.maps.Polyline({
                    path: [record.check_in_location, record.check_out_location],
                    geodesic: true,
                    strokeColor: '#6B7280',
                    strokeOpacity: 0.6,
                    strokeWeight: 2,
                    map: map.value
                });
            }
        });

        // Fit map to show all markers
        if (!bounds.isEmpty()) {
            map.value.fitBounds(bounds);

            // Ensure minimum zoom level
            const listener = google.maps.event.addListener(map.value, 'idle', () => {
                if (map.value.getZoom() > 15) map.value.setZoom(15);
                google.maps.event.removeListener(listener);
            });
        }
    };

    const applyFilters = () => {
        router.get(route('admin.attendance.map'), {
            date: selectedDate.value,
            location_id: selectedLocation.value,
        }, {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                nextTick(() => {
                    addMarkersToMap();
                });
            }
        });
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'present': return 'green';
            case 'absent': return 'red';
            case 'late': return 'yellow';
            case 'half_day': return 'orange';
            case 'checked_in': return 'blue';
            default: return 'gray';
        }
    };
</script>

<template>
    <Head title="Attendance Map View" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance Map View</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.attendance.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        List View
                    </Link>
                    <Link :href="route('admin.attendance.live-tracking')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Live Tracking
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Map Filters</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input
                                        v-model="selectedDate"
                                        type="date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        @change="applyFilters"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select
                                        v-model="selectedLocation"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        @change="applyFilters"
                                >
                                    <option value="">All Locations</option>
                                    <option v-for="location in locations" :key="location.id" :value="location.id">
                                        {{ location.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="md:col-span-2 flex items-end space-x-2">
                                <button
                                        @click="selectedDate = new Date().toISOString().split('T')[0]; selectedLocation = ''; applyFilters();"
                                        class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300"
                                >
                                    Reset Filters
                                </button>
                                <button
                                        @click="addMarkersToMap"
                                        class="px-4 py-2 bg-blue-600 rounded-md text-xs font-semibold text-white hover:bg-blue-700"
                                >
                                    Refresh Map
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Legend -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Map Legend</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-blue-500 rounded-full mr-2 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">Office Locations</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-green-500 rounded-full mr-2 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">Check-in Locations</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-red-500 rounded-full mr-2 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">Check-out Locations</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-0.5 bg-gray-500 mr-2"></div>
                                <span class="text-sm text-gray-700">Employee Movement</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                                ref="mapContainer"
                                class="w-full h-96 md:h-[600px] rounded-lg border border-gray-300"
                                style="min-height: 400px;"
                        >
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-600 mt-2">Loading map...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-blue-500">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Records</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ attendance.length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                        <div class="p-4">
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">Check-ins</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ attendance.filter(a => a.check_in_location).length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Check-outs</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ attendance.filter(a => a.check_out_location).length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-purple-500">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Office Locations</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ locations.length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
    /* Ensure map container takes full height */
    .google-map {
        height: 100%;
        width: 100%;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        .grid-cols-1.md\:grid-cols-4 {
            grid-template-columns: 1fr;
        }
    }
</style>