<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    role: Object,
    actions: Array,
    matrix: Array,
});

const roleName = ref(props.role?.label ?? '');
const roleDescription = ref('Handles product catalog updates, stock adjustments, and warehouse reporting.');
const isActive = ref(true);
const restrictAdminAccess = ref(true);
const dataExportRights = ref(false);

const columns = computed(() => props.actions ?? []);

const permissionRows = ref(
    (props.matrix ?? []).map((row) => {
        const permissions = {};

        (row.permissions ?? []).forEach((permission) => {
            permissions[permission.key] = {
                id: permission.id,
                checked: permission.assigned,
            };
        });

        return {
            moduleKey: row.module,
            module: row.label ?? row.module,
            description: row.description ?? '',
            permissions,
        };
    }),
);

const form = useForm({
    permissions: [],
});

const setAllPermissions = (value) => {
    permissionRows.value.forEach((row) => {
        columns.value.forEach((column) => {
            const permission = row.permissions[column.key];

            if (permission && permission.id) {
                permission.checked = value;
            }
        });
    });
};

const collectSelectedPermissionIds = () => {
    const selected = [];

    permissionRows.value.forEach((row) => {
        Object.values(row.permissions).forEach((permission) => {
            if (permission.id && permission.checked) {
                selected.push(permission.id);
            }
        });
    });

    return selected;
};

const submit = () => {
    form.permissions = collectSelectedPermissionIds();

    form.put(route('admin.roles.permissions.update', props.role.value));
};

const roleInitials = computed(() => {
    if (!roleName.value) {
        return 'NA';
    }

    return roleName.value
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});
</script>

<template>
    <Head title="Edit Permissions" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-white via-slate-50 to-blue-50 px-6 py-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <Link :href="route('admin.users.index')" class="hover:text-slate-700">User Management</Link>
                            <span>/</span>
                            <Link :href="route('admin.roles.index')" class="hover:text-slate-700">Roles</Link>
                            <span>/</span>
                            <span class="text-slate-700">Edit Permissions</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight font-serif">{{ roleName }}</h1>
                            <p class="text-sm text-slate-500">Configure granular access levels for warehouse and stock operations.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="inline-flex items-center rounded-md border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-600 shadow-sm transition hover:bg-slate-50">
                            Cancel
                        </button>
                        <PrimaryButton @click="submit" :disabled="form.processing">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7v14" />
                            </svg>
                            Save Changes
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-sm font-semibold text-blue-700">
                                {{ roleInitials }}
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Role Identity</p>
                                <p class="text-sm font-semibold text-slate-800">{{ roleName }}</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">Role Name</label>
                                <input
                                    v-model="roleName"
                                    type="text"
                                    class="mt-2 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">Description</label>
                                <textarea
                                    v-model="roleDescription"
                                    rows="4"
                                    class="mt-2 w-full resize-none rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="mt-5 rounded-lg border border-blue-100 bg-blue-50/60 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Status</p>
                                    <p class="text-sm font-medium text-blue-900">Active Role</p>
                                </div>
                                <button
                                    type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                    :class="isActive ? 'bg-blue-600' : 'bg-slate-300'"
                                    @click="isActive = !isActive"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                        :class="isActive ? 'translate-x-6' : 'translate-x-1'"
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105 1.343-2 3-2s3 .895 3 2-1.343 2-3 2-3 .895-3 2m0 4h.01" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.93 4.93a10 10 0 0114.14 14.14 10 10 0 01-14.14-14.14z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Need Help?</p>
                                <p class="text-xs text-slate-500">Changes to permissions take effect the next time a user with this role logs in.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
                        <div class="flex flex-col gap-2 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Permission Matrix</p>
                                <p class="text-sm text-slate-500">Select granular capabilities for each module.</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-widest text-blue-600">
                                <button type="button" class="hover:text-blue-700" @click="setAllPermissions(true)">Select All</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" class="hover:text-blue-700" @click="setAllPermissions(false)">Clear All</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
                                    <tr>
                                        <th class="px-6 py-3">Module</th>
                                        <th
                                            v-for="column in columns"
                                            :key="column.key"
                                            class="px-4 py-3 text-center"
                                        >
                                            {{ column.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr
                                        v-for="row in permissionRows"
                                        :key="row.moduleKey"
                                        class="hover:bg-slate-50/70"
                                    >
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ row.module }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ row.description }}
                                            </p>
                                        </td>
                                        <td
                                            v-for="column in columns"
                                            :key="column.key"
                                            class="px-4 py-4 text-center"
                                        >
                                            <input
                                                v-if="row.permissions[column.key]?.id"
                                                v-model="row.permissions[column.key].checked"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span
                                                v-else
                                                class="inline-block h-4 w-4 rounded border border-dashed border-slate-200 bg-slate-50"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19.07a10 10 0 0113.86-13.86 10 10 0 01-13.86 13.86z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Restrict Admin Access</p>
                                    <p class="text-xs text-slate-500">Prevent this role from accessing system settings.</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                :class="restrictAdminAccess ? 'bg-blue-600' : 'bg-slate-300'"
                                @click="restrictAdminAccess = !restrictAdminAccess"
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                    :class="restrictAdminAccess ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 008 0V7a4 4 0 10-8 0v9z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Data Export Rights</p>
                                    <p class="text-xs text-slate-500">Allow user to export data as CSV/Excel.</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                :class="dataExportRights ? 'bg-blue-600' : 'bg-slate-300'"
                                @click="dataExportRights = !dataExportRights"
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                    :class="dataExportRights ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
