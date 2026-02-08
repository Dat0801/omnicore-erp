<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Mock Data
const stats = [
    {
        title: 'Total Products',
        value: '1,250',
        growth: 5.2,
        icon: 'products',
        color: 'blue'
    },
    {
        title: 'Total Inventory',
        value: '45,800',
        growth: 1.8,
        icon: 'inventory',
        color: 'orange'
    },
    {
        title: 'Total Orders',
        value: '328',
        growth: 12.5,
        icon: 'orders',
        color: 'green'
    }
];

const activeOrders = [
    {
        id: '#ORD-7721',
        customer: 'Alice Johnson',
        avatar: 'https://ui-avatars.com/api/?name=Alice+Johnson&background=random',
        status: 'Processing',
        total: '$250.00'
    },
    {
        id: '#ORD-7722',
        customer: 'Bob Smith',
        avatar: 'https://ui-avatars.com/api/?name=Bob+Smith&background=random',
        status: 'Pending',
        total: '$120.50'
    },
    {
        id: '#ORD-7723',
        customer: 'Charlie Davis',
        avatar: 'https://ui-avatars.com/api/?name=Charlie+Davis&background=random',
        status: 'Shipped',
        total: '$540.00'
    },
    {
        id: '#ORD-7724',
        customer: 'Diana Prince',
        avatar: 'https://ui-avatars.com/api/?name=Diana+Prince&background=random',
        status: 'Cancelled',
        total: '$89.00'
    }
];

const lowStockItems = [
    {
        name: 'Logitech MX Master 3S',
        warehouse: 'WH-Alpha',
        sku: '4421',
        remaining: 2,
        urgent: true
    },
    {
        name: 'Apple MacBook Air M2',
        warehouse: 'WH-Gamma',
        sku: '9002',
        remaining: 5,
        urgent: true
    },
    {
        name: 'Dell UltraSharp 27"',
        warehouse: 'WH-Alpha',
        sku: '1104',
        remaining: 12,
        urgent: false
    }
];

const warehouseStatus = [
    {
        name: 'Main Hub (Alpha)',
        capacity: 84,
        color: 'blue'
    },
    {
        name: 'East Coast (Gamma)',
        capacity: 32,
        color: 'green'
    }
];

const getStatusColor = (status) => {
    switch (status) {
        case 'Processing': return 'bg-blue-100 text-blue-800';
        case 'Pending': return 'bg-yellow-100 text-yellow-800';
        case 'Shipped': return 'bg-green-100 text-green-800';
        case 'Cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <!-- Page Title -->
        <div class="mb-8 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h2>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div v-for="(stat, index) in stats" :key="index" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start mb-4">
                    <div :class="`p-3 rounded-lg ${stat.color === 'blue' ? 'bg-blue-50 text-blue-600' : stat.color === 'orange' ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600'}`">
                        <svg v-if="stat.icon === 'products'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <svg v-else-if="stat.icon === 'inventory'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <span class="flex items-center text-sm font-medium text-green-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        {{ stat.growth }}%
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.title }}</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stat.value }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Orders -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Orders</h3>
                        <Link href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View All</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-medium text-xs">
                                <tr>
                                    <th class="px-6 py-4">Order ID</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="order in activeOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ order.id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img :src="order.avatar" :alt="order.customer" class="w-8 h-8 rounded-full mr-3">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ order.customer }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)}`">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">{{ order.total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Low Stock Alerts -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock Alerts</h3>
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded uppercase">Urgent</span>
                    </div>
                    <div class="space-y-6">
                        <div v-for="(item, index) in lowStockItems" :key="index" class="flex items-center justify-between pb-6 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ item.name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ item.warehouse }}</span> • SKU: {{ item.sku }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span :class="`block text-sm font-bold ${item.remaining <= 5 ? 'text-red-600' : 'text-orange-500'}`">
                                    {{ item.remaining }} left
                                </span>
                                <button class="text-xs font-medium text-blue-600 hover:text-blue-500 mt-1 uppercase">Restock</button>
                            </div>
                        </div>
                    </div>
                    <button class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                        Inventory Report
                    </button>
                </div>

                <!-- Warehouse Status -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Warehouse Status</h3>
                    <div class="space-y-6">
                        <div v-for="(wh, index) in warehouseStatus" :key="index">
                            <div class="flex justify-between text-sm font-medium mb-2">
                                <span class="text-gray-700 dark:text-gray-300">{{ wh.name }}</span>
                                <span class="text-gray-900 dark:text-white">{{ wh.capacity }}% Capacity</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                <div :class="`h-2.5 rounded-full ${wh.color === 'blue' ? 'bg-blue-600' : 'bg-green-500'}`" :style="`width: ${wh.capacity}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>