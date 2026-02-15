<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
    warehouses: Array,
});

const step = ref(2);
const selectedSupplier = ref(null);
const supplierSearch = ref('');

const form = useForm({
    supplier_id: null,
    warehouse_id: null,
    expected_at: null,
    items: [],
});

watch(selectedSupplier, (val) => {
    form.supplier_id = val ? val.id : null;
});

const searchQuery = ref('');
const searchResults = ref([]);
const searching = ref(false);
const showResults = ref(false);

const addItem = (product) => {
    const existing = form.items.find((i) => i.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
        existing.price = product.price ?? existing.price;
    } else {
        form.items.push({
            product_id: product.id,
            name: product.name,
            sku: product.sku,
            quantity: 1,
            price: Number(product.price || 0),
        });
    }
    searchQuery.value = '';
    searchResults.value = [];
    showResults.value = false;
};

const removeItem = (idx) => {
    form.items.splice(idx, 1);
};

const itemSubtotal = computed(() =>
    form.items.reduce((sum, i) => sum + i.quantity * Number(i.price || 0), 0),
);

const tax = computed(() => itemSubtotal.value * 0.1);
const shipping = ref(0);
const grandTotal = computed(() => itemSubtotal.value + tax.value + shipping.value);

const searchProducts = async () => {
    const q = searchQuery.value?.trim();
    if (!q) {
        searchResults.value = [];
        showResults.value = false;
        return;
    }
    searching.value = true;
    try {
        const res = await window.axios.get(route('admin.products.search'), { params: { query: q } });
        searchResults.value = res.data || [];
        showResults.value = true;
    } finally {
        searching.value = false;
    }
};

let searchTimer = null;
watch(searchQuery, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(searchProducts, 250);
});

const submitPo = async () => {
    if (!form.supplier_id || !form.warehouse_id || form.items.length === 0) {
        return;
    }
    const payload = {
        supplier_id: form.supplier_id,
        warehouse_id: form.warehouse_id,
        expected_at: form.expected_at,
        items: form.items.map((i) => ({
            product_id: i.product_id,
            quantity: i.quantity,
            price: i.price,
        })),
    };
    try {
        await window.axios.post('/api/purchase-orders', payload);
        window.location = route('admin.purchase-orders.index');
    } catch (e) {
        //
    }
};
</script>

