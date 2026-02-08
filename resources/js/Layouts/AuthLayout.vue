<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 dark:bg-slate-900">
        <div>
            <Link href="/" class="flex flex-col items-center group">
                <ApplicationLogo class="w-20 h-20 fill-current text-indigo-600 group-hover:text-indigo-500 transition-colors" />
                <h1 class="mt-6 text-3xl font-bold text-slate-800 dark:text-white tracking-tight">OmniCore ERP</h1>
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-slate-800 shadow-xl overflow-hidden sm:rounded-lg border border-slate-200 dark:border-slate-700">
            <slot />
        </div>
        
        <div class="mt-8 text-center text-xs text-slate-500">
            &copy; {{ new Date().getFullYear() }} OmniCore ERP. Authorized Personnel Only.
        </div>
    </div>
</template>
