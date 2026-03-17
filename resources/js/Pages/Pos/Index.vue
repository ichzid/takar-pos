<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    taxRate: {
        type: Number,
        default: 0.1,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const selectedCategory = ref('all');
const activePanel = ref('products');
const cart = ref([]);
const paidAmount = ref(0);

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const displayedProducts = computed(() => {
    const q = search.value.trim().toLowerCase();

    return props.products.filter((product) => {
        const name = String(product.name ?? '').toLowerCase();
        const sku = String(product.sku ?? '').toLowerCase();
        const byCategory =
            selectedCategory.value === 'all' ||
            product.category_id === selectedCategory.value;
        const bySearch = q.length === 0 || name.includes(q) || sku.includes(q);

        return byCategory && bySearch;
    });
});

const subtotal = computed(() =>
    cart.value.reduce((sum, item) => sum + item.unit_price * item.quantity, 0),
);
const taxAmount = computed(
    () => Math.round(subtotal.value * props.taxRate * 100) / 100,
);
const total = computed(() => subtotal.value + taxAmount.value);
const changeAmount = computed(() => Math.max(0, paidAmount.value - total.value));
const cartCount = computed(() =>
    cart.value.reduce((sum, item) => sum + item.quantity, 0),
);
const canCheckout = computed(
    () => cart.value.length > 0 && paidAmount.value >= total.value,
);

const isInCart = (productId) =>
    cart.value.find((item) => item.product_id === productId);

function setCategory(categoryId) {
    selectedCategory.value = categoryId;
}

function showProducts() {
    activePanel.value = 'products';
}

function showCart() {
    activePanel.value = 'cart';
}

function addToCart(product) {
    if (product.stock <= 0) return;

    const existing = cart.value.find((item) => item.product_id === product.id);

    if (existing) {
        if (existing.quantity >= product.stock) return;
        existing.quantity += 1;
        return;
    }

    cart.value.push({
        product_id: product.id,
        name: product.name,
        unit_price: Number(product.sell_price),
        quantity: 1,
        stock: product.stock,
    });
}

function changeQty(item, delta) {
    const nextQty = item.quantity + delta;
    if (nextQty <= 0) {
        removeItem(item.product_id);
        return;
    }

    item.quantity = Math.min(nextQty, item.stock);
}

function removeItem(productId) {
    cart.value = cart.value.filter((row) => row.product_id !== productId);
}

function clearCart() {
    cart.value = [];
    paidAmount.value = 0;
}

function setExactPay() {
    paidAmount.value = total.value;
}

function addPay(amount) {
    paidAmount.value = Math.max(0, (paidAmount.value || 0)) + amount;
}

function checkout() {
    if (!canCheckout.value) return;

    router.post(
        route('pos.checkout'),
        {
            items: cart.value.map((item) => ({
                product_id: item.product_id,
                quantity: item.quantity,
            })),
            tax_rate: props.taxRate,
            paid_amount: paidAmount.value,
        },
        {
            onSuccess: () => {
                clearCart();
            },
        },
    );
}
</script>

<template>
    <Head title="POS" />

    <AuthenticatedLayout>
        <section class="pos-shell flex flex-col overflow-hidden lg:flex-row">
            <!-- ─── Mobile Tab Bar ─── -->
            <div class="border-b border-surface-800 bg-surface-950 px-4 py-3 lg:hidden">
                <div class="grid grid-cols-2 gap-2">
                    <button
                        class="relative flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all duration-200"
                        :class="activePanel === 'products'
                            ? 'bg-brand-500 text-white shadow-[0_4px_16px_rgba(249,115,22,0.3)]'
                            : 'bg-surface-800 text-stone-400 hover:bg-surface-700 hover:text-stone-200'"
                        @click="showProducts"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Produk
                    </button>
                    <button
                        class="relative flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all duration-200"
                        :class="activePanel === 'cart'
                            ? 'bg-brand-500 text-white shadow-[0_4px_16px_rgba(249,115,22,0.3)]'
                            : 'bg-surface-800 text-stone-400 hover:bg-surface-700 hover:text-stone-200'"
                        @click="showCart"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Keranjang
                        <span
                            v-if="cartCount > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                        >{{ cartCount }}</span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 LEFT — Product Panel
            ══════════════════════════════════════ -->
            <div
                :class="activePanel === 'products' ? 'flex' : 'hidden lg:flex'"
                class="min-h-0 flex-1 flex-col overflow-hidden"
            >
                <!-- Header -->
                <div class="border-b border-surface-800 bg-surface-950 px-6 py-4">
                    <div class="mb-3 flex items-center gap-3">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <svg
                                class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            >
                                <circle cx="11" cy="11" r="8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35" />
                            </svg>
                            <input
                                v-model="search"
                                class="input-base pl-10"
                                placeholder="Cari nama produk atau SKU..."
                            />
                            <button
                                v-if="search"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-500 hover:text-stone-300 transition-colors"
                                @click="search = ''"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Category chips — scrollable row -->
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                        <button
                            class="chip flex-shrink-0"
                            :class="{ 'chip-active': selectedCategory === 'all' }"
                            @click="setCategory('all')"
                        >
                            <svg class="mr-1 inline-block h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Semua
                        </button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            class="chip flex-shrink-0"
                            :class="{ 'chip-active': selectedCategory === category.id }"
                            @click="setCategory(category.id)"
                        >
                            {{ category.name }}
                        </button>
                    </div>

                    <!-- Product count info -->
                    <div class="mt-2 text-[11px] text-stone-600">
                        Menampilkan {{ displayedProducts.length }} produk
                        <span v-if="search"> untuk "<span class="text-brand-400">{{ search }}</span>"</span>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 content-start">
                        <button
                            v-for="product in displayedProducts"
                            :key="product.id"
                            class="product-card text-left transition-all duration-200"
                            :class="{
                                'product-card-in-cart': isInCart(product.id),
                                'product-card-out-of-stock': product.stock <= 0,
                            }"
                            @click="addToCart(product)"
                        >
                            <!-- Thumbnail area -->
                            <div class="relative flex h-[100px] w-full items-center justify-center bg-surface-800">
                                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="#57534e" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>

                                <!-- "In cart" checkmark badge -->
                                <div
                                    v-if="isInCart(product.id)"
                                    class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 shadow-lg"
                                >
                                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <!-- "Habis" overlay on zero-stock product -->
                                <div
                                    v-if="product.stock <= 0"
                                    class="absolute inset-0 flex items-center justify-center bg-surface-950/70"
                                >
                                    <span class="rounded-full bg-red-500/20 px-3 py-1 text-xs font-bold text-red-400 ring-1 ring-red-500/40">
                                        Habis
                                    </span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-3">
                                <div class="mb-0.5 truncate text-sm font-semibold leading-tight text-surface-50">
                                    {{ product.name }}
                                </div>
                                <div class="mb-1 flex items-center gap-1.5">
                                    <span class="truncate text-xs text-stone-500">{{ product.category?.name || '—' }}</span>
                                    <span class="text-stone-700">·</span>
                                    <span class="text-[10px] font-mono text-stone-600">{{ product.sku }}</span>
                                </div>
                                <div class="mono text-sm font-bold text-brand-400">
                                    {{ currency.format(Number(product.sell_price) || 0) }}
                                </div>
                                <div class="mt-1.5 flex items-center justify-between">
                                    <div
                                        class="flex items-center gap-1 text-xs"
                                        :class="product.stock <= 0 ? 'text-red-500' : product.stock <= 5 ? 'text-amber-500' : 'text-stone-600'"
                                    >
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                        </svg>
                                        Stok: {{ product.stock }}
                                    </div>
                                    <div
                                        v-if="isInCart(product.id)"
                                        class="badge bg-brand-500/15 text-brand-400"
                                    >
                                        ×{{ isInCart(product.id).quantity }}
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-if="displayedProducts.length === 0"
                        class="flex flex-col items-center justify-center py-20 text-center"
                    >
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-800">
                            <svg class="h-8 w-8 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-stone-400">Produk tidak ditemukan</div>
                        <div class="mt-1 text-xs text-stone-600">Coba kata kunci atau kategori lain</div>
                        <button
                            class="mt-4 text-xs text-brand-400 underline hover:text-brand-300"
                            @click="search = ''; setCategory('all')"
                        >Reset filter</button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 RIGHT — Cart / Checkout Sidebar
            ══════════════════════════════════════ -->
            <aside
                :class="activePanel === 'cart' ? 'flex' : 'hidden lg:flex'"
                class="min-h-0 w-full flex-shrink-0 flex-col border-t border-surface-800 bg-surface-950 lg:w-[360px] lg:border-l lg:border-t-0"
            >
                <!-- Cart header -->
                <div class="border-b border-surface-800 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="text-base font-extrabold">Keranjang</h2>
                            <span
                                v-if="cartCount > 0"
                                class="badge bg-brand-500/15 text-brand-400"
                            >{{ cartCount }} item</span>
                        </div>
                        <button
                            class="flex items-center gap-1 rounded-lg px-2 py-1 text-xs text-stone-500 transition-colors hover:bg-red-500/10 hover:text-red-400"
                            @click="clearCart"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Semua
                        </button>
                    </div>
                </div>

                <!-- Cart items -->
                <div class="min-h-0 flex-1 overflow-y-auto p-4">
                    <!-- Empty cart -->
                    <div
                        v-if="cart.length === 0"
                        class="flex h-full flex-col items-center justify-center py-16 text-center"
                    >
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-800 opacity-60">
                            <svg class="h-8 w-8 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-stone-500">Keranjang masih kosong</div>
                        <div class="mt-1 text-xs text-stone-600">Tap produk untuk menambahkan</div>
                    </div>

                    <!-- Cart item list -->
                    <div v-else class="space-y-2">
                        <div
                            v-for="item in cart"
                            :key="item.product_id"
                            class="cart-item-row flex items-center gap-3 rounded-xl border border-surface-800 bg-surface-900 p-3 transition-all duration-200 hover:border-surface-700"
                        >
                            <!-- icon -->
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-surface-800">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#57534e" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <!-- name + price -->
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-semibold text-surface-50">{{ item.name }}</div>
                                <div class="mono text-xs text-brand-400">
                                    {{ currency.format(item.unit_price) }}
                                </div>
                            </div>

                            <!-- qty controls -->
                            <div class="flex items-center gap-1">
                                <button
                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-surface-800 text-sm text-stone-300 transition-colors hover:bg-red-500/20 hover:text-red-400"
                                    @click="changeQty(item, -1)"
                                >−</button>
                                <span class="mono w-7 text-center text-sm font-bold text-surface-50">
                                    {{ item.quantity }}
                                </span>
                                <button
                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-surface-800 text-sm text-stone-300 transition-colors hover:bg-brand-500/20 hover:text-brand-400"
                                    :disabled="item.quantity >= item.stock"
                                    @click="changeQty(item, 1)"
                                >+</button>
                            </div>

                            <!-- subtotal + remove -->
                            <div class="text-right">
                                <div class="mono text-xs font-semibold text-surface-50">
                                    {{ currency.format(item.unit_price * item.quantity) }}
                                </div>
                                <button
                                    class="mt-0.5 text-stone-700 transition-colors hover:text-red-400"
                                    @click="removeItem(item.product_id)"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary + Payment -->
                <div class="border-t border-surface-800 p-5 space-y-3">
                    <!-- Subtotal / tax / total rows -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-sm text-stone-400">
                            <span>Subtotal</span>
                            <span class="mono font-semibold text-stone-200">{{ currency.format(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-stone-400">
                            <span>Pajak ({{ Math.round(props.taxRate * 100) }}%)</span>
                            <span class="mono font-semibold text-stone-200">{{ currency.format(taxAmount) }}</span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-surface-900 px-3 py-2.5 text-base font-extrabold">
                            <span>Total</span>
                            <span class="mono text-brand-400">{{ currency.format(total) }}</span>
                        </div>
                    </div>

                    <!-- Payment input -->
                    <div>
                        <div class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-stone-500">Jumlah Bayar</div>
                        <input
                            v-model.number="paidAmount"
                            class="input-base mono"
                            placeholder="Rp 0"
                            type="number"
                            min="0"
                        />

                        <!-- Quick-pay shortcuts -->
                        <div class="mt-2 flex gap-1.5">
                            <button
                                class="quick-pay-btn flex-1"
                                @click="setExactPay"
                            >Uang Pas</button>
                            <button
                                class="quick-pay-btn flex-1"
                                @click="addPay(50000)"
                            >+50rb</button>
                            <button
                                class="quick-pay-btn flex-1"
                                @click="addPay(100000)"
                            >+100rb</button>
                        </div>
                    </div>

                    <!-- Change -->
                    <div class="flex items-center justify-between rounded-xl bg-surface-900 px-3 py-2.5">
                        <span class="text-sm text-stone-400">Kembalian</span>
                        <span
                            class="mono text-sm font-bold"
                            :class="paidAmount > 0 && paidAmount >= total ? 'text-emerald-400' : 'text-stone-600'"
                        >
                            {{ currency.format(changeAmount) }}
                        </span>
                    </div>

                    <!-- Checkout button -->
                    <button
                        class="btn-primary w-full py-3 text-base"
                        :disabled="!canCheckout"
                        @click="checkout"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ canCheckout ? 'Bayar Sekarang' : cart.length === 0 ? 'Keranjang Kosong' : 'Bayar Kurang' }}
                    </button>
                </div>
            </aside>
        </section>
    </AuthenticatedLayout>
</template>
