<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const showDemoAccounts = computed(() => page.component === 'Auth/Login');
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

                <div v-if="showDemoAccounts" class="mt-6 max-w-xl text-sm text-stone-400">
                    <div class="font-semibold text-stone-300">Akun Demo</div>
                    <div class="mt-2">
                        Super Admin:
                        <span class="mono text-stone-200">admin@example.com</span>
                        <span class="mx-2 text-stone-600">/</span>
                        <span class="mono text-stone-200">password</span>
                    </div>
                    <div class="mt-1">
                        Kasir:
                        <span class="mono text-stone-200">cashier@example.com</span>
                        <span class="mx-2 text-stone-600">/</span>
                        <span class="mono text-stone-200">password</span>
                    </div>
                </div>
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
