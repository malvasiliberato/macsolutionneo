<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    boundedContexts: {
        type: Array,
        required: true,
    },
    deferredItems: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Workspace Roadmap" />

    <AuthenticatedLayout>
        <template #header>
            <p class="text-muted mb-1">Workspace</p>
            <h4 class="mb-sm-0">Roadmap iniziale</h4>
        </template>

        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Bounded context predisposti</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div v-for="context in boundedContexts" :key="context" class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-soft-info text-info rounded-circle">
                                                <i class="ri-layout-masonry-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ context }}</h6>
                                            <p class="text-muted mb-0">Contesto riconosciuto a livello di bootstrap, non ancora implementato.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Rimandato in modo esplicito</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-4">
                            Velzon e' usato come base visuale della shell, non come base architetturale del portale.
                        </div>
                        <ul class="list-group list-group-flush">
                            <li v-for="item in deferredItems" :key="item" class="list-group-item px-0">
                                <i class="ri-time-line text-muted me-2"></i>{{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
