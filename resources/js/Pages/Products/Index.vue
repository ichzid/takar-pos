<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function submitSearch() {
    router.get(
        route('products.index'),
        { search: search.value },
        { preserveState: true, replace: true },
    );
}

function destroyProduct(product) {
    if (!confirm(`Hapus produk "${product.name}"?`)) return;
    router.delete(route('products.destroy', product.id));
}
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">Produk</h1>
                    <p class="page-subtitle">Kelola semua produk toko Anda</p>
                </div>
                <Link :href="route('products.create')" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </Link>
            </div>
        </template>

        <section class="page-wrap space-y-5">
            <div
                v-if="$page.props.flash?.success"
                class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                v-if="$page.props.flash?.error"
                class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300"
            >
                {{ $page.props.flash.error }}
            </div>

            <div class="flex gap-3">
                <div class="relative w-full max-w-sm">
                    <svg
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35" />
                    </svg>
                    <input
                        v-model="search"
                        class="input-base pl-10"
                        placeholder="Cari produk..."
                        @keyup.enter="submitSearch"
                    />
                </div>
                <button class="btn-secondary" @click="submitSearch">Cari</button>
            </div>

            <div class="table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id">
                            <td>
                                <div class="flex h-[50px] w-[50px] items-center justify-center rounded-[10px] bg-surface-800">
                                    <svg
                                        width="20"
                                        height="20"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="#57534e"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                            </td>
                            <td>
                                <span class="font-semibold">{{ product.name }}</span>
                            </td>
                            <td>
                                <span
                                    v-if="product.category?.name"
                                    class="badge bg-brand-500/10 text-brand-400"
                                >
                                    {{ product.category.name }}
                                </span>
                                <span v-else class="text-stone-500">-</span>
                            </td>
                            <td class="mono font-semibold text-brand-400">
                                {{ currency.format(product.sell_price) }}
                            </td>
                            <td>
                                <span
                                    class="mono font-semibold"
                                    :class="product.stock < 10 ? 'text-red-400' : 'text-stone-200'"
                                >
                                    {{ product.stock }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-emerald-500/10 text-emerald-400">
                                    Aktif
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('products.edit', product.id)"
                                        class="btn-secondary px-3 py-1.5 text-xs"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        class="btn-danger"
                                        :disabled="product.order_details_count > 0"
                                        :title="
                                            product.order_details_count > 0
                                                ? 'Produk ini sudah dipakai transaksi'
                                                : 'Hapus produk'
                                        "
                                        @click="destroyProduct(product)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <div
                                    v-if="product.order_details_count > 0"
                                    class="mt-1 text-[11px] text-stone-500"
                                >
                                    Dipakai di transaksi
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="7" class="py-6 text-center text-sm text-stone-500">
                                Belum ada produk.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="products.links" />
        </section>
    </AuthenticatedLayout>
</template>
