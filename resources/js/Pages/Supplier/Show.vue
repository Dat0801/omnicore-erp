<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    supplier: Object,
    metrics: Object,
    products: Object,
    filters: Object,
});

const search = ref(props.filters?.current?.search || '');

const updateFilters = debounce((newSearch) => {
    router.get(route('admin.suppliers.show', props.supplier.id), {
        search: newSearch,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch(search, (val) => updateFilters(val));

const initials = (name) => {
    return (name || '')
        .split(' ')
        .filter(Boolean)
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};

const riskLevel = () => {
    const rating = Number(props.metrics?.rating || 0);
    if (rating >= 4) return { label: 'LOW RISK', classes: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
    if (rating >= 3) return { label: 'MEDIUM RISK', classes: 'bg-amber-50 text-amber-700 border-amber-200' };
    return { label: 'HIGH RISK', classes: 'bg-rose-50 text-rose-700 border-rose-200' };
};
</script>

<template>
    <Head :title="`${supplier.name} · Supplier`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                        <Link :href="route('dashboard')" class="hover:text-gray-700">Dashboard</Link>
                        <span>/</span>
                        <Link :href="route('admin.suppliers.index')" class="hover:text-gray-700">Suppliers</Link>
                        <span>/</span>
                        <span class="text-gray-900">{{ supplier.name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ supplier.name }}</h1>
                        <span
                            class="rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                            :class="supplier.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-100 text-gray-600'"
                        >
                            {{ supplier.is_active ? 'ACTIVE' : 'SUSPENDED' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-amber-500" v-if="metrics.rating">
                            <span v-for="i in 5" :key="i">
                                <svg v-if="i <= Math.round(metrics.rating)" class="inline h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.175 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg v-else class="inline h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.175 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </span>
                        </span>
                        <span v-if="metrics.rating" class="text-sm font-semibold text-gray-700">{{ Number(metrics.rating).toFixed(1) }}</span>
                        <span v-else class="text-xs text-gray-500">No performance data</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 2h8a2 2 0 012 2v3H4V4a2 2 0 012-2z" /><path d="M4 9h12v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9z" /><path d="M6 13h8v2H6z" /></svg>
                        Export
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 10h14v2H3z" /><path d="M10 3h2v14h-2z" /></svg>
                        New Purchase Order
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
                                {{ initials(supplier.name) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Contact Information</p>
                                <p class="text-xs text-gray-500">Primary contact</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-sm">
                            <div class="space-y-0.5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Address</p>
                                <p class="text-gray-900">{{ supplier.address || 'Not provided' }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Email</p>
                                <p class="text-gray-900">{{ supplier.email || 'Not provided' }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Phone</p>
                                <p class="text-gray-900">{{ supplier.phone || 'Not provided' }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Tax ID</p>
                                <p class="text-gray-900">{{ supplier.tax_id || 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Risk Profile</div>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold" :class="riskLevel().classes">
                                {{ riskLevel().label }}
                            </span>
                            <p class="mt-2 text-xs text-gray-500">Based on on-time delivery performance.</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total SKUs</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ metrics.totalSkus }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Avg. Cost Basis</p>
                            <p class="mt-2 text-2xl font-bold text-blue-600">{{ formatCurrency(metrics.avgCost) }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Lead Time</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ metrics.leadTimeDays ?? '—' }} <span v-if="metrics.leadTimeDays">Days</span></p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-gray-100 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Products Supplied</h2>
                                <p class="text-sm text-gray-500">Average unit price and current stock level.</p>
                            </div>
                            <div class="relative w-full sm:max-w-xs">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-10 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Search product or SKU..."
                                />
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Product Details</th>
                                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Stock Level</th>
                                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Last Ordered</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="p in products.data" :key="p.id" class="transition hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ p.name }}</p>
                                                <p class="text-xs text-gray-500">SKU: {{ p.sku || 'N/A' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(p.unit_price) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-2 w-32 rounded-full bg-gray-100">
                                                    <div
                                                        class="h-2 rounded-full"
                                                        :class="p.stock_qty > 100 ? 'bg-emerald-500' : (p.stock_qty > 0 ? 'bg-amber-500' : 'bg-rose-500')"
                                                        :style="{ width: Math.min(100, Math.max(0, Math.round((p.stock_qty / 1000) * 100))) + '%' }"
                                                    ></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700">{{ p.stock_qty }} in Stock</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ p.last_ordered_at ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr v-if="products.data.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                            No products from this supplier yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="products.data.length > 0" class="flex flex-col gap-4 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-gray-500">
                                Showing <span class="font-medium">{{ products.from }}</span> to <span class="font-medium">{{ products.to }}</span>
                                of <span class="font-medium">{{ products.total }}</span> products
                            </div>
                            <div class="flex gap-1">
                                <component
                                    :is="link.url ? Link : 'span'"
                                    v-for="(link, key) in products.links"
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
            </div>
        </div>
    </AdminLayout>
</template>
