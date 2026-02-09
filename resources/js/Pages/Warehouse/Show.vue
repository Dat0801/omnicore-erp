<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    warehouse: Object,
    inventories: Object,
    filters: Object,
    metrics: Object,
});

const search = ref(props.filters?.current?.search || '');
const status = ref(props.filters?.current?.status || 'all');
const categoryId = ref(props.filters?.current?.category_id || 'all');

const updateFilters = debounce((newSearch, newStatus, newCategoryId) => {
    router.get(route('admin.warehouses.show', props.warehouse.id), {
        search: newSearch,
        status: newStatus,
        category_id: newCategoryId === 'all' ? null : newCategoryId,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, status, categoryId], ([newSearch, newStatus, newCategoryId]) => {
    updateFilters(newSearch, newStatus, newCategoryId);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-US').format(value || 0);
};

const statusLabel = (item) => {
    if (item.quantity === 0) {
        return 'Out of Stock';
    }

    if (item.quantity <= item.reorder_level) {
        return 'Low Stock';
    }

    return 'In Stock';
};

const statusClasses = (item) => {
    if (item.quantity === 0) {
        return 'bg-red-50 text-red-700 border-red-200';
    }

    if (item.quantity <= item.reorder_level) {
        return 'bg-orange-50 text-orange-700 border-orange-200';
    }

    return 'bg-emerald-50 text-emerald-700 border-emerald-200';
};
</script>

<template>
    <Head :title="`${warehouse.name} Warehouse`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                        <Link :href="route('dashboard')" class="hover:text-gray-700">Dashboard</Link>
                        <span>/</span>
                        <Link :href="route('admin.warehouses.index')" class="hover:text-gray-700">Warehouses</Link>
                        <span>/</span>
                        <span class="text-gray-900">{{ warehouse.code }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ warehouse.name }}</h1>
                        <span
                            class="rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                            :class="warehouse.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-100 text-gray-600'"
                        >
                            {{ warehouse.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ warehouse.address || 'Address not set' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 2h8a2 2 0 012 2v3H4V4a2 2 0 012-2z" />
                            <path d="M4 9h12v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9z" />
                            <path d="M6 13h8v2H6z" />
                        </svg>
                        Print Report
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 13V3h12v10H4z" />
                            <path d="M2 17h16v2H2z" />
                        </svg>
                        Edit Warehouse
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-1h2v1zm0-3H9V5h2v5z" />
                                </svg>
                            </span>
                            General Information
                        </div>
                        <div class="mt-6 grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Manager</p>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
                                        {{ (warehouse.manager_name || 'UN').split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ warehouse.manager_name || 'Unassigned' }}</p>
                                        <p class="text-xs text-gray-500">{{ warehouse.manager_email || 'No email set' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Contact Email</p>
                                <p class="text-sm text-gray-900">{{ warehouse.manager_email || 'Not provided' }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Physical Address</p>
                                <p class="text-sm text-gray-900">{{ warehouse.address || 'Address not set' }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Phone Number</p>
                                <p class="text-sm text-gray-900">{{ warehouse.manager_phone || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 3h14v3H3V3z" />
                                <path d="M3 8h14v9H3V8z" />
                            </svg>
                        </span>
                        Warehouse Metrics
                    </div>
                    <div class="mt-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total SKUs</p>
                                <p class="text-2xl font-bold text-gray-900">{{ formatNumber(metrics.totalSkus) }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 3h12v4H4V3z" />
                                    <path d="M4 9h12v8H4V9z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold text-gray-400">
                                <span>Capacity Utilization</span>
                                <span class="text-gray-600">{{ metrics.utilization }}%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-blue-600" :style="{ width: `${metrics.utilization}%` }"></div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ formatNumber(metrics.totalItems) }} items on hand</p>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inventory Value</p>
                                <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(metrics.stockValue) }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">Healthy</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg border border-gray-100 bg-white p-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Incoming</p>
                                <p class="text-lg font-semibold text-emerald-600">+{{ formatNumber(metrics.incomingToday) }}</p>
                            </div>
                            <div class="rounded-lg border border-gray-100 bg-white p-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Outgoing</p>
                                <p class="text-lg font-semibold text-rose-600">-{{ formatNumber(metrics.outgoingToday) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Stored Inventory</h2>
                        <p class="text-sm text-gray-500">Live stock for {{ warehouse.name }}</p>
                    </div>
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <div class="relative w-full sm:max-w-xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Search inventory..."
                            />
                        </div>
                        <select v-model="categoryId" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <option value="all">All Categories</option>
                            <option v-for="category in filters.categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <select v-model="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <option value="all">All Statuses</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">SKU ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product Name</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in inventories.data" :key="item.id" class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ item.product.code }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ item.product.name }}</p>
                                        <p class="text-xs text-gray-500">Reorder at {{ formatNumber(item.reorder_level) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ item.product.category?.name || 'Uncategorized' }}</td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ formatNumber(item.quantity) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold" :class="statusClasses(item)">
                                        {{ statusLabel(item) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="inventories.data.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No inventory items found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="inventories.data.length > 0" class="flex flex-col gap-4 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ inventories.from }}</span> to <span class="font-medium">{{ inventories.to }}</span>
                        of <span class="font-medium">{{ inventories.total }}</span> items
                    </div>
                    <div class="flex gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in inventories.links"
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

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Warehouse Location</h2>
                    <button class="text-xs font-semibold text-blue-600 hover:text-blue-700">Open in Maps</button>
                </div>
                <div class="mt-4 h-44 overflow-hidden rounded-xl bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100">
                    <div class="relative h-full w-full">
                        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 25% 35%, #d4d4d8 0, #e5e7eb 40%, transparent 70%);"></div>
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6c0 4.2 6 10 6 10s6-5.8 6-10a6 6 0 00-6-6zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
