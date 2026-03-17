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
        ? 'block w-full rounded-[10px] bg-brand-500 px-[14px] py-[10px] text-start text-sm font-medium text-white shadow-[0_4px_16px_rgba(249,115,22,0.35)] transition'
        : 'block w-full rounded-[10px] px-[14px] py-[10px] text-start text-sm font-medium text-stone-400 transition hover:bg-surface-800 hover:text-surface-50',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
