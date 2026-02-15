<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    customers: Object,
    metrics: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDateTime = (dateString) => {
    if (!dateString) {
        return '-';
    }

    const options = { year: 'numeric', month: 'short', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};
</script>

<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Customers
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tổng hợp khách hàng dựa trên đơn hàng đã phát sinh.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="text-sm text-gray-500">
                    Tổng khách hàng
                </div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">
                    {{ metrics.total_customers.value }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="text-sm text-gray-500">
                    Doanh thu có gắn khách hàng
                </div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">
                    {{ formatCurrency(metrics.total_revenue.value) }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">
                    Danh sách khách hàng
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khách hàng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số đơn
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tổng chi tiêu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Đơn gần nhất
                            </th>
                            <th class="px-6 py-3" />
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="customer in customers.data"
                            :key="customer.customer_email"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ customer.customer_name || 'Không rõ tên' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ customer.customer_email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ customer.orders_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ formatCurrency(customer.total_spent) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDateTime(customer.last_order_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="route('admin.orders.index', { search: customer.customer_email })"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Xem đơn
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="customers.data.length === 0">
                            <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                Chưa có khách hàng nào có đơn hàng.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="customers.links && customers.links.length > 0"
                class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-700"
            >
                <div>
                    Hiển thị
                    <span class="font-semibold">
                        {{ customers.from || 0 }}–{{ customers.to || 0 }}
                    </span>
                    trên
                    <span class="font-semibold">
                        {{ customers.total }}
                    </span>
                    khách hàng
                </div>
                <div class="flex gap-1">
                    <template
                        v-for="(link, index) in customers.links"
                        :key="index"
                    >
                        <span
                            v-if="!link.url"
                            class="px-3 py-1 rounded border border-gray-200 bg-gray-50 text-gray-400 text-xs"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-3 py-1 rounded border text-xs"
                            :class="link.active ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
