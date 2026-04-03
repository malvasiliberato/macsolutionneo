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
    profile: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <p class="text-muted mb-1">Identity Foundation</p>
            <h4 class="mb-sm-0">Profilo utente</h4>
        </template>

        <div class="row">
            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Dati essenziali</h4>
                    </div>
                    <div class="card-body">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sicurezza accesso</h4>
                    </div>
                    <div class="card-body">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Rimozione account</h4>
                    </div>
                    <div class="card-body">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Stato bootstrap</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 border rounded-3 p-3 mb-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-success text-success rounded-circle">
                                    <i class="ri-shield-user-line"></i>
                                </span>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Ruoli bootstrap</p>
                                <h6 class="mb-0">{{ profile.roleCodes.join(', ') || 'Nessun ruolo assegnato' }}</h6>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 border rounded-3 p-3 mb-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-info text-info rounded-circle">
                                    <i class="ri-login-circle-line"></i>
                                </span>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Ultimo login</p>
                                <h6 class="mb-0">{{ profile.lastLoginAt || 'Non ancora registrato' }}</h6>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            Profilo e accesso sono pronti. Organization, ruoli finali e permessi di dominio verranno
                            definiti nello step successivo.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
