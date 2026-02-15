<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.roles[0]?.value ?? 'staff',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="Create User" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Dashboard / User Management / Create</div>
                    <h2 class="text-2xl font-bold text-gray-900">Create User</h2>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.users.index')"
                        class="rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Cancel
                    </Link>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
                    >
                        Create User
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-6 text-sm font-semibold text-gray-900">
                            Basic Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="user_name">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="user_name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. Alex Nguyen"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="user_email">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="user_email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="user@example.com"
                                />
                                <div v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700" for="user_password">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="user_password"
                                        v-model="form.password"
                                        type="password"
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="At least 8 characters"
                                    />
                                    <div v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700" for="user_password_confirmation">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="user_password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900">
                            Role & Status
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="user_role">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="user_role"
                                    v-model="form.role"
                                    class="block w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="r in roles" :key="r.value" :value="r.value">
                                        {{ r.label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</div>
                            </div>

                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3">
                                <input
                                    id="user_is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700" for="user_is_active">Active</label>
                                    <p class="text-xs text-gray-500">Inactive users cannot sign in to the system.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

