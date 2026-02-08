<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarLink from '@/Components/SidebarLink.vue';

const showingSidebar = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Mobile Sidebar Backdrop -->
        <div 
            v-if="showingSidebar" 
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 transition-opacity lg:hidden"
            @click="showingSidebar = false"
        ></div>

        <!-- Sidebar -->
        <div 
            :class="[
                showingSidebar ? 'translate-x-0' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-50 w-64 transform bg-white dark:bg-gray-800 shadow-lg transition-transform duration-300 ease-in-out lg:translate-x-0 lg:border-r lg:border-gray-200 dark:lg:border-gray-700'
            ]"
        >
            <div class="flex h-16 items-center justify-center border-b border-gray-200 dark:border-gray-700 px-6">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                </Link>
            </div>

            <nav class="mt-5 space-y-1 px-2">
                <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Dashboard
                </SidebarLink>
                
                <SidebarLink :href="route('admin.products.index')" :active="route().current('admin.products.*')">
                    Products
                </SidebarLink>
                
                <!-- Placeholders for future modules -->
                <SidebarLink href="#" :active="false">
                    Inventory
                </SidebarLink>
                
                <SidebarLink href="#" :active="false">
                    Orders
                </SidebarLink>
                
                <SidebarLink href="#" :active="false">
                    Warehouses
                </SidebarLink>
                
                <SidebarLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                    Settings
                </SidebarLink>
            </nav>
        </div>

        <!-- Main Content Column -->
        <div class="flex flex-1 flex-col lg:pl-64 min-h-screen">
            <!-- Top Header -->
            <header class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white dark:bg-gray-800 shadow">
                <button
                    type="button"
                    class="border-r border-gray-200 dark:border-gray-700 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden"
                    @click="showingSidebar = true"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-1">
                        <!-- Spacer or Search Bar could go here -->
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Profile Dropdown -->
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 py-6">
                 <!-- Optional Page Header -->
                 <div v-if="$slots.header" class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 mb-6">
                     <slot name="header" />
                 </div>
                 
                 <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                     <slot />
                 </div>
            </main>
        </div>
    </div>
</template>
