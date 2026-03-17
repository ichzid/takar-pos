<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    name: props.category?.name || '',
});

function submit() {
    if (props.category) {
        form.put(route('categories.update', props.category.id));
        return;
    }

    form.post(route('categories.store'));
}
</script>

<template>
    <Head :title="props.category ? 'Edit Category' : 'Add Category'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">
                        {{ props.category ? 'Edit Category' : 'Add Category' }}
                    </h1>
                    <p class="page-subtitle">Buat kategori agar produk lebih terstruktur</p>
                </div>
                <Link :href="route('categories.index')" class="btn-secondary">
                    Kembali
                </Link>
            </div>
        </template>

        <section class="page-wrap">
            <form class="card max-w-3xl space-y-5 p-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <InputLabel for="name" value="Nama Kategori" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full"
                        placeholder="Contoh: Makanan"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :disabled="form.processing">
                        Simpan
                    </PrimaryButton>
                </div>
            </form>
        </section>
    </AuthenticatedLayout>
</template>
