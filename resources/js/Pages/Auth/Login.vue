<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Admin Portal Access</h2>
            <p class="text-sm text-gray-500 mt-2">Please enter your credentials to manage the ERP.</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Address" class="font-semibold text-gray-700" />

                <div class="relative mt-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full pl-10 border-gray-200 bg-white focus:bg-white focus:ring-blue-500 focus:border-blue-500 rounded-lg py-2.5 dark:bg-white dark:text-gray-900 dark:border-gray-200"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="e.g., admin@omnicore.com"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" class="font-semibold text-gray-700" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500"
                    >
                        Forgot password?
                    </Link>
                </div>

                <div class="relative mt-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full pl-10 border-gray-200 bg-white focus:bg-white focus:ring-blue-500 focus:border-blue-500 rounded-lg py-2.5 dark:bg-white dark:text-gray-900 dark:border-gray-200"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-6">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-blue-600 rounded border-gray-300 focus:ring-blue-500 bg-white dark:bg-white dark:border-gray-300 dark:focus:ring-offset-white" />
                    <span class="ml-2 text-sm text-gray-600">Remember this device for 30 days</span>
                </label>
            </div>

            <div class="mt-8">
                <PrimaryButton
                    class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 rounded-lg text-base font-semibold shadow-sm transition-all duration-200"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing In...
                    </span>
                    <span v-else class="flex items-center gap-2">
                        Sign In to Dashboard
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
