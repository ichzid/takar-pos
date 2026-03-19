<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const s = page.props.store_settings ?? {};

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function formatDate(value) {
    if (!value) return '-';
    const d = new Date(value);
    return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function printReceipt() {
    window.print();
}
</script>

<template>
    <Head title="Detail Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Detail Transaksi</h1>
                    <p class="page-subtitle">{{ order.order_number }}</p>
                </div>
                <div class="no-print flex items-center gap-2">
                    <Link :href="route('orders.index')" class="btn-secondary">← Kembali</Link>
                    <button class="btn-primary flex items-center gap-2" @click="printReceipt">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Struk
                    </button>
                </div>
            </div>
        </template>

        <section class="page-wrap flex justify-center">
            <!-- Receipt card -->
            <div class="receipt-card w-full max-w-md rounded-2xl border border-surface-800 bg-surface-950 p-6 shadow-2xl">

                <!-- Store header -->
                <div class="mb-5 text-center">
                    <div class="text-lg font-extrabold tracking-tight text-surface-50">
                        {{ s.store_name || 'KasirKu' }}
                    </div>
                    <div v-if="s.store_address" class="mt-0.5 text-xs text-stone-500">{{ s.store_address }}</div>
                    <div v-if="s.store_phone"   class="text-xs text-stone-500">{{ s.store_phone }}</div>
                    <div class="mt-3 border-t border-dashed border-surface-700 pt-3 text-xs text-stone-500">
                        <div>No: <span class="mono font-semibold text-brand-400">{{ order.order_number }}</span></div>
                        <div>{{ formatDate(order.created_at) }}</div>
                        <div>Kasir: <span class="text-stone-300">{{ order.user?.name ?? '-' }}</span></div>
                        <div v-if="order.customer_name">Pelanggan: <span class="text-stone-300">{{ order.customer_name }}</span></div>
                    </div>
                </div>

                <!-- Line items -->
                <div class="mb-4 space-y-2">
                    <div
                        v-for="detail in order.details"
                        :key="detail.id"
                        class="flex items-start justify-between gap-2"
                    >
                        <div class="flex-1">
                            <div class="text-sm font-medium text-surface-50">{{ detail.product?.name }}</div>
                            <div class="mono text-xs text-stone-500">
                                {{ detail.quantity }} × {{ currency.format(detail.unit_price) }}
                            </div>
                        </div>
                        <div class="mono text-sm font-semibold text-surface-50 tabular-nums">
                            {{ currency.format(detail.line_total) }}
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="space-y-1.5 border-t border-dashed border-surface-700 pt-4 text-sm">
                    <div class="flex justify-between text-stone-400">
                        <span>Subtotal</span>
                        <span class="mono">{{ currency.format(order.subtotal) }}</span>
                    </div>
                    <div v-if="Number(order.discount_amount) > 0" class="flex justify-between text-emerald-400">
                        <span>Diskon</span>
                        <span class="mono">− {{ currency.format(order.discount_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-400">
                        <span>Pajak</span>
                        <span class="mono">{{ currency.format(order.tax_amount) }}</span>
                    </div>
                    <div class="flex justify-between rounded-lg bg-surface-800 px-3 py-2 text-base font-extrabold text-surface-50">
                        <span>TOTAL</span>
                        <span class="mono text-brand-400">{{ currency.format(order.total) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-400">
                        <span>Bayar</span>
                        <span class="mono">{{ currency.format(order.paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-emerald-400">
                        <span>Kembalian</span>
                        <span class="mono">{{ currency.format(order.change_amount) }}</span>
                    </div>
                </div>

                <!-- Note -->
                <div v-if="order.note" class="mt-4 rounded-lg border border-surface-700 bg-surface-800 px-3 py-2 text-xs text-stone-400">
                    <span class="font-semibold text-stone-300">Catatan:</span> {{ order.note }}
                </div>

                <!-- Footer message -->
                <div class="mt-5 border-t border-dashed border-surface-700 pt-4 text-center text-xs text-stone-500">
                    {{ s.receipt_message || 'Terima kasih sudah berbelanja!' }}
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
