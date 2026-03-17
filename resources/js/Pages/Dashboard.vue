<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            today_revenue: 0,
            today_orders: 0,
            total_products: 0,
            total_categories: 0,
        }),
    },
    weeklySales: {
        type: Array,
        default: () => [],
    },
    topProducts: {
        type: Array,
        default: () => [],
    },
    recentOrders: {
        type: Array,
        default: () => [],
    },
});

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const maxWeekly = computed(() => {
    if (!props.weeklySales.length) return 1;
    return Math.max(...props.weeklySales.map((item) => Number(item.value) || 0), 1);
});

const maxTopQty = computed(() => {
    if (!props.topProducts.length) return 1;
    return Math.max(...props.topProducts.map((item) => Number(item.qty) || 0), 1);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang kembali, {{ $page.props.auth.user.name }}</p>
                </div>
                <div class="text-right">
                    <div class="mono text-xs text-stone-500">
                        {{ new Date().toLocaleTimeString('id-ID') }}
                    </div>
                    <div class="mt-1 text-xs text-stone-400">
                        {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' }) }}
                    </div>
                </div>
            </div>
        </template>

        <section class="page-wrap space-y-6">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="card stat-card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="label">Pendapatan Hari Ini</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mono text-2xl font-extrabold">
                        {{ currency.format(Number(stats.today_revenue) || 0) }}
                    </div>
                    <span class="badge mt-2 bg-emerald-500/10 text-emerald-400">Hari ini</span>
                </div>

                <div class="card stat-card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="label">Total Transaksi</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10">
                            <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                        </div>
                    </div>
                    <div class="mono text-2xl font-extrabold">{{ stats.today_orders }}</div>
                    <span class="badge mt-2 bg-blue-500/10 text-blue-400">Hari ini</span>
                </div>

                <div class="card stat-card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="label">Total Produk</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500/10">
                            <svg class="h-4 w-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mono text-2xl font-extrabold">{{ stats.total_products }}</div>
                    <span class="badge mt-2 bg-brand-500/10 text-brand-400">Aktif</span>
                </div>

                <div class="card stat-card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="label">Kategori</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-500/10">
                            <svg class="h-4 w-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mono text-2xl font-extrabold">{{ stats.total_categories }}</div>
                    <span class="badge mt-2 bg-purple-500/10 text-purple-400">Total</span>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <div class="card p-6 xl:col-span-3">
                    <h3 class="mb-6 font-bold">Penjualan 7 Hari Terakhir</h3>
                    <div class="flex h-40 items-end gap-3">
                        <div
                            v-for="(item, index) in weeklySales"
                            :key="item.label"
                            class="flex flex-1 flex-col items-center gap-2"
                        >
                            <div class="mono text-xs text-stone-500">
                                {{ currency.format(Number(item.value) || 0).replace('Rp', '').trim() }}
                            </div>
                            <div
                                class="w-full rounded-t-md"
                                :class="index === weeklySales.length - 1 ? 'bg-brand-500' : 'bg-[#3d3734]'"
                                :style="{ height: `${((Number(item.value) || 0) / maxWeekly) * 120}px` }"
                            ></div>
                        </div>
                    </div>
                    <div class="mono mt-3 flex justify-between text-xs text-stone-500">
                        <span v-for="item in weeklySales" :key="`day-${item.label}`">{{ item.short }}</span>
                    </div>
                </div>

                <div class="card p-6 xl:col-span-2">
                    <h3 class="mb-4 font-bold">Produk Terlaris</h3>
                    <div class="space-y-3" v-if="topProducts.length">
                        <div v-for="product in topProducts" :key="product.name">
                            <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                                <span class="truncate">{{ product.name }}</span>
                                <span class="mono flex-shrink-0 font-semibold text-stone-300">
                                    {{ product.qty }} terjual
                                </span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" :style="{ width: `${(Number(product.qty) / maxTopQty) * 100}%` }"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-sm text-stone-500">
                        Belum ada data penjualan produk.
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <div class="flex items-center justify-between border-b border-surface-800 p-5">
                    <h3 class="font-bold">Transaksi Terbaru</h3>
                    <Link
                        v-if="$page.props.auth.user.role === 'admin'"
                        :href="route('orders.index')"
                        class="btn-secondary px-3 py-2 text-xs"
                    >
                        Lihat Semua
                    </Link>
                </div>
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>#Order</th>
                            <th>Waktu</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in recentOrders" :key="order.order_number">
                            <td class="mono font-semibold text-brand-400">{{ order.order_number }}</td>
                            <td class="text-stone-400">{{ order.time }}</td>
                            <td class="text-stone-300">{{ order.items_count }} item</td>
                            <td class="mono font-semibold">{{ currency.format(Number(order.total) || 0) }}</td>
                            <td>
                                <span class="badge bg-emerald-500/10 text-emerald-400">
                                    {{ order.status || 'Selesai' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!recentOrders.length">
                            <td colspan="5" class="py-5 text-center text-sm text-stone-500">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
