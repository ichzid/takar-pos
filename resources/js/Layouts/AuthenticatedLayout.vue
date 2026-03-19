<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const showingNavigationDropdown = ref(false);
const page = usePage();

// ─── Theme Management ────────────────────────────────────────────────────────
// Theme is driven by store_settings (admin can change in /settings).
// Falls back to localStorage for instant load, then syncs from server.
const theme = ref('dark');

const applyTheme = (value) => {
    document.body.classList.remove('theme-dark', 'theme-light');
    document.body.classList.add(`theme-${value}`);
};

onMounted(() => {
    // Use localStorage first for instant paint, then trust server value
    const serverTheme = page.props.store_settings?.theme;
    const localTheme  = localStorage.getItem('kashier-theme');
    const resolved    = serverTheme ?? localTheme ?? 'dark';
    theme.value       = resolved;
    applyTheme(resolved);
});

// If store_settings.theme changes (e.g. after Settings page save), re-apply
watch(() => page.props.store_settings?.theme, (val) => {
    if (val && val !== theme.value) {
        theme.value = val;
        localStorage.setItem('kashier-theme', val);
        applyTheme(val);
    }
});

// ─── Navigation ──────────────────────────────────────────────────────────────
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

const navSections = computed(() => {
    const sections = [
        {
            key: 'utama',
            label: 'Utama',
            items: [
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
                    key: 'orders',
                    label: 'Transaksi',
                    href: route('orders.index'),
                    active: 'orders.*',
                    adminOnly: false,
                    icon: 'clipboard',
                },
            ],
        },
        {
            key: 'master-data',
            label: 'Master Data',
            items: [
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
                    key: 'users',
                    label: 'Pengguna',
                    href: route('users.index'),
                    active: 'users.*',
                    adminOnly: true,
                    icon: 'users',
                },
            ],
        },
        {
            key: 'sistem',
            label: 'Sistem',
            items: [
                {
                    key: 'settings',
                    label: 'Pengaturan',
                    href: route('settings.index'),
                    active: 'settings.*',
                    adminOnly: true,
                    icon: 'cog',
                },
            ],
        },
    ];

    const role = page.props.auth.user?.role;

    return sections
        .map((section) => ({
            ...section,
            items: section.items.filter((item) => !item.adminOnly || role === 'admin'),
        }))
        .filter((section) => section.items.length > 0);
});

const iconPaths = {
    grid: 'M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z',
    receipt:
        'M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1-4H9m0 0a2 2 0 000 4h6a2 2 0 000-4m-6 0V3',
    box: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    tag: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    clipboard:
        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    users: 'M17 20h5V18a4 4 0 00-5-3.87M9 20H4V18a4 4 0 015-3.87m8-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0zM6 10a3 3 0 11-6 0 3 3 0 016 0z',
    cog: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
};
</script>

<template>
    <div class="app-shell flex h-screen overflow-hidden">
        <!-- ─── Desktop Sidebar ─── -->
        <aside
            class="no-print hidden w-[220px] flex-shrink-0 flex-col border-r border-surface-800 bg-surface-950 p-4 md:flex"
        >
            <div class="mb-3 flex items-center gap-3 px-2 py-4">
                <ApplicationLogo />
            </div>

            <div class="space-y-5">
                <div
                    v-for="section in navSections"
                    :key="section.key"
                    class="space-y-1"
                >
                    <div class="nav-section-label px-2">
                        {{ section.label }}
                    </div>

                    <NavLink
                        v-for="item in section.items"
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
                </div>

                <div class="space-y-1">
                    <div class="nav-section-label px-2">
                        Akun
                    </div>

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
            </div>

            <!-- User profile -->
            <div class="mt-auto border-t border-surface-800 pt-4">
                <div class="flex items-center gap-3 px-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-xs font-bold text-white"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-xs font-semibold text-surface-50">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-xs capitalize text-stone-500">
                            {{ $page.props.auth.user.role }}
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ─── Main Content ─── -->
        <div class="main-panel flex flex-col">
            <!-- Mobile top bar -->
            <div
                class="no-print flex items-center justify-between border-b border-surface-800 bg-surface-950 px-4 py-3 md:hidden"
            >
                <Link :href="route('dashboard')" class="inline-flex">
                    <ApplicationLogo />
                </Link>
                <button
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                    class="btn-secondary p-2"
                    type="button"
                >
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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

            <!-- Mobile dropdown menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="no-print border-b border-surface-800 bg-surface-950 px-4 py-3 md:hidden"
            >
                <div class="space-y-4">
                    <div
                        v-for="section in navSections"
                        :key="`m-${section.key}`"
                        class="space-y-2"
                    >
                        <div class="nav-section-label px-1">
                            {{ section.label }}
                        </div>

                        <ResponsiveNavLink
                            v-for="item in section.items"
                            :key="`m-${item.key}`"
                            :href="item.href"
                            :active="route().current(item.active)"
                        >
                            {{ item.label }}
                        </ResponsiveNavLink>
                    </div>

                    <div class="space-y-2">
                        <div class="nav-section-label px-1">
                            Akun
                        </div>

                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>

            <header v-if="$slots.header" class="no-print border-b border-surface-800 bg-surface-950 px-6 py-5">
                <slot name="header" />
            </header>

            <main class="fade-up flex-1 min-h-0 flex flex-col">
                <slot />
            </main>
        </div>
    </div>
</template>
