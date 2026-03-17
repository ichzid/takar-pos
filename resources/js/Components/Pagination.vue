<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <Link
            v-for="link in links"
            :key="link.url ?? link.label"
            :href="link.url || ''"
            class="rounded-lg border px-3 py-1.5 text-sm font-medium transition"
            :class="{
                'pointer-events-none border-surface-800 bg-surface-900 text-stone-500': !link.url,
                'border-brand-500 bg-brand-500 text-white shadow-[0_4px_16px_rgba(249,115,22,0.35)]':
                    link.active,
                'border-surface-800 bg-surface-900 text-stone-200 hover:bg-surface-800':
                    link.url && !link.active,
            }"
            v-html="link.label"
        />
    </div>
</template>
