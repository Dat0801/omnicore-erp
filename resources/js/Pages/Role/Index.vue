<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    stats: Object,
    roles: Array,
});

const search = ref('');

const roleDescriptions = {
    admin: 'Full system access, manages global settings, security policies, and user accounts.',
    manager: 'Access to stock levels, purchase orders, warehouse reports, and vendor management modules.',
    staff: 'Create quotes, manage customers, and view personal sales dashboards.',
};

const toneClasses = {
    blue: {
        badge: 'bg-blue-50 text-blue-700 ring-blue-100',
        icon: 'text-blue-600',
    },
    emerald: {
        badge: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        icon: 'text-emerald-600',
    },
    amber: {
        badge: 'bg-amber-50 text-amber-700 ring-amber-100',
        icon: 'text-amber-600',
    },
};

const roleUserCounts = computed(() => ({
    admin: props.stats?.admins ?? 0,
    manager: props.stats?.managers ?? 0,
    staff: props.stats?.staff ?? 0,
}));

const roleRows = computed(() => props.roles.map((role) => {
    const initials = role.label
        .split(' ')
        .map(part => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();

    return {
        value: role.value,
        label: role.label,
        description: roleDescriptions[role.value] || 'No description available yet.',
        users: roleUserCounts.value[role.value] ?? 0,
        initials,
    };
}));

const filteredRoles = computed(() => {
    const query = search.value.trim().toLowerCase();
    if (!query) {
        return roleRows.value;
    }

    return roleRows.value.filter((role) => {
        return role.label.toLowerCase().includes(query)
            || role.description.toLowerCase().includes(query);
    });
});

const summaryCards = computed(() => ([
    {
        label: 'Total Roles',
        value: props.roles?.length ?? 0,
        note: 'Configured in system',
        tone: 'blue',
    },
    {
        label: 'Active Permissions',
        value: props.stats?.permissions ?? 0,
        note: 'Across all modules',
        tone: 'emerald',
    },
    {
        label: 'Recent Changes',
        value: props.stats?.recentPermissionUpdates ?? 0,
        note: 'Permissions updated in last 24h',
        tone: 'amber',
    },
]));

const goToCreateRole = () => {
    router.get(route('admin.roles.create'));
};
</script>

<template>
    <Head title="Role Management" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Role Management</h2>
                    <p class="text-sm text-gray-500 mt-1">Define and maintain access control levels across the enterprise.</p>
                </div>
                <PrimaryButton @click="goToCreateRole">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Role
                </PrimaryButton>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div v-for="card in summaryCards" :key="card.label" class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ card.label }}</p>
                            <p class="text-3xl font-semibold text-gray-900 mt-2">{{ String(card.value).padStart(2, '0') }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ card.note }}</p>
                        </div>
                        <div :class="['rounded-full ring-1 p-2', toneClasses[card.tone].badge]">
                            <svg class="w-5 h-5" :class="toneClasses[card.tone].icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full rounded-md border border-gray-200 bg-gray-50 py-2 pl-10 pr-3 text-sm text-gray-700 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Filter roles by name or description..."
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <button class="inline-flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <button class="inline-flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Role Name</th>
                                    <th class="px-6 py-3">Description</th>
                                    <th class="px-6 py-3">Users</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="role in filteredRoles" :key="role.value" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-xs font-semibold text-blue-700">
                                                {{ role.initials }}
                                            </span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ role.label }}</p>
                                                <p class="text-xs text-gray-400">{{ role.value }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ role.description }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                            {{ role.users }} Users
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="route('admin.roles.permissions.edit', role.value)"
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                        >
                                            Edit Permissions
                                        </Link>
                                        <button class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredRoles.length === 0">
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No roles match your search.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 text-sm text-gray-500 sm:flex-row">
                        <p>
                            Showing
                            <span class="font-semibold text-gray-700">{{ filteredRoles.length }}</span>
                            of
                            <span class="font-semibold text-gray-700">{{ roleRows.length }}</span>
                            roles
                        </p>
                        <div class="inline-flex items-center gap-1">
                            <button class="h-8 w-8 rounded-md border border-gray-200 text-gray-400">
                                <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button class="h-8 w-8 rounded-md border border-blue-500 bg-blue-50 text-sm font-semibold text-blue-600">1</button>
                            <button class="h-8 w-8 rounded-md border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">2</button>
                            <button class="h-8 w-8 rounded-md border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">3</button>
                            <button class="h-8 w-8 rounded-md border border-gray-200 text-gray-400">
                                <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
