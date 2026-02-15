<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    next_statuses: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    status: props.order.status,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDateTime = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        picking: 'bg-indigo-100 text-indigo-800',
        packed: 'bg-purple-100 text-purple-800',
        shipped: 'bg-sky-100 text-sky-800',
        delivered: 'bg-green-100 text-green-800',
        refunded: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };

    const key = status.toLowerCase();

    return colors[key] || 'bg-gray-100 text-gray-800';
};

const submitStatus = () => {
    form.patch(route('admin.orders.update-status', props.order.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.orders.index')" class="text-gray-500 hover:text-gray-700">
                    ← Back to Orders
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">
                    Order #{{ order.id }}
                </h2>
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusColor(order.status)"
                >
                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                </span>
            </div>
            <div class="text-right text-sm text-gray-500">
                <div>Created: {{ formatDateTime(order.created_at) }}</div>
                <div v-if="order.updated_at !== order.created_at">
                    Updated: {{ formatDateTime(order.updated_at) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Order Summary
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">
                                Source
                            </dt>
                            <dd class="mt-1 text-gray-900">
                                {{ order.source }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">
                                External ID
                            </dt>
                            <dd class="mt-1 text-gray-900">
                                {{ order.source_id }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">
                                Customer Name
                            </dt>
                            <dd class="mt-1 text-gray-900">
                                {{ order.customer_name || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">
                                Customer Email
                            </dt>
                            <dd class="mt-1 text-gray-900">
                                {{ order.customer_email || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">
                                Warehouse
                            </dt>
                            <dd class="mt-1 text-gray-900">
                                {{ order.warehouse ? `${order.warehouse.name} (${order.warehouse.code})` : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">
                                Total Amount
                            </dt>
                            <dd class="mt-1 text-gray-900 font-semibold">
                                {{ formatCurrency(order.total_amount) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Items
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ item.product_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ item.quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ formatCurrency(item.price * item.quantity) }}
                                    </td>
                                </tr>
                                <tr v-if="!order.items || order.items.length === 0">
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No items found for this order.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Fulfillment Status
                    </h3>
                    <form @submit.prevent="submitStatus" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Next Status
                            </label>
                            <select
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                            >
                                <option :value="order.status">
                                    Current: {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                </option>
                                <option
                                    v-for="status in next_statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                </option>
                            </select>
                            <div
                                v-if="form.errors.status"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.status }}
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                            :disabled="form.processing || next_statuses.length === 0"
                        >
                            Update Status
                        </button>
                        <p
                            v-if="next_statuses.length === 0"
                            class="mt-2 text-xs text-gray-500"
                        >
                            No further transitions available from the current status.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

