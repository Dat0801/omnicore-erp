<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Forgot Password" />

        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 border border-gray-100 text-center">
            <!-- Icon -->
            <div class="mx-auto w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
            
            <p class="mb-8 text-sm text-gray-500 leading-relaxed">
                Enter your work email address and we'll send you a link to reset your password and regain access to the portal.
            </p>

            <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-100 rounded-lg flex items-start gap-3 text-left">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-green-700">
                    {{ status }}
                </div>
            </div>

            <form @submit.prevent="submit" class="text-left">
                <div class="mb-6">
                    <InputLabel for="email" value="Email Address" class="mb-2 font-semibold text-gray-900" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            class="block w-full pl-10 bg-gray-50 border-gray-200 focus:bg-white focus:ring-blue-500 focus:border-blue-500 rounded-md transition-colors py-2.5 shadow-sm sm:text-sm"
                            v-model="form.email"
                            required
                            autofocus
                            placeholder="admin@company.com"
                            autocomplete="username"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500 rounded-lg text-base font-semibold transition-all shadow-md hover:shadow-lg"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Send Reset Link
                        <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </PrimaryButton>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <Link :href="route('login')" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Login
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>
