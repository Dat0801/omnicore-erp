<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    purchaseOrders: Object,
    filters: Object,
    metrics: Object,
});

const status = ref(props.filters?.current?.status || 'all');

watch(status, (v) => {
    router.get(route('admin.purchase-orders.index'), { status: v }, { preserveState: true, replace: true });
});
</script>

<template>
    <Head title="Purchase Orders" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">OmniCore / Approval Queue</div>
                    <h2 class="text-2xl font-bold text-gray-900">Purchase Order Approval Queue</h2>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('admin.purchase-orders.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        New PO
                    </Link>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Pending Approvals</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ metrics.pending }} Orders</div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Value in Queue</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">${{ Number(metrics.totalValue || 0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) }}</div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Average Review Time</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ (metrics.avgLeadHours || 0).toFixed(1) }} Hours</div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Filter by Status:</label>
                        <select v-model="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                            <option value="all">All</option>
                            <option value="ordered">Pending Review</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">Refresh Queue</button>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">PO ID</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Supplier</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Amount</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="po in purchaseOrders.data" :key="po.id" class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <Link class="text-blue-600 font-semibold hover:underline" href="#!">#{{ po.code || ('PO-'+String(po.id).padStart(4,'0')) }}</Link>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ new Date(po.ordered_at || po.created_at).toLocaleDateString() }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ po.supplier?.name || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">${{ Number(po.total_amount || 0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                          :class="po.status==='ordered' ? 'border-amber-200 bg-amber-50 text-amber-700' : po.status==='received' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-700'">
                                        {{ po.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex gap-2">
                                        <button class="rounded-lg border border-gray-200 px-2.5 py-1 text-xs">View</button>
                                        <button class="rounded-lg border border-gray-200 px-2.5 py-1 text-xs">Print</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="purchaseOrders.data.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No purchase orders.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="purchaseOrders.data.length > 0" class="flex flex-col gap-4 border-top border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ purchaseOrders.from }}</span> to <span class="font-medium">{{ purchaseOrders.to }}</span>
                        of <span class="font-medium">{{ purchaseOrders.total }}</span> purchase orders
                    </div>
                    <div class="flex gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in purchaseOrders.links"
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

