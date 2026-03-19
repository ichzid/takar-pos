<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    categories: { type: Array, required: true },
    product:    { type: Object, default: null },
});

const isEdit = computed(() => !!props.product);

// form fields
const sku         = ref(props.product?.sku || '');
const name        = ref(props.product?.name || '');
const categoryId  = ref(props.product?.category_id || '');
const sellPrice   = ref(props.product?.sell_price || 0);
const buyPrice    = ref(props.product?.buy_price || '');
const stock       = ref(props.product?.stock || 0);
const removeImage = ref(false);

// Image handling
const imageFile    = ref(null);
const imagePreview = ref(null);
const fileInput    = ref(null);

const currentImage = computed(() => {
    if (removeImage.value) return null;
    if (imagePreview.value) return imagePreview.value;
    if (props.product?.image) return `/storage/${props.product.image}`;
    return null;
});

function onFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Validate client-side
    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar');
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran gambar maks 2MB');
        return;
    }

    imageFile.value    = file;
    removeImage.value  = false;
    imagePreview.value = URL.createObjectURL(file);
}

function triggerFileInput() {
    fileInput.value?.click();
}

function clearImage() {
    imageFile.value    = null;
    imagePreview.value = null;
    removeImage.value  = true;
    if (fileInput.value) fileInput.value.value = '';
}

// Errors from server
const errors = ref({});
const processing = ref(false);

function submit() {
    processing.value = true;

    const formData = new FormData();
    formData.append('sku',       sku.value);
    formData.append('name',      name.value);
    formData.append('category_id', categoryId.value || '');
    formData.append('sell_price', sellPrice.value);
    formData.append('buy_price',  buyPrice.value || '');
    formData.append('stock',     stock.value);

    if (imageFile.value) {
        formData.append('image', imageFile.value);
    }

    if (isEdit.value) {
        formData.append('remove_image', removeImage.value ? '1' : '0');
        formData.append('_method', 'PUT');
        router.post(route('products.update', props.product.id), formData, {
            onError: (err) => { errors.value = err; },
            onFinish: () => { processing.value = false; },
        });
    } else {
        router.post(route('products.store'), formData, {
            onError: (err) => { errors.value = err; },
            onFinish: () => { processing.value = false; },
        });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Produk' : 'Tambah Produk'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">
                        {{ isEdit ? 'Edit Produk' : 'Tambah Produk' }}
                    </h1>
                    <p class="page-subtitle">Lengkapi detail produk di bawah ini</p>
                </div>
                <Link :href="route('products.index')" class="btn-secondary">← Kembali</Link>
            </div>
        </template>

        <section class="page-wrap">
            <form class="card max-w-3xl space-y-6 p-6" @submit.prevent="submit">

                <!-- Image upload -->
                <div class="space-y-2">
                    <label class="label">Foto Produk</label>
                    <div class="flex items-start gap-5">
                        <!-- Preview / Placeholder -->
                        <div
                            class="relative flex h-32 w-32 flex-shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-[#3d3734] bg-surface-800 transition hover:border-brand-500"
                            @click="triggerFileInput"
                        >
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                class="h-full w-full object-cover"
                                alt="Preview"
                            />
                            <div v-else class="flex flex-col items-center gap-1 text-stone-500">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-[10px]">Upload</span>
                            </div>
                        </div>

                        <div class="flex-1 space-y-2">
                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="onFileChange"
                            />
                            <button type="button" class="btn-secondary text-xs" @click="triggerFileInput">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Pilih Gambar
                            </button>
                            <button
                                v-if="currentImage"
                                type="button"
                                class="btn-danger text-xs"
                                @click="clearImage"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Gambar
                            </button>
                            <p class="text-[11px] text-stone-500">JPG, PNG, WebP. Maks 2MB.</p>
                        </div>
                    </div>
                    <InputError :message="errors.image" />
                </div>

                <!-- SKU & Name -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="sku" class="label">SKU</label>
                        <input id="sku" v-model="sku" type="text" class="input-base" placeholder="Mis. COF-001" />
                        <InputError :message="errors.sku" />
                    </div>
                    <div class="space-y-2">
                        <label for="name" class="label">Nama Produk</label>
                        <input id="name" v-model="name" type="text" class="input-base" placeholder="Mis. Americano" />
                        <InputError :message="errors.name" />
                    </div>
                </div>

                <!-- Category -->
                <div class="space-y-2">
                    <label for="category" class="label">Kategori</label>
                    <select id="category" v-model="categoryId" class="input-base">
                        <option value="">Pilih Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                    <InputError :message="errors.category_id" />
                </div>

                <!-- Prices -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="sell_price" class="label">Harga Jual</label>
                        <input id="sell_price" v-model.number="sellPrice" type="number" min="0" step="1" class="input-base mono" />
                        <InputError :message="errors.sell_price" />
                    </div>
                    <div class="space-y-2">
                        <label for="buy_price" class="label">Harga Beli (Modal)</label>
                        <input id="buy_price" v-model.number="buyPrice" type="number" min="0" step="1" class="input-base mono" placeholder="Opsional" />
                        <InputError :message="errors.buy_price" />
                    </div>
                </div>

                <!-- Stock -->
                <div class="space-y-2 max-w-[200px]">
                    <label for="stock" class="label">Stok</label>
                    <input id="stock" v-model.number="stock" type="number" min="0" class="input-base mono" />
                    <InputError :message="errors.stock" />
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-3 border-t border-surface-800 pt-5">
                    <Link :href="route('products.index')" class="btn-secondary">Batal</Link>
                    <button type="submit" class="btn-primary" :disabled="processing">
                        <svg v-if="processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Produk' }}
                    </button>
                </div>

            </form>
        </section>
    </AuthenticatedLayout>
</template>
