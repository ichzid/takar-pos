<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
const theme = ref('dark');

const applyTheme = (value) => {
    document.body.classList.remove('theme-dark', 'theme-light');
    document.body.classList.add(`theme-${value}`);
};

const toggleTheme = () => {
    theme.value = theme.value === 'dark' ? 'light' : 'dark';
};

onMounted(() => {
    const saved = localStorage.getItem('kashier-theme');

    if (saved === 'light' || saved === 'dark') {
        theme.value = saved;
    }

    applyTheme(theme.value);
});

watch(theme, (value) => {
    localStorage.setItem('kashier-theme', value);
    applyTheme(value);
});

const userInitials = computed(() => {
    const name = page.props.auth.user?.name ?? '';
    if (!name.trim()) return 'U';

    return name
        .trim()
        .split(' ')
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();
});

const navItems = computed(() => {
    const items = [
        {
            key: 'dashboard',
            label: 'Dashboard',
            href: route('dashboard'),
            active: 'dashboard',
            adminOnly: false,
            icon: 'grid',
        },
        {
            key: 'pos',
            label: 'POS',
            href: route('pos.index'),
            active: 'pos.*',
            adminOnly: false,
            icon: 'receipt',
        },
        {
            key: 'products',
            label: 'Produk',
            href: route('products.index'),
            active: 'products.*',
            adminOnly: true,
            icon: 'box',
        },
        {
            key: 'categories',
            label: 'Kategori',
            href: route('categories.index'),
            active: 'categories.*',
            adminOnly: true,
            icon: 'tag',
        },
        {
            key: 'orders',
            label: 'Orders',
            href: route('orders.index'),
            active: 'orders.*',
            adminOnly: true,
            icon: 'clipboard',
        },
    ];

    const role = page.props.auth.user?.role;

    return items.filter((item) => !item.adminOnly || role === 'admin');
});

const iconPaths = {
    grid: 'M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z',
    receipt:
        'M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1-4H9m0 0a2 2 0 000 4h6a2 2 0 000-4m-6 0V3',
    box: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    tag: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    clipboard:
        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
};
</script>

<template>
    <div class="app-shell flex h-screen overflow-hidden">
        <aside
            class="no-print hidden w-[220px] flex-shrink-0 flex-col border-r border-surface-800 bg-surface-950 p-4 md:flex"
        >
            <div class="mb-3 flex items-center gap-3 px-2 py-4">
                <ApplicationLogo />
                <div class="pulse-dot ms-auto"></div>
            </div>

            <div class="space-y-1">
                <NavLink
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    :active="route().current(item.active)"
                >
                    <svg
                        class="h-[18px] w-[18px] flex-shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            :d="iconPaths[item.icon]"
                        />
                    </svg>
                    <span>{{ item.label }}</span>
                </NavLink>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="nav-item w-full"
                >
                    <svg
                        class="h-[18px] w-[18px] flex-shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"
                        />
                    </svg>
                    <span>Logout</span>
                </Link>
            </div>

            <div class="mt-auto border-t border-surface-800 pt-4">
                <div class="px-2">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] border border-[#3d3734] bg-surface-900 text-stone-200 transition hover:bg-surface-800 hover:text-white"
                        :title="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                        :aria-label="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                        @click="toggleTheme"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                v-if="theme === 'dark'"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1M18.36 18.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M12 7a5 5 0 100 10 5 5 0 000-10z"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"
                            />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 flex items-center gap-3 px-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-xs font-bold text-white"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-xs font-semibold text-surface-50">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-xs text-stone-500">
                            {{ $page.props.auth.user.role }}
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="main-panel">
            <div
                class="no-print flex items-center justify-between border-b border-surface-800 bg-surface-950 px-4 py-3 md:hidden"
            >
                <Link :href="route('dashboard')" class="inline-flex">
                    <ApplicationLogo />
                </Link>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="btn-secondary p-2"
                        :title="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                        :aria-label="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                        @click="toggleTheme"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                v-if="theme === 'dark'"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1M18.36 18.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M12 7a5 5 0 100 10 5 5 0 000-10z"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"
                            />
                        </svg>
                    </button>
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="btn-secondary p-2"
                        type="button"
                    >
                        <svg
                            class="h-5 w-5"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="no-print border-b border-surface-800 bg-surface-950 px-4 py-3 md:hidden"
            >
                <div class="space-y-2">
                    <ResponsiveNavLink
                        v-for="item in navItems"
                        :key="`m-${item.key}`"
                        :href="item.href"
                        :active="route().current(item.active)"
                    >
                        {{ item.label }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>

            <header v-if="$slots.header" class="no-print border-b border-surface-800 bg-surface-950 px-6 py-5">
                <slot name="header" />
            </header>

            <main class="fade-up">
                <slot />
            </main>
        </div>
    </div>
</template>
