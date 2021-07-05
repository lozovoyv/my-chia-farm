<template>
    <div>
        <div class="min-h-screen bg-gray-300 dark:bg-gray-800">
            <nav class="bg-white border-b border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16">
                        <!-- Navigation Links -->
                        <div class="flex flex-1">
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <nav-link :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</nav-link>
                                <nav-link :href="route('jobs')" :active="route().current('jobs')">Jobs</nav-link>
                                <nav-link :href="route('settings')" :active="route().current('settings')">Settings</nav-link>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative whitespace-nowrap">
                                <dropdown class="inline-block whitespace-normal" align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <dropdown-link :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </dropdown-link>
                                    </template>
                                </dropdown>
                            </div>
                        </div>

                        <!-- Dark mode switch -->
                        <theme-switch class="flex items-center px-4"></theme-switch>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 focus:outline-none transition duration-150 ease-in-out text-gray-500 hover:text-gray-600 focus:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 dark:focus:text-gray-200">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <nav-link :href="route('dashboard')" :active="route().current('dashboard')" :responsive="true">Dashboard</nav-link>
                        <nav-link :href="route('jobs')" :active="route().current('jobs')" :responsive="true">Jobs</nav-link>
                        <nav-link :href="route('settings')" :active="route().current('settings')" :responsive="true">Settings</nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-400">{{ $page.props.auth.user.name }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <nav-link :href="route('logout')" method="post" as="button" :responsive="true">Log Out</nav-link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-700 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="py-0.5">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
    import ThemeSwitch from "@/Components/Buttons/ThemeSwitch";
    import NavLink from "@/Components/Buttons/NavLink";
    import Dropdown from '@/Components/Dropdown'
    import DropdownLink from '@/Components/DropdownLink'

    export default {
        components: {
            ThemeSwitch,
            NavLink,
            Dropdown,
            DropdownLink,
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },
    }
</script>
