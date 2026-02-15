<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('admin.roles.store'));
};
</script>

<template>
    <Head title="Create Role" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">
                        Dashboard / Role Management / Create
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Create Role
                    </h2>
                    <p class="text-sm text-gray-500">
                        Define a new access role to use across users and modules.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.roles.index')"
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
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Role
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-6 text-sm font-semibold text-gray-900">
                            Role Details
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="role_name">
                                    Role Name
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="role_name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. warehouse_manager"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">
                                    {{ form.errors.name }}
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Use a short, machine-friendly key. Display labels are generated automatically.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900">
                            Guidance
                        </h3>
                        <p class="text-xs text-gray-500">
                            After creating a role, you will be redirected to the permission
                            matrix where you can assign granular capabilities for each module.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

