<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
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
                        <Link :href="route('login')" class="btn-login">
                            Masuk
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="flex flex-1 items-center">
                <section class="mx-auto max-w-3xl py-16 text-center">
                    <h1 class="text-4xl font-extrabold leading-tight text-surface-50 sm:text-5xl lg:text-6xl">
                        Kasir rapi. Jualan cepat. Operasional lebih tenang.
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-stone-400 sm:text-lg">
                        Takar POS membantu Anda mengelola transaksi, produk, stok,
                        pengguna, dan laporan dalam satu sistem yang simpel dan enak dipakai setiap hari.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
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
                            Masuk ke Sistem
                        </Link>
                    </div>

                    <div class="mt-10 flex flex-wrap justify-center gap-x-8 gap-y-3 text-sm text-stone-500">
                        <span>Transaksi lebih cepat</span>
                        <span>Stok lebih terkontrol</span>
                        <span>Laporan lebih mudah dibaca</span>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
