<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    product: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    sku: props.product?.sku || '',
    name: props.product?.name || '',
    category_id: props.product?.category_id || '',
    sell_price: props.product?.sell_price || 0,
    buy_price: props.product?.buy_price || '',
    stock: props.product?.stock || 0,
});

function submit() {
    if (props.product) {
        form.put(route('products.update', props.product.id));
        return;
    }

    form.post(route('products.store'));
}
</script>

<template>
    <Head :title="props.product ? 'Edit Product' : 'Add Product'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">
                        {{ props.product ? 'Edit Product' : 'Add Product' }}
                    </h1>
                    <p class="page-subtitle">Lengkapi detail produk di bawah ini</p>
                </div>
                <Link :href="route('products.index')" class="btn-secondary">
                    Kembali
                </Link>
            </div>
        </template>

        <section class="page-wrap">
            <form class="card max-w-3xl space-y-5 p-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <InputLabel for="sku" value="SKU" />
                    <TextInput id="sku" v-model="form.sku" type="text" class="w-full" />
                    <InputError :message="form.errors.sku" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="name" value="Nama Produk" />
                    <TextInput id="name" v-model="form.name" type="text" class="w-full" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="category" value="Kategori" />
                    <select id="category" v-model="form.category_id" class="input-base">
                        <option value="">Pilih Kategori</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel for="sell_price" value="Harga Jual" />
                        <TextInput
                            id="sell_price"
                            v-model="form.sell_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full"
                        />
                        <InputError :message="form.errors.sell_price" />
                    </div>
                    <div class="space-y-2">
                        <InputLabel for="buy_price" value="Harga Beli" />
                        <TextInput
                            id="buy_price"
                            v-model="form.buy_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full"
                        />
                        <InputError :message="form.errors.buy_price" />
                    </div>
                </div>

                <div class="space-y-2">
                    <InputLabel for="stock" value="Stok" />
                    <TextInput
                        id="stock"
                        v-model="form.stock"
                        type="number"
                        min="0"
                        class="w-full"
                    />
                    <InputError :message="form.errors.stock" />
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
