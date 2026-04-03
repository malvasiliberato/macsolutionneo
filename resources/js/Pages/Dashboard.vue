<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Array,
        required: true,
    },
    nextSteps: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <p class="text-muted mb-1">Portal Bootstrap</p>
            <h4 class="mb-sm-0">Dashboard iniziale</h4>
        </template>

        <div class="row">
            <div class="col-xxl-9">
                <div class="row">
                    <div v-for="stat in stats" :key="stat.label" class="col-md-4">
                        <div class="card card-animate msn-stat-card">
                            <div class="card-body">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">{{ stat.label }}</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h2 class="mb-0 ff-secondary fw-semibold">{{ stat.value }}</h2>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-3">
                                            <i class="ri-dashboard-2-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="text-muted mb-0 mt-3">{{ stat.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Perimetro bootstrap</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <h6 class="mb-2">Auth foundation</h6>
                                    <p class="text-muted mb-0">Login, logout, profilo, area riservata e base ruoli non di dominio.</p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <h6 class="mb-2">API foundation</h6>
                                    <p class="text-muted mb-0">Versioning iniziale su <code>/api/v1</code> e profilo utente autenticato via Sanctum.</p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <h6 class="mb-2">Migration discipline</h6>
                                    <p class="text-muted mb-0">Utenti e ruoli bootstrap presenti, organization e ACL finale volutamente rimandati.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Primo focus operativo</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline-2">
                            <div v-for="step in nextSteps" :key="step" class="timeline-continue mb-3">
                                <div class="row timeline-right">
                                    <div class="col-12">
                                        <p class="fw-medium mb-2">
                                            <i class="ri-arrow-right-circle-line align-bottom text-success me-2"></i>{{ step }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
