<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    roleOptions: {
        type: Array,
        default: () => [],
    },
});

const isEdit = computed(() => !!props.user);

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    role: props.user?.role || 'cashier',
    password: '',
    password_confirmation: '',
});

function submit() {
    if (isEdit.value) {
        form.put(route('users.update', props.user.id));
        return;
    }

    form.post(route('users.store'));
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">
                        {{ isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                    </h1>
                    <p class="page-subtitle">Atur data akun admin dan kasir di bawah ini</p>
                </div>
                <Link :href="route('users.index')" class="btn-secondary">
                    Kembali
                </Link>
            </div>
        </template>

        <section class="page-wrap">
            <form class="card max-w-3xl space-y-5 p-6" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel for="name" value="Nama" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full"
                            placeholder="Contoh: Kasir Shift Malam"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full"
                            placeholder="contoh@email.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>
                </div>

                <div class="space-y-2 max-w-sm">
                    <InputLabel for="role" value="Role" />
                    <select id="role" v-model="form.role" class="input-base">
                        <option v-for="role in roleOptions" :key="role.value" :value="role.value">
                            {{ role.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.role" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel
                            for="password"
                            :value="isEdit ? 'Password Baru (Opsional)' : 'Password'"
                        />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="w-full"
                            :placeholder="isEdit ? 'Kosongkan jika tidak diubah' : 'Masukkan password'"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="space-y-2">
                        <InputLabel
                            for="password_confirmation"
                            :value="isEdit ? 'Konfirmasi Password Baru' : 'Konfirmasi Password'"
                        />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full"
                            :placeholder="isEdit ? 'Ulangi password baru' : 'Ulangi password'"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <div v-if="isEdit" class="rounded-xl border border-surface-800 bg-surface-900/60 px-4 py-3 text-xs text-stone-400">
                    Password boleh dikosongkan jika Anda hanya ingin memperbarui nama, email, atau role pengguna.
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :disabled="form.processing">
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                    </PrimaryButton>
                </div>
            </form>
        </section>
    </AuthenticatedLayout>
</template>
