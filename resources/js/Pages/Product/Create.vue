<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    sku: '',
    barcode: '',
    type: 'physical',
    internal_id: '',
    description: '',
    price: '', // Base Price
    cost_price: '',
    unit: 'pcs',
    weight: '',
    dimensions: { length: '', width: '', height: '' },
    quantity: 0,
    low_stock_threshold: 5,
    is_active: true, // Product Status (Active/Draft)
    allow_backorders: false,
    product_category_id: '',
    tags: '',
    images: [],
    has_variants: false,
    variants: [],
});

const options = ref([{ name: '', values: '' }]); // UI state for option generation

const addOption = () => {
    options.value.push({ name: '', values: '' });
};

const removeOption = (index) => {
    options.value.splice(index, 1);
};

const generateVariants = () => {
    // Cartesian product of options
    const validOptions = options.value.filter(o => o.name && o.values);
    if (validOptions.length === 0) return;

    const optionValues = validOptions.map(o => {
        return o.values.split(',').map(v => ({ name: o.name, value: v.trim() }));
    });

    const cartesian = (args) => {
        const r = [], max = args.length - 1;
        function helper(arr, i) {
            for (let j = 0, l = args[i].length; j < l; j++) {
                const a = arr.slice(0); // clone arr
                a.push(args[i][j]);
                if (i == max) r.push(a);
                else helper(a, i + 1);
            }
        }
        helper([], 0);
        return r;
    };

    const combinations = cartesian(optionValues);

    form.variants = combinations.map(combo => {
        const variantAttributes = {};
        combo.forEach(c => variantAttributes[c.name] = c.value);
        
        const skuSuffix = combo.map(c => c.value.toUpperCase().substring(0, 3)).join('-');
        
        return {
            variant_attributes: variantAttributes,
            sku: `${form.sku}-${skuSuffix}`,
            price: form.price,
            quantity: 0,
            barcode: '',
        };
    });
};

const generateSku = () => {
    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    form.sku = `PROD-${random}`;
};

const submit = () => {
    form.post(route('admin.products.store'));
};

const handleImageUpload = (e) => {
    // Simple mock for image upload visual
    const file = e.target.files[0];
    if (file) {
        // In a real app, we'd handle file preview and upload
        // For now, we just mock adding it to the form
        console.log('File selected:', file);
    }
};
</script>

