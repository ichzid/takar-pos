<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div class="auth-shell min-h-screen">
        <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-6 py-10">
            <header class="flex items-center justify-between">
                <ApplicationLogo />

                <nav v-if="canLogin" class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="btn-primary"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="btn-ghost">
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="btn-primary"
                        >
                            Daftar
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="mt-16 grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="label">Kashier Suite</p>
                    <h1 class="mt-3 text-4xl font-extrabold text-surface-50 sm:text-5xl">
                        Kasir modern yang rapi, cepat, dan profesional.
                    </h1>
                    <p class="mt-4 text-base text-stone-400">
                        Dirancang untuk toko yang ingin tampil elegan sekaligus
                        operasional yang stabil. Semua aktivitas penjualan,
                        stok, dan laporan terangkum dalam satu tempat.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="btn-primary"
                        >
                            Buka Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="route('login')"
                            class="btn-primary"
                        >
                            Mulai Sekarang
                        </Link>
                        <Link
                            v-if="canRegister && !$page.props.auth.user"
                            :href="route('register')"
                            class="btn-secondary"
                        >
                            Buat Akun
                        </Link>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="card p-4">
                            <p class="label">Realtime</p>
                            <p class="mt-2 text-sm font-semibold text-surface-50">
                                Pantau transaksi detik ini.
                            </p>
                        </div>
                        <div class="card p-4">
                            <p class="label">Terukur</p>
                            <p class="mt-2 text-sm font-semibold text-surface-50">
                                Laporan jelas dan cepat.
                            </p>
                        </div>
                        <div class="card p-4">
                            <p class="label">Ringan</p>
                            <p class="mt-2 text-sm font-semibold text-surface-50">
                                Tetap responsif di kasir.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="grid gap-4">
                        <div class="rounded-2xl border border-surface-800 bg-surface-800 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">
                                Today
                            </p>
                            <p class="mono mt-2 text-2xl font-semibold text-surface-50">
                                Rp 12.450.000
                            </p>
                            <p class="mt-1 text-sm text-stone-400">
                                Total pendapatan hari ini.
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-surface-800 bg-surface-900 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">
                                    Orders
                                </p>
                                <p class="mono mt-2 text-lg font-semibold text-surface-50">
                                    128
                                </p>
                            </div>
                            <div class="rounded-2xl border border-surface-800 bg-surface-900 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">
                                    Stock
                                </p>
                                <p class="mono mt-2 text-lg font-semibold text-surface-50">
                                    5.342
                                </p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-surface-800 bg-surface-900 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">
                                Insight
                            </p>
                            <p class="mt-2 text-sm text-stone-400">
                                Produk terlaris hari ini: Signature Latte.
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="mt-16 text-xs text-stone-500">
                Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
            </footer>
        </div>
    </div>
</template>
