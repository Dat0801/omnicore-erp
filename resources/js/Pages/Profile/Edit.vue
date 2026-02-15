<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const profileFormRef = ref(null);
const showPasswordForm = ref(false);
const language = ref('en_US');
const timezone = ref('UTC');
const theme = ref('light');

const applyTheme = (value) => {
    theme.value = value;
    if (value === 'system') {
        localStorage.removeItem('theme');
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', isDark);
    } else {
        localStorage.setItem('theme', value);
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
};

onMounted(() => {
    const stored = localStorage.getItem('theme');
    theme.value = stored ? stored : 'system';
});
</script>

<template>
    <Head title="Profile" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <span>Settings</span>
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M7.05 3.55L13.5 10l-6.45 6.45-1.414-1.414L10.672 10 5.636 4.964 7.05 3.55z"/></svg>
                <span class="font-medium text-gray-900 dark:text-gray-200">User Profile</span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6">
                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <div class="flex flex-col items-center">
                                <div class="relative h-28 w-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="currentColor"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <p class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $page.props.auth.user.name }}</p>
                                <p class="text-sm font-semibold text-blue-600">SENIOR INVENTORY MANAGER</p>
                                <p class="mt-1 text-xs text-gray-500">Operations & Logistics Dept.</p>
                                <div class="mt-4">
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        <input type="file" class="hidden" />
                                        <span>Change Avatar</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">System Activity</h3>
                            <div class="mt-4 space-y-4 text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8v4l3 3 1-1-2.5-2.5V8z"/><path d="M12 1a11 11 0 100 22 11 11 0 000-22zm0 2a9 9 0 110 18 9 9 0 010-18z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Last login</p>
                                        <p class="text-gray-500">Today, 09:42 AM</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v12H4z"/><path d="M2 18h20v2H2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Device</p>
                                        <p class="text-gray-500">MacBook Pro - Chrome</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Personal Information</h3>
                            <UpdateProfileInformationForm
                                ref="profileFormRef"
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                :hide-header="true"
                                :hide-actions="true"
                                class="mt-6"
                            />
                        </div>

                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Account Security</h3>
                            <div class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-4 flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Account Password</p>
                                        <p class="text-sm text-gray-500">Change your password to keep your account secure</p>
                                    </div>
                                    <button class="text-sm font-medium text-blue-600 hover:text-blue-700" @click="showPasswordForm = !showPasswordForm">Change Password</button>
                                </div>
                                <div v-if="showPasswordForm" class="pt-4">
                                    <UpdatePasswordForm class="max-w-xl" />
                                </div>
                                <div class="py-4 flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 100 20 10 10 0 000-20z" fill-opacity=".1"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">Two-Factor Authentication</p>
                                            <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1">ENABLED</span>
                                        <button class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">Settings</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Preferences</h3>
                            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Language</label>
                                    <select v-model="language" class="mt-2 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="en_US">English (United States)</option>
                                        <option value="vi_VN">Tiếng Việt</option>
                                        <option value="ja_JP">日本語</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                                    <select v-model="timezone" class="mt-2 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="UTC">(UTC+00:00) UTC</option>
                                        <option value="Asia/Ho_Chi_Minh">(UTC+07:00) Asia/Ho_Chi_Minh</option>
                                        <option value="America/New_York">(UTC-05:00) Eastern Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface Theme</label>
                                <div class="mt-3 grid grid-cols-3 gap-3">
                                    <button
                                        @click="applyTheme('light')"
                                        :class="['rounded-xl border p-4 text-sm', theme==='light' ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-400/30' : 'border-gray-200 dark:border-gray-700']"
                                    >
                                        Light
                                    </button>
                                    <button
                                        @click="applyTheme('dark')"
                                        :class="['rounded-xl border p-4 text-sm', theme==='dark' ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-400/30' : 'border-gray-200 dark:border-gray-700']"
                                    >
                                        Dark
                                    </button>
                                    <button
                                        @click="applyTheme('system')"
                                        :class="['rounded-xl border p-4 text-sm', theme==='system' ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-400/30' : 'border-gray-200 dark:border-gray-700']"
                                    >
                                        System
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <button class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300" @click="location.reload()">Discard Changes</button>
                            <button
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-60"
                                :disabled="profileFormRef?.form?.processing"
                                @click="profileFormRef?.submit()"
                            >
                                Update Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
