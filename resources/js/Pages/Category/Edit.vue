<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    parentCategories: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    _method: 'PUT',
    name: props.category.name,
    parent_id: props.category.parent_id || '',
    description: props.category.description || '',
    display_order: props.category.display_order,
    is_active: Boolean(props.category.is_active),
    is_featured: Boolean(props.category.is_featured),
    icon: null,
});

const iconFileName = ref('');

const handleIconUpload = (event) => {
    const file = event.target.files?.[0];
    if (file) {
        form.icon = file;
        iconFileName.value = file.name;
    }
};

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);

const performSearch = async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    isSearching.value = true;
    try {
        const response = await axios.get(route('admin.products.search', { query: searchQuery.value }));
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search failed', error);
    } finally {
        isSearching.value = false;
    }
};

const assignProduct = (product) => {
    router.post(route('admin.categories.products.assign', props.category.id), {
        product_id: product.id
    }, {
        preserveScroll: true,
        onSuccess: () => {
            searchQuery.value = '';
            searchResults.value = [];
        }
    });
};

const unassignProduct = (product) => {
    if (confirm('Remove product from this category?')) {
        router.delete(route('admin.categories.products.unassign', [props.category.id, product.id]), {
            preserveScroll: true,
        });
    }
};

const submit = () => {
    form.post(route('admin.categories.update', props.category.id), {
        preserveScroll: true,
    });
};

const destroy = () => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(route('admin.categories.destroy', props.category.id));
    }
};
</script>

<template>
    <Head title="Edit Category" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-xs text-gray-500">Categories > Edit</div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Category: {{ props.category.name }}</h1>
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
                        @click="destroy"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                    >
                        Delete
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
                    >
                        Update Category
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
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
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
                                    <div v-if="form.errors.parent_id" class="mt-1 text-xs text-red-500">{{ form.errors.parent_id }}</div>
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
                                    class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-6 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-600">P</span>
                            Assigned Products
                        </h3>

                        <div class="space-y-6">
                            <!-- Product Search -->
                            <div class="relative">
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Add Product to Category</label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        v-model="searchQuery"
                                        @input="performSearch"
                                        placeholder="Search products by name or SKU..."
                                        class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                                
                                <!-- Search Results Dropdown -->
                                <div v-if="searchResults.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ul class="divide-y divide-gray-100">
                                        <li 
                                            v-for="product in searchResults" 
                                            :key="product.id"
                                            class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                            @click="assignProduct(product)"
                                        >
                                            <div class="font-medium">{{ product.name }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ product.sku }} | ${{ product.price }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Products Table -->
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium text-gray-900">Name</th>
                                            <th class="px-4 py-3 text-left font-medium text-gray-900">SKU</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-900">Price</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="product in props.products" :key="product.id">
                                            <td class="px-4 py-3 text-gray-900">{{ product.name }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ product.sku }}</td>
                                            <td class="px-4 py-3 text-right text-gray-900">${{ product.price }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <button 
                                                    @click="unassignProduct(product)"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="props.products.length === 0">
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                No products assigned to this category.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
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
                                <div v-if="form.errors.display_order" class="mt-1 text-xs text-red-500">{{ form.errors.display_order }}</div>
                            </div>

                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
                                <input id="is_active" v-model="form.is_active" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200" for="is_active">Active Category</label>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
                                <input id="is_featured" v-model="form.is_featured" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200" for="is_featured">Featured Category</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Category Icon</h3>
                        <div v-if="props.category.icon" class="mb-4 flex justify-center">
                            <img :src="props.category.icon" alt="Current Icon" class="h-20 w-20 object-contain" />
                        </div>
                        <div class="relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-8 text-center text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <input
                                type="file"
                                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                @change="handleIconUpload"
                                accept="image/*"
                            />
                            <svg class="mb-2 h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2a2 2 0 012 2v1h4a2 2 0 012 2v10a2 2 0 01-2 2h-4v1a2 2 0 01-4 0v-1H6a2 2 0 01-2-2V7a2 2 0 012-2h4V4a2 2 0 012-2zm-3 7a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <p class="font-medium text-gray-700 dark:text-gray-200">Click to upload new icon</p>
                            <p v-if="iconFileName" class="mt-2 text-xs font-semibold text-blue-600">{{ iconFileName }}</p>
                            <div v-if="form.errors.icon" class="mt-1 text-xs text-red-500">{{ form.errors.icon }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center justify-end gap-3 border-t border-gray-200 pt-6 sm:flex-row dark:border-gray-700">
                <Link
                    :href="route('admin.categories.index')"
                    class="w-full rounded-md border border-gray-200 bg-white px-4 py-2 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    Discard Changes
                </Link>
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto disabled:opacity-50"
                >
                    Update Category
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
