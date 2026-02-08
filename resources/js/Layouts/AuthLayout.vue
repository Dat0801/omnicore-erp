<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50 font-sans text-gray-900">
        <!-- Top Navigation -->
        <nav class="bg-white px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 rounded-md p-1.5 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight text-gray-900">OMNICORE</span>
            </div>
            <div class="text-gray-500 text-sm flex items-center gap-2 hover:text-gray-700 cursor-pointer">
                <svg class="w-4 h-4 bg-gray-500 text-white rounded-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">System Support</span>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center p-4">
            <div class="w-full max-w-[480px] bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 border border-gray-100">
                <slot />
            </div>

            <!-- Secure Access Badge -->
            <div class="mt-12 flex flex-col items-center">
                <div class="flex items-center gap-2 mb-3 text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                    </svg>
                    <span class="text-xs font-bold tracking-[0.2em] uppercase">Secure Access Only</span>
                </div>
                <p class="text-center text-gray-400 text-[10px] leading-relaxed max-w-xs">
                    This is a private computer system for authorized personnel only.
                    All activities are logged and monitored.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400">
            <div>&copy; 2024 OmniCore ERP Systems. All rights reserved.</div>
            <div class="flex gap-6 mt-2 sm:mt-0">
                <a href="#" class="hover:text-gray-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-gray-600 transition-colors">Security Audit</a>
                <span class="text-gray-300">V1.4.2-stable</span>
            </div>
        </footer>
    </div>
</template>
