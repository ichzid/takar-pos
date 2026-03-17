<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    order: {
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

function printReceipt() {
    window.print();
}
</script>

<template>
    <Head title="Order Detail" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Order Detail</h1>
                    <p class="page-subtitle">Rincian transaksi pelanggan</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('orders.index')" class="btn-secondary">Kembali</Link>
                    <button class="btn-primary" @click="printReceipt">Cetak Struk</button>
                </div>
            </div>
        </template>

        <section class="page-wrap">
            <div class="card printable-receipt max-w-4xl p-6">
                <div class="mb-4 space-y-1 text-sm text-stone-400">
                    <div>Nomor: {{ order.order_number }}</div>
                    <div>Kasir: {{ order.user?.name }}</div>
                    <div>Status: {{ order.status }}</div>
                    <div>Tanggal: {{ formatDate(order.created_at) }}</div>
                </div>

                <div class="overflow-hidden rounded-xl border border-surface-800">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="detail in order.details" :key="detail.id">
                                <td>{{ detail.product?.name }}</td>
                                <td>{{ detail.quantity }}</td>
                                <td>{{ currency.format(detail.unit_price) }}</td>
                                <td>{{ currency.format(detail.line_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 space-y-1 text-sm text-stone-300">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>{{ currency.format(order.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pajak</span>
                        <span>{{ currency.format(order.tax_amount) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-surface-50">
                        <span>Total</span>
                        <span>{{ currency.format(order.total) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Bayar</span>
                        <span>{{ currency.format(order.paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kembalian</span>
                        <span>{{ currency.format(order.change_amount) }}</span>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
