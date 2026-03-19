<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function formatDate(value) {
    if (!value) return '-';
    return new Date(value).toLocaleString('id-ID');
}

// Filter state (initialized from server-side filters)
const search   = ref(props.filters.search ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo   = ref(props.filters.date_to ?? '');

function applyFilter() {
    router.get(route('orders.index'), {
        search: search.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, { preserveState: true, replace: true });
}

function resetFilter() {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get(route('orders.index'), {}, { replace: true });
}

// Export URL (attach current filters)
const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (dateFrom.value) params.set('date_from', dateFrom.value);
    if (dateTo.value) params.set('date_to', dateTo.value);
    const qs = params.toString();
    return route('orders.export') + (qs ? `?${qs}` : '');
});

const stats = computed(() => {
    const rows = props.orders.data || [];
    const totalRevenue = rows.reduce((sum, order) => sum + Number(order.total || 0), 0);
    const totalItems = rows.reduce((sum, order) => sum + Number(order.details_count || 0), 0);

    return {
        count: props.orders.total ?? rows.length,
        totalRevenue,
        avg: rows.length ? Math.round(totalRevenue / rows.length) : 0,
        totalItems,
    };
});

const isFiltered = computed(
    () => search.value || dateFrom.value || dateTo.value,
);
</script>

<template>
    <Head title="Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h1 class="page-title">Transaksi</h1>
                    <p class="page-subtitle">Riwayat semua transaksi penjualan</p>
                </div>

                <div class="flex w-full flex-col gap-2 xl:w-auto xl:flex-row xl:flex-nowrap xl:items-end xl:justify-end">
                    <div class="w-full xl:w-[240px]">
                        <input
                            v-model="search"
                            class="input-base h-9 text-sm"
                            type="text"
                            placeholder="Cari order / kasir..."
                            @keyup.enter="applyFilter"
                        />
                    </div>

                    <div class="w-full sm:w-[170px] xl:w-[170px]">
                        <input
                            v-model="dateFrom"
                            class="input-base h-9 text-sm"
                            type="date"
                            title="Dari tanggal"
                        />
                    </div>

                    <div class="hidden h-9 items-center px-1 text-xs text-stone-500 xl:flex">s/d</div>

                    <div class="w-full sm:w-[170px] xl:w-[170px]">
                        <input
                            v-model="dateTo"
                            class="input-base h-9 text-sm"
                            type="date"
                            title="Sampai tanggal"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2 xl:flex-nowrap">
                        <button class="btn-primary h-9 px-4 text-sm" @click="applyFilter">Filter</button>
                        <button
                            v-if="isFiltered"
                            class="btn-secondary h-9 px-3 text-sm"
                            @click="resetFilter"
                        >
                            Reset
                        </button>

                        <a
                            :href="exportUrl"
                            class="btn-secondary flex h-9 items-center gap-1.5 px-4 text-sm"
                            download
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <section class="page-wrap space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="card-sm p-4">
                    <div class="mb-1 text-xs text-stone-500">Total Transaksi</div>
                    <div class="mono text-xl font-extrabold">{{ stats.count }}</div>
                </div>
                <div class="card-sm p-4">
                    <div class="mb-1 text-xs text-stone-500">Total Pendapatan</div>
                    <div class="mono text-xl font-extrabold text-emerald-400">
                        {{ currency.format(stats.totalRevenue) }}
                    </div>
                </div>
                <div class="card-sm p-4">
                    <div class="mb-1 text-xs text-stone-500">Rata-rata / Transaksi</div>
                    <div class="mono text-xl font-extrabold text-brand-400">
                        {{ currency.format(stats.avg) }}
                    </div>
                </div>
                <div class="card-sm p-4">
                    <div class="mb-1 text-xs text-stone-500">Item Terjual</div>
                    <div class="mono text-xl font-extrabold text-blue-400">{{ stats.totalItems }}</div>
                </div>
            </div>

            <div v-if="isFiltered" class="flex items-center gap-2 text-xs text-stone-400">
                <svg class="h-4 w-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                Filter aktif -
                <button class="text-brand-400 underline hover:text-brand-300" @click="resetFilter">hapus filter</button>
            </div>

            <div class="table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>#Order</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Pelanggan</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Pajak</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="mono font-semibold text-brand-400">{{ order.order_number }}</td>
                            <td class="text-xs text-stone-400">{{ formatDate(order.created_at) }}</td>
                            <td class="text-xs text-stone-300">{{ order.user?.name || '-' }}</td>
                            <td class="text-xs text-stone-400">{{ order.customer_name || '-' }}</td>
                            <td class="mono">{{ currency.format(order.subtotal) }}</td>
                            <td class="mono text-stone-500">
                                {{ order.discount_amount > 0 ? currency.format(order.discount_amount) : '-' }}
                            </td>
                            <td class="mono text-stone-500">{{ currency.format(order.tax_amount) }}</td>
                            <td class="mono font-bold">{{ currency.format(order.total) }}</td>
                            <td>
                                <Link
                                    :href="route('orders.show', order.id)"
                                    class="btn-secondary px-3 py-1.5 text-xs"
                                >
                                    Detail
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="9" class="py-10 text-center text-sm text-stone-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-10 w-10 text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Belum ada transaksi
                                    <span v-if="isFiltered" class="text-xs">yang cocok dengan filter.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="orders.links" />
        </section>
    </AuthenticatedLayout>
</template>
