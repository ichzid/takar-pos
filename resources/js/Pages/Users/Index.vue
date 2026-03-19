<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');

function submitSearch() {
    router.get(
        route('users.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
}

function destroyUser(user) {
    if (!confirm(`Hapus pengguna "${user.name}"?`)) return;
    router.delete(route('users.destroy', user.id));
}

function roleLabel(role) {
    return role === 'admin' ? 'Admin' : 'Kasir';
}

function formatDate(value) {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">Pengguna</h1>
                    <p class="page-subtitle">Kelola akun admin dan kasir dalam sistem</p>
                </div>
                <Link :href="route('users.create')" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </Link>
            </div>
        </template>

        <section class="page-wrap space-y-5">
            <div
                v-if="$page.props.flash?.success"
                class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                v-if="$page.props.flash?.error"
                class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300"
            >
                {{ $page.props.flash.error }}
            </div>

            <div class="flex gap-3">
                <div class="relative w-full max-w-sm">
                    <svg
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-500"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35" />
                    </svg>
                    <input
                        v-model="search"
                        class="input-base pl-10"
                        placeholder="Cari nama, email, atau role..."
                        @keyup.enter="submitSearch"
                    />
                </div>
                <button class="btn-secondary" @click="submitSearch">Cari</button>
            </div>

            <div class="table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Verifikasi</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="font-semibold">{{ user.name }}</td>
                            <td class="text-stone-400">{{ user.email }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="user.role === 'admin'
                                        ? 'bg-brand-500/15 text-brand-400'
                                        : 'bg-blue-500/10 text-blue-400'"
                                >
                                    {{ roleLabel(user.role) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="user.email_verified_at
                                        ? 'bg-emerald-500/10 text-emerald-400'
                                        : 'bg-stone-500/10 text-stone-400'"
                                >
                                    {{ user.email_verified_at ? 'Terverifikasi' : 'Belum' }}
                                </span>
                            </td>
                            <td class="text-stone-400">{{ formatDate(user.created_at) }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('users.edit', user.id)"
                                        class="btn-secondary px-3 py-1.5 text-xs"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        class="btn-danger"
                                        @click="destroyUser(user)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="py-6 text-center text-sm text-stone-500">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="users.links" />
        </section>
    </AuthenticatedLayout>
</template>
