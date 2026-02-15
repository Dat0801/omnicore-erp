<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    suppliers: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters?.current?.search || '');
const status = ref(props.filters?.current?.status || 'all');

const updateFilters = debounce((newSearch, newStatus) => {
    router.get(route('admin.suppliers.index'), {
        search: newSearch,
        status: newStatus,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, status], ([newSearch, newStatus]) => {
    updateFilters(newSearch, newStatus);
});

const initials = (name) => {
    return (name || '')
        .split(' ')
        .filter(Boolean)
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};
</script>

<template>
    <Head title="Supplier Management" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Home / Suppliers</div>
                    <h2 class="text-2xl font-bold text-gray-900">Supplier Management</h2>
                </div>
                <Link :href="route('admin.suppliers.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Supplier
                </Link>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative w-full sm:max-w-md">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-10 pr-3 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Search supplier name, email or phone..."
                            />
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status:</label>
                            <select
                                v-model="status"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs">
                            <span class="font-semibold text-gray-700">{{ stats.total }}</span>
                            <span class="text-gray-500">suppliers</span>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs">
                            <span class="font-semibold text-emerald-700">{{ stats.active }}</span>
                            <span class="text-gray-500">active</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Supplier Name</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Contact</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="s in suppliers.data" :key="s.id" class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
                                            {{ initials(s.name) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                <Link :href="route('admin.suppliers.show', s.id)" class="hover:text-blue-600">
                                                    {{ s.name }}
                                                </Link>
                                            </div>
                                            <div class="text-xs text-gray-500">ID: VEN-{{ String(s.id).padStart(4, '0') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div v-if="s.email">{{ s.email }}</div>
                                        <div class="text-xs text-gray-500" v-if="s.phone">{{ s.phone }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                        :class="s.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'"
                                    >
                                        {{ s.is_active ? 'ACTIVE' : 'SUSPENDED' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.suppliers.show', s.id)"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50"
                                            title="View"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="suppliers.data.length === 0">
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No suppliers found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="suppliers.data.length > 0" class="flex flex-col gap-4 border-top border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ suppliers.from }}</span> to <span class="font-medium">{{ suppliers.to }}</span>
                        of <span class="font-medium">{{ suppliers.total }}</span> suppliers
                    </div>
                    <div class="flex gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in suppliers.links"
                            :key="key"
                            :href="link.url"
                            v-html="link.label"
                            class="rounded-md border px-3 py-1 text-sm transition-colors"
                            :class="{
                                'border-blue-600 bg-blue-600 text-white': link.active,
                                'border-gray-300 bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                'cursor-not-allowed opacity-50': !link.url,
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
