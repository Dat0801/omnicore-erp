<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const updateSearch = debounce((value) => {
    router.get(route('admin.categories.index'), { search: value }, { preserveState: true, replace: true });
}, 300);

watch(search, (value) => {
    updateSearch(value);
});

const badgeClasses = [
    'bg-blue-100 text-blue-600',
    'bg-emerald-100 text-emerald-600',
    'bg-amber-100 text-amber-600',
    'bg-violet-100 text-violet-600',
    'bg-rose-100 text-rose-600',
];

const badgeClass = (index) => badgeClasses[index % badgeClasses.length];

const initialFor = (name) => (name ? name.charAt(0).toUpperCase() : '?');

const descriptionFor = (category) => category.description ?? 'No description provided.';

const statusLabel = (category) => (category.is_active ? 'Active' : 'Inactive');
</script>

<template>
    <Head title="Category Management" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Dashboard / Category Management</div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Category Management</h2>
                </div>
                <Link :href="route('admin.categories.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add New Category
                </Link>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-10 pr-3 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="Search categories..."
                        />
                    </div>
                    <div class="text-xs text-gray-500">
                        Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ categories.from ?? 0 }}</span>
                        to <span class="font-semibold text-gray-700 dark:text-gray-200">{{ categories.to ?? 0 }}</span>
                        of <span class="font-semibold text-gray-700 dark:text-gray-200">{{ categories.total ?? 0 }}</span> categories
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Category Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Products</th>
                                <th scope="col" class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Order</th>
                                <th scope="col" class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-for="(category, index) in categories.data" :key="category.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div :class="['flex h-9 w-9 items-center justify-center rounded-lg text-sm font-semibold', badgeClass(index)]">
                                            {{ initialFor(category.name) }}
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ category.name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="max-w-xs truncate">{{ descriptionFor(category) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                        {{ category.products_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ (categories.from ?? 0) + index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            :class="category.is_active ? 'bg-blue-600' : 'bg-gray-200'"
                                            role="switch"
                                            :aria-checked="category.is_active"
                                        >
                                            <span
                                                aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
                                                :class="category.is_active ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                        <span class="text-xs text-gray-500">{{ statusLabel(category) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    <Link href="#" class="text-blue-600 hover:text-blue-700">View Products</Link>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No categories found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ categories.from ?? 0 }}</span> to <span class="font-medium">{{ categories.to ?? 0 }}</span>
                        of <span class="font-medium">{{ categories.total ?? 0 }}</span> categories
                    </div>
                    <div class="flex gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, key) in categories.links"
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
