<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    product: Object,
});
</script>

<template>
    <Head :title="product.name" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.products.index')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ product.name }}
                </h2>
                <span :class="{'bg-green-100 text-green-800': product.is_active, 'bg-red-100 text-red-800': !product.is_active}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.products.edit', product.id)" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit Product
                </Link>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <!-- Images Section -->
                <div class="md:col-span-1">
                    <div v-if="product.images && product.images.length > 0" class="mb-4">
                        <img :src="product.images[0].url" :alt="product.name" class="w-full h-auto rounded-lg shadow-sm object-cover aspect-square">
                    </div>
                    <div v-else class="w-full aspect-square bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 mb-4">
                        <svg class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    
                    <!-- Gallery (if more than 1 image) -->
                    <div v-if="product.images && product.images.length > 1" class="grid grid-cols-4 gap-2">
                        <div v-for="(image, index) in product.images.slice(1)" :key="image.id" class="cursor-pointer">
                            <img :src="image.url" class="w-full h-20 object-cover rounded-md border border-gray-200 hover:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Product Information</h3>
                        <dl class="mt-3 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ product.sku || 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Barcode</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ product.barcode || 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ product.category ? product.category.name : 'Uncategorized' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Brand</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ product.brand || 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pricing</h3>
                        <dl class="mt-3 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</dt>
                                <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-200">${{ Number(product.price).toFixed(2) }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stock</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ product.inventories_sum_quantity || 0 }} {{ product.unit || 'pcs' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost Price</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">${{ product.cost_price ? Number(product.cost_price).toFixed(2) : '0.00' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Description</h3>
                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">
                            {{ product.description || 'No description available.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Section -->
        <div v-if="product.variants && product.variants.length > 0" class="mt-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Variants</h3>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="variant in product.variants" :key="variant.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ variant.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ variant.sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                ${{ Number(variant.price).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ variant.inventories_sum_quantity || 0 }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