<template>
    <Head title="Create Purchase Order" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">Procurement / New Purchase Order</div>
                    <h2 class="text-2xl font-bold text-gray-900">Create Purchase Order</h2>
                    <div class="text-xs text-gray-500 mt-1">Draft status</div>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('admin.purchase-orders.index')" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</Link>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-3 gap-4">
                    <div class="flex flex-col items-center justify-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white">✓</div>
                        <div class="mt-2 text-xs font-semibold text-emerald-700">Select Supplier</div>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full border border-blue-600 text-blue-600">2</div>
                        <div class="mt-2 text-xs font-semibold text-blue-700">Add Items</div>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 text-gray-400">3</div>
                        <div class="mt-2 text-xs font-medium text-gray-500">Logistics & Terms</div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-8 space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path d="M2 5a2 2 0 012-2h2.28a2 2 0 011.788 1.106l.447.894A2 2 0 009.72 6H16a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/></svg>
                                <h3 class="font-semibold text-gray-900">Selected Supplier</h3>
                            </div>
                            <div>
                                <select v-model="selectedSupplier" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm" :class="{'text-gray-400': !selectedSupplier}">
                                    <option :value="null">Choose supplier...</option>
                                    <option v-for="s in suppliers" :key="s.id" :value="s">{{ s.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="selectedSupplier" class="grid gap-4 sm:grid-cols-3 text-sm">
                            <div>
                                <div class="text-xs text-gray-500">Vendor</div>
                                <div class="font-medium text-gray-900">{{ selectedSupplier.name }}</div>
                                <div class="text-xs text-gray-500">ID: VEN-{{ String(selectedSupplier.id).padStart(4,'0') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Contact</div>
                                <div class="font-medium text-gray-900">{{ selectedSupplier.email || '—' }}</div>
                                <div class="text-xs text-gray-500">{{ selectedSupplier.phone || '' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Billing Address</div>
                                <div class="font-medium text-gray-900">{{ selectedSupplier.address || '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3h14v2H3V3zm0 6h14v2H3V9zm0 6h14v2H3v-2z"/></svg>
                                <h3 class="font-semibold text-gray-900">Order Items</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">Import CSV</button>
                                <button type="button" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Add Product</button>
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <input v-model="searchQuery" type="text" placeholder="Search product name or SKU..." class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" />
                            <div v-if="showResults && searchResults.length" class="absolute z-10 mt-2 w-full rounded-lg border border-gray-200 bg-white shadow">
                                <ul class="max-h-60 overflow-y-auto divide-y divide-gray-100">
                                    <li v-for="p in searchResults" :key="p.id">
                                        <button type="button" class="flex w-full items-center justify-between px-4 py-2 text-left text-sm hover:bg-gray-50" @click="addItem(p)">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ p.name }}</div>
                                                <div class="text-xs text-gray-500">SKU: {{ p.sku }}</div>
                                            </div>
                                            <div class="text-sm text-gray-700">${{ Number(p.price || 0).toFixed(2) }}</div>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            <div v-for="(item, idx) in form.items" :key="item.product_id" class="flex items-center gap-4 py-3">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ item.name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ item.sku }}</div>
                                </div>
                                <div class="w-28">
                                    <input type="number" min="1" v-model.number="item.quantity" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                </div>
                                <div class="w-36">
                                    <input type="number" step="0.01" min="0" v-model.number="item.price" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                </div>
                                <div class="w-32 text-right font-medium text-gray-900">${{ (item.quantity * Number(item.price || 0)).toFixed(2) }}</div>
                                <button type="button" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50" @click="removeItem(idx)">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 6h8v8H6V6z" /></svg>
                                </button>
                            </div>
                            <div v-if="form.items.length === 0" class="px-4 py-8 text-center text-sm text-gray-500">
                                Click to add another product
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3h14l-2 14H5L3 3z"/></svg>
                            <h3 class="font-semibold text-gray-900">Logistics & Terms</h3>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-gray-500">Destination Warehouse</label>
                                <select v-model="form.warehouse_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                    <option :value="null">Choose warehouse...</option>
                                    <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }} ({{ w.code }})</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500">Expected Delivery Date</label>
                                <input type="date" v-model="form.expected_at" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-gray-500">Internal Comments</label>
                                <textarea rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="Enter any notes for procurement team..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Items</span>
                                <span class="font-medium text-gray-900">{{ form.items.reduce((s,i)=>s+i.quantity,0) }} units</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Item Subtotal</span>
                                <span class="font-medium text-gray-900">${{ itemSubtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Tax Estimate (10%)</span>
                                <span class="font-medium text-gray-900">${{ tax.toFixed(2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-gray-900">${{ shipping.toFixed(2) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <div class="flex items-center justify-between text-base font-bold text-gray-900">
                                <span>Grand Total</span>
                                <span>${{ grandTotal.toFixed(2) }}</span>
                            </div>
                        </div>
                        <div class="mt-6 space-y-2">
                            <button type="button" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50" :disabled="!form.supplier_id || !form.warehouse_id || form.items.length===0" @click="submitPo">Submit for Approval</button>
                            <button type="button" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Save as Draft</button>
                        </div>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a7 7 0 100 14A7 7 0 009 2zM8 7h2v5H8V7zm0 6h2v2H8v-2z"/></svg>
                            <div>
                                <div class="font-semibold">Approval Workflow</div>
                                <div class="text-blue-700">Orders over $5,000.00 require additional approval.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
    </template>

