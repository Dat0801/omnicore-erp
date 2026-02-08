<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    products: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || 'all');

// Debounce search to avoid too many requests
const updateSearch = debounce((value) => {
    router.get(route('admin.products.index'), { search: value, status: status.value }, { preserveState: true, replace: true });
}, 300);

watch(search, (value) => {
    updateSearch(value);
});

const updateStatus = (newStatus) => {
    status.value = newStatus;
    router.get(route('admin.products.index'), { search: search.value, status: newStatus }, { preserveState: true, replace: true });
};

const toggleProductStatus = (product) => {
    const { images, category, created_at, updated_at, ...data } = product;
    
    router.put(route('admin.products.update', product.id), {
        ...data,
        is_active: !product.is_active
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show toast
        }
    });
};
</script>

<template>
    <Head title="Product Inventory" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">Product Inventory</h2>
                <span class="text-gray-500 text-sm border-l border-gray-300 pl-4">{{ stats.totalProducts }} Total Products</span>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
                <Link :href="route('admin.products.create')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create New Product
                </Link>
            </div>
        </div>

        <div class="space-y-6">
            
            <!-- Filter Bar -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                            placeholder="Search by name, SKU or category..." 
                        />
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button @click="updateStatus('all')" :class="{'bg-blue-50 text-blue-700 font-medium': status === 'all', 'text-gray-500 hover:text-gray-700': status !== 'all'}" class="px-3 py-1.5 rounded-md text-sm transition-colors">All</button>
                        <button @click="updateStatus('active')" :class="{'bg-blue-50 text-blue-700 font-medium': status === 'active', 'text-gray-500 hover:text-gray-700': status !== 'active'}" class="px-3 py-1.5 rounded-md text-sm transition-colors">Active</button>
                        <button @click="updateStatus('inactive')" :class="{'bg-blue-50 text-blue-700 font-medium': status === 'inactive', 'text-gray-500 hover:text-gray-700': status !== 'inactive'}" class="px-3 py-1.5 rounded-md text-sm transition-colors">Inactive</button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button class="flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            More Filters
                        </button>
                        <button class="flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Product Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img v-if="product.images && product.images.length > 0" :src="product.images[0].url" class="h-10 w-10 rounded-lg object-cover bg-gray-100" alt="">
                                                <div v-else class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ product.name }}</div>
                                                <div class="text-xs text-gray-500">{{ product.category ? product.category.name : 'Uncategorized' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 font-mono">{{ product.code || product.sku || 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ product.category ? product.category.name : 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Status Toggle -->
                                        <button 
                                            @click="toggleProductStatus(product)"
                                            class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            :class="product.is_active ? 'bg-blue-600' : 'bg-gray-200'"
                                            role="switch" 
                                            :aria-checked="product.is_active"
                                        >
                                            <span 
                                                aria-hidden="true" 
                                                class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                                                :class="product.is_active ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                        <span class="ml-2 text-sm text-gray-500">{{ product.is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('admin.products.edit', product.id)" class="text-blue-600 hover:text-blue-900">Edit</Link>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        No products found matching your criteria.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing <span class="font-medium">{{ products.from }}</span> to <span class="font-medium">{{ products.to }}</span> of <span class="font-medium">{{ products.total }}</span> products
                        </div>
                        <div class="flex gap-1">
                        <component 
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in products.links" 
                            :key="key" 
                            :href="link.url" 
                            v-html="link.label"
                            class="px-3 py-1 border rounded text-sm transition-colors"
                            :class="{
                                'bg-blue-600 text-white border-blue-600': link.active,
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url
                            }"
                        />
                    </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Active Listings</div>
                        <div class="flex items-end justify-between">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.activeCount }}</div>
                            <div class="text-green-500 text-sm font-medium flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                ~4%
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Out of Stock</div>
                        <div class="flex items-end justify-between">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.outOfStockCount }}</div>
                            <div class="text-orange-500 text-sm font-medium flex items-center">
                                ! Warning
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Top Category</div>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white truncate w-2/3" :title="stats.topCategory">{{ stats.topCategory }}</div>
                            <div class="text-blue-600 text-sm font-medium">
                                {{ stats.topCategoryCount }} Products
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Recent Updates</div>
                        <div class="flex items-end justify-between">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.recentUpdates }}</div>
                            <div class="text-gray-500 text-sm font-medium">
                                Today
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </AdminLayout>
</template>
