<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    orders: {
        type: Object,
        required: true,
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

const stats = computed(() => {
    const rows = props.orders.data || [];
    const totalRevenue = rows.reduce((sum, order) => sum + Number(order.total || 0), 0);
    const totalItems = rows.reduce((sum, order) => sum + Number(order.details_count || 0), 0);

    return {
        count: rows.length,
        totalRevenue,
        avg: rows.length ? Math.round(totalRevenue / rows.length) : 0,
        totalItems,
    };
});
</script>

<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">Orders</h1>
                    <p class="page-subtitle">Riwayat semua transaksi</p>
                </div>
                <div class="flex gap-3">
                    <input class="input-base w-40" type="date" />
                    <button class="btn-secondary">Filter</button>
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
                    <div class="mono text-xl font-extrabold text-blue-400">
                        {{ stats.totalItems }}
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>#Order</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Subtotal</th>
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
                            <td class="mono">{{ currency.format(order.subtotal) }}</td>
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
                            <td colspan="7" class="py-6 text-center text-sm text-stone-500">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="orders.links" />
        </section>
    </AuthenticatedLayout>
</template>
