<!-- resources/js/Layouts/AdminLayout.vue -->
<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

    const showingNavigationDropdown = ref(false);
    const showingMasterDataDropdown = ref(false);

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
        if (!event.target.closest('.relative')) {
            showingMasterDataDropdown.value = false;
        }
    };

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
    });

    const user = usePage().props.auth.user;
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100 shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('admin.dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-blue-600" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                                    </svg>
                                    Dashboard
                                </NavLink>

                                <NavLink :href="route('admin.employees.index')" :active="route().current('admin.employees.*')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Employees
                                </NavLink>

                                <!-- Master Data Dropdown -->
                                <div class="relative inline-flex items-center">
                                    <button
                                            @click="showingMasterDataDropdown = !showingMasterDataDropdown"
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none h-16"
                                            :class="{
            'border-indigo-400 text-gray-900 focus:border-indigo-700': route().current('admin.departments.*') || route().current('admin.locations.*') || route().current('admin.designations.*') || route().current('admin.facilities.*') || route().current('admin.assets.*'),
            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300': !(route().current('admin.departments.*') || route().current('admin.locations.*') || route().current('admin.designations.*') || route().current('admin.facilities.*') || route().current('admin.assets.*'))
        }"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        Master Data
                                        <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{ 'transform rotate-180': showingMasterDataDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div
                                            v-show="showingMasterDataDropdown"
                                            class="absolute top-full left-0 mt-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                            @click.stop="showingMasterDataDropdown = false"
                                    >
                                        <div class="py-1">
                                            <Link :href="route('admin.departments.index')"
                                                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out"
                                                  :class="{ 'bg-indigo-50 text-indigo-900 border-r-2 border-indigo-500': route().current('admin.departments.*') }"
                                                  @click="showingMasterDataDropdown = false">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Departments
                                            </Link>

                                            <Link :href="route('admin.locations.index')"
                                                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out"
                                                  :class="{ 'bg-indigo-50 text-indigo-900 border-r-2 border-indigo-500': route().current('admin.locations.*') }"
                                                  @click="showingMasterDataDropdown = false">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Locations
                                            </Link>

                                            <Link :href="route('admin.designations.index')"
                                                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out"
                                                  :class="{ 'bg-indigo-50 text-indigo-900 border-r-2 border-indigo-500': route().current('admin.designations.*') }"
                                                  @click="showingMasterDataDropdown = false">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Designations
                                            </Link>

                                            <Link :href="route('admin.facilities.index')"
                                                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out"
                                                  :class="{ 'bg-indigo-50 text-indigo-900 border-r-2 border-indigo-500': route().current('admin.facilities.*') }"
                                                  @click="showingMasterDataDropdown = false">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Facilities
                                            </Link>

                                            <div class="border-t border-gray-100 my-1"></div>

                                            <Link :href="route('admin.assets.index')"
                                                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out"
                                                  :class="{ 'bg-indigo-50 text-indigo-900 border-r-2 border-indigo-500': route().current('admin.assets.*') }"
                                                  @click="showingMasterDataDropdown = false">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                                </svg>
                                                Assets
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <NavLink :href="route('admin.attendance.index')" :active="route().current('admin.attendance.*')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Attendance
                                </NavLink>

                                <NavLink :href="route('admin.leave.index')" :active="route().current('admin.leave.*')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Leave Management
                                </NavLink>

                                <NavLink :href="route('admin.payroll.index')" :active="route().current('admin.payroll.*')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Payroll
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                                                    <span class="text-white text-sm font-semibold">
                                                        {{ user.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                                {{ user.name }}
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="px-4 py-2 text-center border-b border-gray-100">
                                            <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full inline-flex items-center justify-center font-medium">Admin</span>
                                        </div>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                            :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                            :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                        :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                        class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.employees.index')" :active="route().current('admin.employees.*')">
                            Employees
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.departments.index')" :active="route().current('admin.departments.*')">
                            Departments
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.locations.index')" :active="route().current('admin.locations.*')">
                            Locations
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.designations.index')" :active="route().current('admin.designations.*')">
                            Designations
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.facilities.index')" :active="route().current('admin.facilities.*')">
                            Facilities
                        </ResponsiveNavLink>

                        <ResponsiveNavLink :href="route('admin.assets.index')" :active="route().current('admin.assets.*')">
                            Assets
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.attendance.index')" :active="route().current('admin.attendance.*')">
                            Attendance
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.leave.index')" :active="route().current('admin.leave.*')">
                            Leave Management
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.payroll.index')" :active="route().current('admin.payroll.*')">
                            Payroll
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>