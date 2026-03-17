<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    categories: {
        type: Object,
        required: true,
    },
});

function destroyCategory(category) {
    if (!confirm(`Hapus kategori "${category.name}"?`)) return;
    router.delete(route('categories.destroy', category.id));
}
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">Kategori</h1>
                    <p class="page-subtitle">Kelola kategori produk Anda</p>
                </div>
                <Link :href="route('categories.create')" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori
                </Link>
            </div>
        </template>

        <section class="page-wrap space-y-5">
            <div class="table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Jumlah Produk</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories.data" :key="category.id">
                            <td class="font-semibold">{{ category.name }}</td>
                            <td class="text-stone-400">{{ category.products_count || 0 }} produk</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="route('categories.edit', category.id)"
                                        class="btn-secondary px-3 py-1.5 text-xs"
                                    >
                                        Edit
                                    </Link>
                                    <button class="btn-danger" @click="destroyCategory(category)">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.data.length === 0">
                            <td colspan="3" class="py-6 text-center text-sm text-stone-500">
                                Belum ada kategori.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="categories.links" />
        </section>
    </AuthenticatedLayout>
</template>
