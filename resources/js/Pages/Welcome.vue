<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
        required: true,
    },
    portalName: {
        type: String,
        required: true,
    },
    boundedContexts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Portal Bootstrap" />

    <div class="layout-wrapper landing">
        <section class="section pb-0 hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-sm-10">
                        <div class="text-center mt-lg-5 pt-5">
                            <h1 class="display-6 fw-semibold mb-3 lh-base">
                                {{ portalName }} nasce su <span class="text-success">Laravel + Vue</span> con shell UI Velzon e governance backend-first.
                            </h1>
                            <p class="lead text-muted lh-base">
                                Bootstrap reale del portale, pronto per evolvere verso Identity + Organization senza copiare architettura o business logic dal legacy CI3.
                            </p>

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <span class="badge bg-primary-subtle text-primary fs-12">Backend-first</span>
                                <span class="badge bg-success-subtle text-success fs-12">API-first</span>
                                <span class="badge bg-info-subtle text-info fs-12">Mobile-ready</span>
                            </div>

                            <div class="d-flex justify-content-center gap-2 mt-4">
                                <Link
                                    v-if="canLogin"
                                    :href="$page.props.auth.user ? route('dashboard') : route('login')"
                                    class="btn btn-primary"
                                >
                                    {{ $page.props.auth.user ? 'Apri dashboard' : 'Accedi al portale' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5 pt-5">
                    <div v-for="context in boundedContexts" :key="context" class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card shadow-none border">
                            <div class="card-body text-center p-4">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                        <i class="ri-layout-grid-line"></i>
                                    </div>
                                </div>
                                <h5 class="fs-15">{{ context }}</h5>
                                <p class="text-muted mb-0">Context predisposto nel bootstrap, senza implementazione business anticipata.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
