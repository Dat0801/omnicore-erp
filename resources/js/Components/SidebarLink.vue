<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'group flex items-center px-3 py-2 text-sm font-medium rounded-md bg-indigo-50 text-indigo-700 dark:bg-gray-800 dark:text-white'
        : 'group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
