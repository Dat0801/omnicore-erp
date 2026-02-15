<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    tax_id: '',
    address: '',
    is_active: true,
});

function submit() {
    form.post(route('admin.suppliers.store'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Add Supplier" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">Home / Suppliers</div>
                    <h2 class="mt-1 text-2xl font-bold text-gray-900">Add Supplier</h2>
                </div>
                <Link :href="route('admin.suppliers.index')" class="text-sm font-semibold text-gray-600 hover:text-gray-800">
                    Cancel
                </Link>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Supplier Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Phone</label>
                        <input v-model="form.phone" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Tax ID</label>
                        <input v-model="form.tax_id" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-sm font-medium text-gray-700">Address</label>
                        <textarea v-model="form.address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-gray-700">Active</label>
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    </div>
                </div>
                <div class="mt-6">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-60"
                        :disabled="form.processing"
                        @click="submit"
                    >
                        Save Supplier
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
    </template>
