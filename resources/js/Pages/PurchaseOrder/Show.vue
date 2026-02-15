<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    purchaseOrder: Object,
    metrics: Object,
});

const formattedCode = computed(() => {
    if (props.purchaseOrder.code) {
        return props.purchaseOrder.code;
    }

    return `PO-${String(props.purchaseOrder.id).padStart(4, '0')}`;
});

const statusBadgeClasses = computed(() => {
    if (props.purchaseOrder.status === 'received') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (props.purchaseOrder.status === 'cancelled') {
        return 'border-rose-200 bg-rose-50 text-rose-700';
    }

    return 'border-amber-200 bg-amber-50 text-amber-700';
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};

const formatDateTime = (value) => {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString();
};
</script>

<template>
    <Head :title="`${formattedCode} · Purchase Order`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                        <Link :href="route('dashboard')" class="hover:text-gray-700">Dashboard</Link>
                        <span>/</span>
                        <Link :href="route('admin.purchase-orders.index')" class="hover:text-gray-700">Purchase Orders</Link>
                        <span>/</span>
                        <span class="text-gray-900">{{ formattedCode }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ formattedCode }}</h1>
                        <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold" :class="statusBadgeClasses">
                            {{ purchaseOrder.status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        Supplier:
                        <span class="font-medium text-gray-900">
                            {{ purchaseOrder.supplier?.name || 'Unknown supplier' }}
                        </span>
                        · Ship to
                        <span class="font-medium text-gray-900">
                            {{ purchaseOrder.warehouse?.name || 'Unknown warehouse' }}
                        </span>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 2h8a2 2 0 012 2v3H4V4a2 2 0 012-2z" />
                            <path d="M4 9h12v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9z" />
                            <path d="M6 13h8v2H6z" />
                        </svg>
                        Print PO
                    </button>
                    <Link
                        :href="route('admin.purchase-orders.index', { status: purchaseOrder.status })"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 10h14v2H3z" />
                            <path d="M10 3h2v14h-2z" />
                        </svg>
                        Back to Queue
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M4 3h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1zM3 9h14a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6a1 1 0 011-1z"
                                    />
                                </svg>
                            </span>
                            Order Details
                        </div>
                        <div class="mt-6 grid gap-6 md:grid-cols-2">
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Supplier</p>
                                <p class="text-gray-900">{{ purchaseOrder.supplier?.name || 'Not set' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ purchaseOrder.supplier?.email || '' }}
                                </p>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ship To Warehouse</p>
                                <p class="text-gray-900">
                                    {{ purchaseOrder.warehouse?.name || 'Not set' }}
                                    <span v-if="purchaseOrder.warehouse?.code" class="text-xs text-gray-500">
                                        ({{ purchaseOrder.warehouse.code }})
                                    </span>
                                </p>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ordered At</p>
                                <p class="text-gray-900">{{ formatDateTime(purchaseOrder.ordered_at) }}</p>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Expected Delivery</p>
                                <p class="text-gray-900">{{ formatDateTime(purchaseOrder.expected_at) }}</p>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Received At</p>
                                <p class="text-gray-900">{{ formatDateTime(purchaseOrder.received_at) }}</p>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Status</p>
                                <p class="text-gray-900 capitalize">{{ purchaseOrder.status }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">Line Items</h2>
                                <p class="text-xs text-gray-500">
                                    {{ metrics.totalItems }} products, {{ metrics.totalQuantity }} units
                                </p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                            Product
                                        </th>
                                        <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                            Unit Cost
                                        </th>
                                        <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                            Quantity
                                        </th>
                                        <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                            Line Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="item in purchaseOrder.items" :key="item.id" class="transition hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ item.product?.name || 'Unknown product' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    SKU: {{ item.product?.sku || 'N/A' }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-700">
                                            {{ formatCurrency(item.price) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-700">
                                            {{ item.quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(item.quantity * item.price) }}
                                        </td>
                                    </tr>
                                    <tr v-if="!purchaseOrder.items || purchaseOrder.items.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                            No line items on this purchase order.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex items-center justify-end border-t border-gray-100 px-6 py-4">
                            <div class="space-y-1 text-sm">
                                <div class="flex items-center justify-between gap-8">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium text-gray-900">
                                        {{ formatCurrency(metrics.totalAmount) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Units</span>
                                <span class="font-semibold text-gray-900">{{ metrics.totalQuantity }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Amount</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(metrics.totalAmount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

