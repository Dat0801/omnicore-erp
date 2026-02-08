<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    stats: Object,
    filters: Object,
    inventories: Object,
    alerts: Array,
});

const search = ref(props.filters.current.search || '');
const warehouseId = ref(props.filters.current.warehouse_id || 'all');
const status = ref(props.filters.current.status || 'all');

watch([search, warehouseId, status], debounce(([newSearch, newWarehouseId, newStatus]) => {
    router.get(route('admin.inventory.index'), {
        search: newSearch,
        warehouse_id: newWarehouseId,
        status: newStatus,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-US').format(value);
};
</script>

<template>
    <Head title="Inventory Overview" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                        <span>Management</span>
                        <span>/</span>
                        <span class="text-gray-900 font-medium">Inventory Overview</span>
                    </div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">Inventory Overview</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            v-model="search"
                            placeholder="Search products, SKUs, or locations..." 
                            class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg w-80 text-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button class="p-2 text-gray-400 hover:text-gray-600 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Stock
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total SKUs</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.totalSkus) }}</h3>
                            <p class="text-xs text-green-600 font-medium mt-1 flex items-center">
                                <span class="bg-green-100 text-green-600 rounded px-1 py-0.5 mr-1">+2.4%</span>
                            </p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Low Stock Alerts</p>
                            <h3 class="text-2xl font-bold text-orange-600 mt-2">{{ formatNumber(stats.lowStockAlerts) }}</h3>
                            <p class="text-xs text-orange-600 font-medium mt-1">Needs restock</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Valuation</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ formatCurrency(stats.totalValuation) }}</h3>
                            <p class="text-xs text-gray-400 font-medium mt-1">USD</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Active Warehouses</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ stats.activeWarehouses }}</h3>
                            <p class="text-xs text-gray-400 font-medium mt-1">Global locations</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Warehouse Location</label>
                        <select v-model="warehouseId" class="border-gray-200 rounded-lg text-sm w-full md:w-48 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Warehouses</option>
                            <option v-for="warehouse in filters.warehouses" :key="warehouse.id" :value="warehouse.id">
                                {{ warehouse.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Stock Status</label>
                        <select v-model="status" class="border-gray-200 rounded-lg text-sm w-full md:w-48 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Statuses</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <a :href="route('admin.inventory.index', { ...filters.current, export: 'csv' })" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export CSV
                    </a>
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Filters
                    </button>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product Details</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Warehouse Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Qty On Hand</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in inventories.data" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        <!-- Placeholder for product image -->
                                        <img v-if="item.product.images && item.product.images.length > 0" :src="item.product.images[0].url" class="w-full h-full object-cover rounded-lg" alt="">
                                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ item.product.name }}</h4>
                                        <p class="text-xs text-gray-500">SKU: {{ item.product.code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ item.warehouse.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                <span :class="{'text-red-500': item.quantity === 0, 'text-gray-900': item.quantity > 0}">
                                    {{ formatNumber(item.quantity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                    :class="{
                                        'bg-green-50 text-green-700 border-green-200': item.quantity > item.reorder_level,
                                        'bg-orange-50 text-orange-700 border-orange-200': item.quantity <= item.reorder_level && item.quantity > 0,
                                        'bg-red-50 text-red-700 border-red-200': item.quantity === 0
                                    }"
                                >
                                    {{ item.quantity === 0 ? 'Out of Stock' : (item.quantity <= item.reorder_level ? 'Low Stock' : 'In Stock') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-end gap-1 ml-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Movement History
                                </button>
                            </td>
                        </tr>
                        <tr v-if="inventories.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No inventory items found.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-between" v-if="inventories.data.length > 0">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ inventories.from }}</span> to <span class="font-medium">{{ inventories.to }}</span> of <span class="font-medium">{{ inventories.total }}</span> results
                    </p>
                    <div class="flex gap-2">
                         <Link 
                            v-for="link in inventories.links" 
                            :key="link.label"
                            :href="link.url || '#'"
                            class="px-3 py-1 text-sm border rounded-md"
                            :class="{
                                'bg-blue-50 text-blue-600 border-blue-200 font-medium': link.active,
                                'text-gray-500 border-gray-200 hover:bg-gray-50': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url
                            }"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Bottom Section (Charts & Alerts) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="bg-blue-600 p-1 rounded">
                             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900">Stock Level Distribution</h3>
                    </div>
                    <div class="h-48 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400">
                        Chart Placeholder
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <h3 class="font-bold text-gray-900">Critical Alerts</h3>
                    </div>
                    <div class="space-y-3">
                         <div v-for="alert in alerts" :key="alert.id" class="p-3 bg-red-50 border border-red-100 rounded-lg flex items-start gap-3">
                             <div class="bg-red-100 p-1.5 rounded-full mt-0.5">
                                 <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                 </svg>
                             </div>
                             <div>
                                 <h4 class="text-sm font-semibold text-gray-900">{{ alert.quantity === 0 ? 'Stock Out' : 'Low Stock' }}</h4>
                                 <p class="text-xs text-gray-600 mt-0.5">
                                     {{ alert.product.name }} is {{ alert.quantity === 0 ? 'out of stock' : 'low on stock' }} in {{ alert.warehouse.name }}.
                                 </p>
                             </div>
                         </div>
                         <div v-if="alerts.length === 0" class="text-sm text-gray-500 text-center py-4">
                             No critical alerts.
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
