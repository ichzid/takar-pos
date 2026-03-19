<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

// Local editable state — initialize from props
const form = ref({
    store_name:      props.settings.store_name      ?? '',
    store_address:   props.settings.store_address   ?? '',
    store_phone:     props.settings.store_phone     ?? '',
    receipt_message: props.settings.receipt_message ?? '',
    tax_enabled:     props.settings.tax_enabled === '1' || props.settings.tax_enabled === true,
    tax_rate:        Number(props.settings.tax_rate  ?? 10),
    theme:           props.settings.theme           ?? 'dark',
});

const saving   = ref(false);
const saved    = ref(false);

function save() {
    saving.value = true;
    router.post(route('settings.update'), {
        ...form.value,
        tax_enabled: form.value.tax_enabled ? 1 : 0,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = true;
            setTimeout(() => (saved.value = false), 2500);

            // Apply theme immediately
            document.body.classList.remove('theme-dark', 'theme-light');
            document.body.classList.add(`theme-${form.value.theme}`);
            localStorage.setItem('kashier-theme', form.value.theme);
        },
        onFinish: () => (saving.value = false),
    });
}
</script>

<template>
    <Head title="Pengaturan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Pengaturan</h1>
                    <p class="page-subtitle">Konfigurasi toko dan tampilan sistem</p>
                </div>
                <button
                    class="btn-primary flex items-center gap-2"
                    :disabled="saving"
                    @click="save"
                >
                    <svg v-if="!saving && !saved" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <svg v-else-if="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    <svg v-else class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ saving ? 'Menyimpan…' : saved ? 'Tersimpan!' : 'Simpan' }}
                </button>
            </div>
        </template>

        <section class="page-wrap space-y-6">

            <!-- Flash message -->
            <div
                v-if="$page.props.flash?.success"
                class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- ─── Info Toko ─── -->
            <div class="card p-6 space-y-5">
                <h2 class="text-sm font-bold uppercase tracking-widest text-stone-400">Info Toko</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-stone-400">Nama Toko</label>
                        <input
                            v-model="form.store_name"
                            class="input-base"
                            placeholder="Mis. Warung Kopi Maju"
                            type="text"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-stone-400">No. HP / WhatsApp</label>
                        <input
                            v-model="form.store_phone"
                            class="input-base"
                            placeholder="08xx-xxxx-xxxx"
                            type="text"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-stone-400">Alamat</label>
                        <input
                            v-model="form.store_address"
                            class="input-base"
                            placeholder="Jl. Contoh No. 1, Kota"
                            type="text"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-stone-400">Pesan di Bawah Struk</label>
                        <input
                            v-model="form.receipt_message"
                            class="input-base"
                            placeholder="Mis. Terima kasih sudah berbelanja!"
                            type="text"
                        />
                        <p class="mt-1 text-xs text-stone-600">Pesan ini ditampilkan di bagian bawah setiap struk.</p>
                    </div>
                </div>
            </div>

            <!-- ─── Pajak ─── -->
            <div class="card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-widest text-stone-400">Pajak</h2>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-surface-50">Aktifkan Pajak</div>
                        <div class="text-xs text-stone-500">Pajak dihitung otomatis saat checkout</div>
                    </div>
                    <!-- Toggle -->
                    <button
                        type="button"
                        class="relative inline-flex h-7 w-14 flex-shrink-0 items-center rounded-full border px-0.5 transition-all duration-300 focus:outline-none"
                        :class="form.tax_enabled
                            ? 'border-brand-400/40 bg-brand-500/15 shadow-[0_0_0_1px_rgba(249,115,22,0.08)]'
                            : 'border-stone-500/40 bg-surface-800'"
                        :aria-pressed="form.tax_enabled"
                        :title="form.tax_enabled ? 'Pajak aktif' : 'Pajak nonaktif'"
                        @click="form.tax_enabled = !form.tax_enabled"
                    >
                        <span
                            class="pointer-events-none absolute left-2 text-[9px] font-bold uppercase tracking-wide transition-opacity duration-300"
                            :class="form.tax_enabled ? 'text-brand-300 opacity-100' : 'opacity-0'"
                        >
                            On
                        </span>
                        <span
                            class="pointer-events-none absolute right-2 text-[9px] font-bold uppercase tracking-wide transition-opacity duration-300"
                            :class="form.tax_enabled ? 'opacity-0' : 'text-stone-300 opacity-100'"
                        >
                            Off
                        </span>
                        <span
                            class="relative inline-flex h-5 w-5 transform items-center justify-center rounded-full shadow transition-all duration-300"
                            :class="form.tax_enabled
                                ? 'translate-x-7 bg-brand-500 text-white'
                                : 'translate-x-0 bg-stone-200 text-stone-700'"
                        >
                            <svg
                                v-if="form.tax_enabled"
                                class="h-3 w-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg
                                v-else
                                class="h-3 w-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div v-if="form.tax_enabled" class="flex items-center gap-3">
                    <div class="max-w-[140px]">
                        <label class="mb-1.5 block text-xs font-semibold text-stone-400">Persentase Pajak (%)</label>
                        <div class="relative">
                            <input
                                v-model.number="form.tax_rate"
                                class="input-base pr-8"
                                type="number"
                                min="0"
                                max="100"
                                step="0.5"
                            />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-stone-500">%</span>
                        </div>
                    </div>
                    <div class="mt-5 text-xs text-stone-500">Mis. masukkan <span class="text-brand-400 font-semibold">10</span> untuk pajak 10%</div>
                </div>
            </div>

            <!-- ─── Tampilan ─── -->
            <div class="card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-widest text-stone-400">Tampilan</h2>
                <p class="text-xs text-stone-500">Pilih tema default aplikasi.</p>

                <div class="flex gap-3">
                    <!-- Dark option -->
                    <button
                        type="button"
                        class="flex flex-1 items-center gap-3 rounded-xl border-2 p-4 transition-all duration-200"
                        :class="form.theme === 'dark'
                            ? 'border-brand-500 bg-brand-500/10'
                            : 'border-surface-700 bg-surface-800 hover:border-surface-600'"
                        @click="form.theme = 'dark'"
                    >
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-[#0d0c0a]">
                            <svg class="h-5 w-5 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-semibold text-surface-50">Dark Mode</div>
                            <div class="text-xs text-stone-500">Tema gelap</div>
                        </div>
                        <div
                            v-if="form.theme === 'dark'"
                            class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-brand-500"
                        >
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Light option -->
                    <button
                        type="button"
                        class="flex flex-1 items-center gap-3 rounded-xl border-2 p-4 transition-all duration-200"
                        :class="form.theme === 'light'
                            ? 'border-brand-500 bg-brand-500/10'
                            : 'border-surface-700 bg-surface-800 hover:border-surface-600'"
                        @click="form.theme = 'light'"
                    >
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100">
                            <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1M18.36 18.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M12 7a5 5 0 100 10 5 5 0 000-10z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-semibold text-surface-50">Light Mode</div>
                            <div class="text-xs text-stone-500">Tema terang</div>
                        </div>
                        <div
                            v-if="form.theme === 'light'"
                            class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-brand-500"
                        >
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

        </section>
    </AuthenticatedLayout>
</template>
