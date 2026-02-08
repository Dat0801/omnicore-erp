<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object,
    roles: Array,
});

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || 'all');
const status = ref(props.filters.status || 'all');

const updateFilters = debounce(() => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: role.value,
        status: status.value,
    }, { preserveState: true, replace: true });
}, 300);

watch([search, role, status], updateFilters);

const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'admin':
            return 'bg-blue-600 text-white';
        case 'manager':
            return 'bg-indigo-100 text-indigo-700';
        case 'staff':
            return 'bg-gray-100 text-gray-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
};

const getRoleLabel = (roleValue) => {
    const r = props.roles.find(r => r.value === roleValue);
    return r ? r.label : roleValue;
};
</script>

<template>
    <Head title="User & Role Management" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">User & Role Management</h2>
                    <p class="text-sm text-gray-500 mt-1">Configure user access levels and internal permissions.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Manage Roles
                    </button>
                    <PrimaryButton>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Add New User
                    </PrimaryButton>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        placeholder="Search by name, email, or role..."
                    >
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select v-model="role" class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all">Role: All</option>
                        <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                    </select>

                    <select v-model="status" class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all">Status: All</option>
                        <option value="active">Status: Active</option>
                        <option value="inactive">Status: Disabled</option>
                    </select>

                    <button class="p-2 border border-gray-300 rounded-md hover:bg-gray-50 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" :src="user.avatar_url" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase', getRoleBadgeClass(user.role)]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="user.is_active" class="flex items-center text-sm text-gray-500">
                                        <span class="h-2 w-2 bg-green-400 rounded-full mr-2"></span>
                                        Active
                                    </span>
                                    <span v-else class="flex items-center text-sm text-gray-400 italic">
                                        <span class="h-2 w-2 bg-gray-300 rounded-full mr-2"></span>
                                        Disabled
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.last_login_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-gray-600 mx-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 mx-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ users.from || 0 }}</span>
                                to
                                <span class="font-medium">{{ users.to || 0 }}</span>
                                of
                                <span class="font-medium">{{ users.total }}</span>
                                users
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link v-for="(link, k) in users.links" 
                                    :key="k" 
                                    :href="link.url || '#'" 
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        k === 0 ? 'rounded-l-md' : '',
                                        k === users.links.length - 1 ? 'rounded-r-md' : '',
                                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                                    ]"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Admins -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Admins</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ String(stats.admins).padStart(2, '0') }}</h3>
                            <p class="text-xs text-green-500 mt-2">Full System Access</p>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-full">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Managers -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Managers</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ String(stats.managers).padStart(2, '0') }}</h3>
                            <p class="text-xs text-gray-500 mt-2">Departmental Access</p>
                        </div>
                        <div class="p-2 bg-indigo-50 rounded-full">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Staff -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Staff</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ String(stats.staff).padStart(2, '0') }}</h3>
                            <p class="text-xs text-gray-500 mt-2">Operational Access</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-full">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
