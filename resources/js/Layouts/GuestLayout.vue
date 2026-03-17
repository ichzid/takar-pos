<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="auth-shell flex min-h-screen items-center justify-center px-4 py-10">
        <div class="mx-auto grid w-full max-w-6xl gap-10 lg:grid-cols-2 lg:items-center">
            <div class="hidden lg:block">
                <Link href="/" class="inline-flex">
                    <ApplicationLogo />
                </Link>
                <h1 class="mt-8 max-w-xl text-4xl font-extrabold leading-tight text-surface-50">
                    Kasir modern dengan alur cepat, bersih, dan siap operasional.
                </h1>
                <p class="mt-3 max-w-xl text-sm text-stone-400">
                    Seluruh transaksi, stok, dan laporan terintegrasi dalam sistem
                    yang ringan dipakai di meja kasir.
                </p>
            </div>

            <div class="w-full">
                <div class="auth-card mx-auto w-full max-w-md">
                    <div class="mb-6 lg:hidden">
                        <Link href="/" class="inline-flex">
                            <ApplicationLogo />
                        </Link>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
