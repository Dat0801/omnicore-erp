<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    sku: '',
    price: '',
    description: '',
});

const submit = () => {
    form.post(route('admin.products.store'));
};
</script>

<template>
    <Head title="Create Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Product</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2" for="name">Name</label>
                                <input v-model="form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600" id="name" type="text">
                                <div v-if="form.errors.name" class="text-red-500 text-xs italic">{{ form.errors.name }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2" for="sku">SKU</label>
                                <input v-model="form.sku" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600" id="sku" type="text">
                                <div v-if="form.errors.sku" class="text-red-500 text-xs italic">{{ form.errors.sku }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2" for="price">Price</label>
                                <input v-model="form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600" id="price" type="number" step="0.01">
                                <div v-if="form.errors.price" class="text-red-500 text-xs italic">{{ form.errors.price }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2" for="description">Description</label>
                                <textarea v-model="form.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600" id="description"></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-xs italic">{{ form.errors.description }}</div>
                            </div>
                            <div class="flex items-center justify-between">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" :disabled="form.processing">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
