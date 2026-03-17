<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="page-title">Profile</h1>
                <p class="page-subtitle">Kelola informasi akun dan keamanan</p>
            </div>
        </template>

        <section class="page-wrap">
            <div class="mx-auto max-w-5xl space-y-6">
                <div class="card p-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="card p-6">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="card p-6">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
