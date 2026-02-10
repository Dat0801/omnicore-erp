<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    warehouses: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters?.current?.search || '');
const status = ref(props.filters?.current?.status || 'all');

const updateFilters = debounce((newSearch, newStatus) => {
    router.get(route('admin.warehouses.index'), {
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

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};

const managerName = (warehouse) => warehouse.manager_name ?? 'Unassigned';

const managerInitials = (name) => {
    if (!name) {
        return '?';
    }

    return name
        .split(' ')
        .filter(Boolean)
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};
</script>

<template>
    <Head title="Warehouse Management" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Home / Warehouses</div>
                    <h2 class="text-2xl font-bold text-gray-900">Warehouse Management</h2>
                </div>
                <Link :href="route('admin.warehouses.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Warehouse
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
                                placeholder="Search by name, code or location..."
                            />
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status:</label>
                            <select
                                v-model="status"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 14a1 1 0 011-1h3v2H5v2h10v-2h-2v-2h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3z" />
                                <path d="M7 9l3 3 3-3" />
                                <path d="M10 12V3" />
                            </svg>
                        </button>
                        <button class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 10a7 7 0 0112.906-4.108l.699-.698a1 1 0 111.414 1.414l-2.12 2.12a1 1 0 01-1.415 0l-2.12-2.12a1 1 0 011.414-1.414l.71.71A5 5 0 105 10a1 1 0 11-2 0z" clip-rule="evenodd" />
                                <path d="M17 10a1 1 0 112 0 7 7 0 01-12.906 4.108l-.699.698a1 1 0 11-1.414-1.414l2.12-2.12a1 1 0 011.415 0l2.12 2.12a1 1 0 11-1.414 1.414l-.71-.71A5 5 0 0017 10z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Warehouse Name</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Code</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Location</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Manager</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Stock Value</th>
                                <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="warehouse in warehouses.data" :key="warehouse.id" class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <Link :href="route('admin.warehouses.show', warehouse.id)" class="hover:text-blue-600">
                                                {{ warehouse.name }}
                                            </Link>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ warehouse.is_active ? 'Operational Site' : 'Inactive Site' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">{{ warehouse.code }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ warehouse.address || 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">
                                            {{ managerInitials(managerName(warehouse)) }}
                                        </div>
                                        <div class="text-sm text-gray-600">{{ managerName(warehouse) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors"
                                            :class="warehouse.is_active ? 'bg-emerald-500' : 'bg-gray-200'"
                                        >
                                            <span
                                                aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                                                :class="warehouse.is_active ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ warehouse.is_active ? 'Active' : 'Inactive' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ formatCurrency(warehouse.stock_value) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.warehouses.show', warehouse.id)"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50"
                                            title="View Details"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.warehouses.edit', warehouse.id)"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-blue-600 transition hover:bg-blue-50"
                                            title="Edit Warehouse"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="warehouses.data.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No warehouses found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="warehouses.data.length > 0" class="flex flex-col gap-4 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ warehouses.from }}</span> to <span class="font-medium">{{ warehouses.to }}</span>
                        of <span class="font-medium">{{ warehouses.total }}</span> results
                    </div>
                    <div class="flex gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in warehouses.links"
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

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Warehouses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalWarehouses }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Asset Value</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalAssetValue) }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1m-6 6H2v-2a4 4 0 014-4h1m3-4a4 4 0 11-8 0 4 4 0 018 0zm8 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active Personnel</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.activePersonnel }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