<template>
    <Head title="Create New Product" />

    <AdminLayout>
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center text-sm text-gray-500 mb-1">
                        <span>Products</span>
                        <span class="mx-2">›</span>
                        <span class="text-gray-900 font-medium">Create New Product</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Product</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Populate details to add a new SKU to the central warehouse.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    <Link :href="route('admin.products.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 mr-2">
                        ← Back to List
                    </Link>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">i</span>
                            Basic Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                                <input v-model="form.name" type="text" id="name" placeholder="e.g. Wireless Ergonomic Mouse MX-2"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Type</label>
                                    <select v-model="form.type" id="type" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                        <option value="physical">Physical Item</option>
                                        <option value="service">Service</option>
                                        <option value="digital">Digital Product</option>
                                    </select>
                                    <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">{{ form.errors.type }}</div>
                                </div>
                                <div>
                                    <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input v-model="form.sku" type="text" id="sku" placeholder="PROD-001"
                                            class="flex-1 rounded-l-md border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3 border-r-0">
                                        <button type="button" @click="generateSku"
                                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-xs font-medium hover:bg-gray-100 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300">
                                            AUTO
                                        </button>
                                    </div>
                                    <div v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode / EAN / UPC</label>
                                    <input v-model="form.barcode" type="text" id="barcode" placeholder="Scanned barcode..."
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                    <div v-if="form.errors.barcode" class="text-red-500 text-xs mt-1">{{ form.errors.barcode }}</div>
                                </div>
                                <div>
                                    <label for="internal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Internal Identifier</label>
                                    <input v-model="form.internal_id" type="text" id="internal_id" placeholder="WH-LOC-42"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Description</label>
                                <textarea v-model="form.description" id="description" rows="4" placeholder="Describe features, dimensions, and technical specs..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3"></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div v-if="form.type === 'physical'" class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-4 mt-2">
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Weight (kg)</label>
                                    <input v-model="form.weight" type="number" step="0.01" id="weight" placeholder="0.5"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                </div>
                                <div>
                                    <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit</label>
                                    <input v-model="form.unit" type="text" id="unit" placeholder="pcs"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dimensions (L x W x H)</label>
                                    <div class="flex gap-1">
                                        <input v-model="form.dimensions.length" type="number" placeholder="L" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm text-sm p-2">
                                        <input v-model="form.dimensions.width" type="number" placeholder="W" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm text-sm p-2">
                                        <input v-model="form.dimensions.height" type="number" placeholder="H" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm text-sm p-2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Media -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">📷</span>
                            Product Media
                        </h3>
                        
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer relative">
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleImageUpload" multiple>
                            <div class="flex flex-col items-center justify-center pointer-events-none">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, WebP up to 10MB</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-4 gap-4">
                            <!-- Preview Slots (Static for now as shown in image) -->
                            <div class="aspect-square rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center relative overflow-hidden group">
                                <span class="absolute top-2 left-2 bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">MAIN</span>
                                <img src="https://placehold.co/100" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <div class="aspect-square rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:border-gray-300 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">V</span>
                                Variants
                            </h3>
                            <div class="flex items-center">
                                <input v-model="form.has_variants" id="has_variants" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="has_variants" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">This product has variants</label>
                            </div>
                        </div>

                        <div v-if="form.has_variants">
                            <div class="space-y-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Option Types</h4>
                                <div v-for="(option, index) in options" :key="index" class="flex gap-4 items-start">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Name (e.g. Size)</label>
                                        <input v-model="option.name" type="text" placeholder="Size" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm text-sm p-2">
                                    </div>
                                    <div class="flex-[2]">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Values (comma separated)</label>
                                        <input v-model="option.values" type="text" placeholder="S, M, L" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm text-sm p-2">
                                    </div>
                                    <button @click="removeOption(index)" type="button" class="mt-6 text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="addOption" type="button" class="text-sm text-blue-600 hover:text-blue-800 font-medium">+ Add Option</button>
                                    <button @click="generateVariants" type="button" class="text-sm text-green-600 hover:text-green-800 font-medium ml-auto">Generate Variants</button>
                                </div>
                            </div>

                            <div v-if="form.variants.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="(variant, index) in form.variants" :key="index">
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                <span v-for="(val, key) in variant.variant_attributes" :key="key" class="mr-2 px-2 py-0.5 bg-gray-100 rounded text-xs">{{ key }}: {{ val }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input v-model="variant.sku" type="text" class="w-32 rounded border-gray-300 text-xs p-1">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input v-model="variant.price" type="number" step="0.01" class="w-24 rounded border-gray-300 text-xs p-1">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input v-model="variant.quantity" type="number" class="w-20 rounded border-gray-300 text-xs p-1">
                                            </td>
                                            <td class="px-3 py-2">
                                                <button @click="form.variants.splice(index, 1)" type="button" class="text-red-500 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory (Grid) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pricing -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-full">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                <span class="text-blue-500">💵</span> Pricing
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Base Price</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input v-model="form.price" type="number" step="0.01" id="price" placeholder="0.00"
                                            class="block w-full rounded-md border-gray-300 pl-7 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2">
                                    </div>
                                    <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                                </div>
                                <div>
                                    <label for="cost_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost per item</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input v-model="form.cost_price" type="number" step="0.01" id="cost_price" placeholder="0.00"
                                            class="block w-full rounded-md border-gray-300 pl-7 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 italic">Customers won't see this.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div v-if="!form.has_variants" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-full">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                <span class="text-blue-500">📦</span> Inventory
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity Available</label>
                                    <input v-model="form.quantity" type="number" id="quantity" placeholder="0"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                </div>
                                <div>
                                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Low Stock Threshold</label>
                                    <input v-model="form.low_stock_threshold" type="number" id="low_stock_threshold" placeholder="5"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-6">
                    <!-- Product Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4">Product Status</h3>
                        
                        <div class="space-y-4">
                            <select v-model="form.is_active" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                <option :value="true">Active</option>
                                <option :value="false">Draft</option>
                            </select>

                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow backorders</span>
                                <button type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="form.allow_backorders ? 'bg-blue-600' : 'bg-gray-200'"
                                    @click="form.allow_backorders = !form.allow_backorders">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" 
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.allow_backorders ? 'translate-x-5' : 'translate-x-0'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Organization -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4">Organization</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="category" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Category</label>
                                <select v-model="form.product_category_id" id="category" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                    <option value="" disabled>Select Category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.product_category_id" class="text-red-500 text-xs mt-1">{{ form.errors.product_category_id }}</div>
                            </div>

                            <div>
                                <label for="tags" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Tags</label>
                                <input v-model="form.tags" type="text" id="tags" placeholder="Add tags..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white py-2 px-3">
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                        Wireless
                                        <button type="button" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                    </span>
                                    <span class="inline-flex items-center rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                        Ergonomic
                                        <button type="button" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Log (Visual Only) -->
                    <div class="bg-blue-50 rounded-lg border border-blue-100 p-6">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">Audit Log</h3>
                        <div class="flex items-start gap-3 mt-3">
                            <div class="mt-0.5">
                                <div class="w-4 h-4 rounded-full border border-blue-400 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Created by Admin</p>
                                <p class="text-xs text-gray-500">{{ new Date().toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-4">
                <button type="button" class="px-6 py-2.5 rounded-md border border-gray-300 bg-white text-gray-700 font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                    Discard
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-md bg-blue-600 text-white font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" :disabled="form.processing">
                    Save Product
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
