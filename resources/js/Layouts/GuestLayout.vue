<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="auth-page-wrapper pt-5 msn-auth-shell">
        <div class="auth-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <Link href="/" class="d-inline-flex align-items-center gap-3">
                                    <ApplicationLogo />
                                    <span class="fs-3 fw-semibold text-dark">Mac Solution Neo</span>
                                </Link>
                            </div>
                            <p class="mt-3 fs-15 text-muted">Bootstrap Laravel + Vue con shell UI ispirata a Velzon.</p>
                        </div>

                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <slot />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
