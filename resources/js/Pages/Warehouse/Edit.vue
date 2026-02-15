<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    warehouse: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.warehouse.name,
    code: props.warehouse.code,
    address: props.warehouse.address || '',
    is_active: Boolean(props.warehouse.is_active),
});

const submit = () => {
    form.put(route('admin.warehouses.update', props.warehouse.id));
};

const destroy = () => {
    if (confirm('Are you sure you want to delete this warehouse?')) {
        router.delete(route('admin.warehouses.destroy', props.warehouse.id));
    }
};
</script>

<template>
    <Head title="Edit Warehouse" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                        <Link :href="route('dashboard')" class="hover:text-gray-700 dark:hover:text-gray-200">Dashboard</Link>
                        <span>/</span>
                        <Link :href="route('admin.warehouses.index')" class="hover:text-gray-700 dark:hover:text-gray-200">Warehouses</Link>
                        <span>/</span>
                        <span class="text-gray-900 dark:text-gray-200">Edit</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Warehouse: {{ props.warehouse.name }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.warehouses.index')"
                        class="rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    >
                        Cancel
                    </Link>
                    <button
                        type="button"
                        @click="destroy"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                    >
                        Delete
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
                    >
                        Update Warehouse
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-6 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">i</span>
                            Basic Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="warehouse_name">
                                    Warehouse Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="warehouse_name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="warehouse_code">
                                    Warehouse Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="warehouse_code"
                                    v-model="form.code"
                                    type="text"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <div v-if="form.errors.code" class="mt-1 text-xs text-red-500">{{ form.errors.code }}</div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="warehouse_address">
                                    Address
                                </label>
                                <textarea
                                    id="warehouse_address"
                                    v-model="form.address"
                                    rows="3"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                <div v-if="form.errors.address" class="mt-1 text-xs text-red-500">{{ form.errors.address }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            Status
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
                                <input id="is_active" v-model="form.is_active" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200" for="is_active">Active</label>
                                    <p class="text-xs text-gray-500">Inactive warehouses cannot receive new stock.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
