<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="space-y-6">
            <div>
                <p class="label">Verify</p>
                <h1 class="mt-2 text-3xl font-extrabold text-surface-50">
                    Konfirmasi email Anda
                </h1>
                <p class="mt-2 text-sm text-stone-400">
                    Klik tautan yang kami kirim ke email Anda untuk mengaktifkan akun.
                </p>
            </div>

            <div
                v-if="verificationLinkSent"
                class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                Link verifikasi baru sudah dikirim ke email Anda.
            </div>

            <form @submit.prevent="submit">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Kirim ulang email verifikasi
                    </PrimaryButton>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="btn-ghost"
                    >
                        Log Out
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
