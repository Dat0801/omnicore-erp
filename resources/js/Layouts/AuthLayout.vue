<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] font-sans text-gray-900 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-gray-50 to-gray-50">
        <!-- Header -->
        <header class="w-full px-8 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 rounded-lg p-1.5 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-900">ERP Admin</span>
            </div>
            <div class="text-gray-500 text-sm">
                Need help? <a href="#" class="text-blue-600 font-medium hover:underline">Support</a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center p-4">
            <div class="w-full max-w-[480px]">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full px-8 py-6 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400">
            <div>&copy; 2024 ERP Admin Portal</div>
            <div class="flex gap-6 mt-2 sm:mt-0">
                <a href="#" class="hover:text-gray-600 transition-colors">Privacy</a>
                <a href="#" class="hover:text-gray-600 transition-colors">Terms</a>
            </div>
        </footer>
    </div>
</template>
