<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    parentCategories: {
        type: Array,
        default: () => [],
    },
});

const form = reactive({
    name: '',
    parent_id: '',
    description: '',
    display_order: 0,
    is_active: true,
    is_featured: false,
});

const assignedProducts = ref([
    {
        id: 1,
        name: 'Galaxy S23 Ultra',
        sku: 'SAM-S23U-BLK',
        status: 'in_stock',
    },
    {
        id: 2,
        name: 'iPhone 15 Pro Max',
        sku: 'APL-15PM-TI',
        status: 'low_stock',
    },
]);

const productSearch = ref('');
const iconFileName = ref('');

const handleIconUpload = (event) => {
    const file = event.target.files?.[0];
    iconFileName.value = file ? file.name : '';
};

const stockLabel = (status) => (status === 'low_stock' ? 'LOW STOCK' : 'IN STOCK');

const stockBadgeClass = (status) =>
    status === 'low_stock'
        ? 'bg-amber-100 text-amber-700'
        : 'bg-emerald-100 text-emerald-700';
</script>

<template>
    <Head title="Create New Category" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-xs text-gray-500">Categories > Create New</div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Category</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Define your catalog structure and manage product hierarchy.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.categories.index')"
                        class="rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    >
                        Cancel
                    </Link>
                    <button
                        type="button"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Save Category
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-6 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">i</span>
                            Basic Information
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="category_name">
                                        Category Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="category_name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="e.g. Home Electronics"
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="parent_category">
                                        Parent Category
                                    </label>
                                    <select
                                        id="parent_category"
                                        v-model="form.parent_id"
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">None (Top Level)</option>
                                        <option v-for="category in props.parentCategories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300" for="category_description">
                                    Description
                                </label>
                                <textarea
                                    id="category_description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Briefly describe what products belong in this category..."
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Assigned Products</h3>
                            <button type="button" class="text-sm font-semibold text-blue-600 hover:text-blue-700">+ Add Products</button>
                        </div>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <input
                                v-model="productSearch"
                                type="text"
                                placeholder="Search products by SKU or name..."
                                class="mb-4 w-full rounded-md border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            />
                        </div>

                        <div class="overflow-hidden rounded-lg border border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-12 bg-gray-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                <div class="col-span-5">Product Name</div>
                                <div class="col-span-3">SKU</div>
                                <div class="col-span-3">Stock Status</div>
                                <div class="col-span-1 text-right">Action</div>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <div
                                    v-for="product in assignedProducts"
                                    :key="product.id"
                                    class="grid grid-cols-12 items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200"
                                >
                                    <div class="col-span-5 flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-500 dark:bg-gray-700">P</div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ product.name }}</span>
                                    </div>
                                    <div class="col-span-3 text-xs text-gray-500">{{ product.sku }}</div>
                                    <div class="col-span-3">
                                        <span :class="['rounded-full px-2 py-0.5 text-[11px] font-semibold', stockBadgeClass(product.status)]">
                                            {{ stockLabel(product.status) }}
                                        </span>
                                    </div>
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" class="text-red-500 hover:text-red-600">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H3.5a.5.5 0 000 1h.638l.56 9.045A2 2 0 006.694 16h6.612a2 2 0 001.996-1.955L15.862 5H16.5a.5.5 0 000-1H15V3a1 1 0 00-1-1H6zm2 4a.5.5 0 011 0v7a.5.5 0 01-1 0V6zm4 0a.5.5 0 10-1 0v7a.5.5 0 001 0V6z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="button" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                                View All Assigned Products
                                <span class="ml-1">+</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M11 3a1 1 0 10-2 0v1.382a1 1 0 00.553.894l4 2a1 1 0 00.894-1.788L11 3.882V3zM5 7a3 3 0 00-3 3v4a3 3 0 003 3h10a3 3 0 003-3v-4a3 3 0 00-3-3H5zm3.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            Display & Ranking
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500" for="display_order">
                                    Display Order
                                </label>
                                <input
                                    id="display_order"
                                    v-model.number="form.display_order"
                                    type="number"
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <p class="mt-1 text-xs text-gray-400">Lower numbers appear first in lists.</p>
                            </div>

                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
                                <input id="is_active" v-model="form.is_active" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200" for="is_active">Active Category</label>
                                    <p class="text-xs text-gray-500">If disabled, this category and its products will be hidden.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
                                <input id="is_featured" v-model="form.is_featured" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200" for="is_featured">Featured Category</label>
                                    <p class="text-xs text-gray-500">Highlight this category on the homepage dashboard.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Category Icon</h3>
                        <div class="relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-8 text-center text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <input
                                type="file"
                                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                @change="handleIconUpload"
                            />
                            <svg class="mb-2 h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2a2 2 0 012 2v1h4a2 2 0 012 2v10a2 2 0 01-2 2h-4v1a2 2 0 01-4 0v-1H6a2 2 0 01-2-2V7a2 2 0 012-2h4V4a2 2 0 012-2zm-3 7a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <p class="font-medium text-gray-700 dark:text-gray-200">Click to upload</p>
                            <p class="text-xs">SVG, PNG, JPG (max 512x512)</p>
                            <p v-if="iconFileName" class="mt-2 text-xs font-semibold text-blue-600">{{ iconFileName }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 text-xs text-blue-600">
                        <h4 class="text-sm font-semibold text-blue-800">Internal Note</h4>
                        <p class="mt-2">Categories are cached globally. Changes may take up to 5 minutes to reflect on the customer-facing portal.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center justify-end gap-3 border-t border-gray-200 pt-6 sm:flex-row dark:border-gray-700">
                <button
                    type="button"
                    class="w-full rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    Discard Changes
                </button>
                <button
                    type="button"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
                >
                    Create Category
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
