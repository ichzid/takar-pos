<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
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

const orderDate = computed(() => {
    if (!props.order?.created_at) return '-';
    return new Date(props.order.created_at).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

function printReceipt() {
    window.print();
}
</script>

<template>
    <Head title="Struk Transaksi" />

    <AuthenticatedLayout>
        <div class="page-wrap printable-receipt mx-auto max-w-xl">
            <!-- Action bar -->
            <div class="mb-6 flex items-center justify-between no-print">
                <div>
                    <h1 class="page-title text-xl">Struk Transaksi</h1>
                    <p class="mt-0.5 text-xs text-stone-500">Transaksi berhasil disimpan</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('pos.index')" class="btn-secondary">
                        ← Kembali ke POS
                    </Link>
                    <button class="btn-primary" @click="printReceipt">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Struk
                    </button>
                </div>
            </div>

            <!-- Receipt card -->
            <div class="receipt">
                <!-- Store header -->
                <div class="mb-5 border-b border-dashed border-slate-300 pb-4 text-center">
                    <div class="text-xl font-extrabold tracking-tight">KasirKu</div>
                    <div class="mt-0.5 text-xs text-slate-500">Point of Sale System</div>
                </div>

                <!-- Order meta -->
                <div class="mb-4 space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">No. Order</span>
                        <!-- FIX: was order.id (integer), now shows correct order_number -->
                        <span class="font-bold tracking-wide">{{ order.order_number }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Tanggal</span>
                        <span>{{ orderDate }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Kasir</span>
                        <span>{{ order.user?.name || '—' }}</span>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-4 border-y border-dashed border-slate-300 py-3">
                    <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Detail Produk</div>
                    <div class="space-y-2.5 text-xs">
                        <div
                            v-for="detail in order.details || []"
                            :key="detail.id"
                            class="flex justify-between gap-2"
                        >
                            <div class="min-w-0">
                                <div class="truncate font-semibold">{{ detail.product?.name || 'Produk' }}</div>
                                <div class="text-slate-400">
                                    {{ detail.quantity }} × {{ currency.format(Number(detail.unit_price) || 0) }}
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right font-semibold">
                                {{ currency.format(Number(detail.subtotal) || 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span>{{ currency.format(Number(order.subtotal) || 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Pajak</span>
                        <span>{{ currency.format(Number(order.tax_amount) || 0) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-dashed border-slate-300 pt-2 font-bold">
                        <span>Total</span>
                        <span>{{ currency.format(Number(order.total) || 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Bayar</span>
                        <span>{{ currency.format(Number(order.paid_amount) || 0) }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>Kembalian</span>
                        <span>{{ currency.format(Number(order.change_amount) || 0) }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 border-t border-dashed border-slate-300 pt-4 text-center text-[11px] text-slate-400">
                    Terima kasih sudah berbelanja!<br>Selamat datang kembali 🙏
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
