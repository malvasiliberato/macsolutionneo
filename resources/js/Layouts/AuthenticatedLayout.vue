<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const page = usePage();
const sidebarOpen = ref(false);
const accountMenuOpen = ref(false);

const navigation = computed(() => page.props.portal.navigation ?? []);
const boundedContexts = computed(() => page.props.portal.boundedContexts ?? []);
const authContext = computed(() => page.props.auth.context ?? {});

watch(sidebarOpen, (isOpen) => {
    document.body.classList.toggle('vertical-sidebar-enable', isOpen);
});

onBeforeUnmount(() => {
    document.body.classList.remove('vertical-sidebar-enable');
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

const toggleAccountMenu = () => {
    accountMenuOpen.value = !accountMenuOpen.value;
};
</script>

<template>
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <div class="navbar-brand-box">
                            <Link :href="route('dashboard')" class="logo logo-dark d-flex align-items-center gap-2">
                                <ApplicationLogo />
                                <span class="logo-lg text-dark fw-semibold">Mac Solution Neo</span>
                            </Link>
                            <Link :href="route('dashboard')" class="logo logo-light d-flex align-items-center gap-2">
                                <ApplicationLogo />
                                <span class="logo-lg text-white fw-semibold">Mac Solution Neo</span>
                            </Link>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            @click="toggleSidebar"
                        >
                            <span class="hamburger-icon open">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                        <div class="app-search d-none d-md-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" value="Backend-first / API-first / Mobile-ready" readonly />
                                <span class="ri-rocket-line search-widget-icon"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end d-none d-md-block me-2">
                            <p class="mb-0 fs-13 fw-semibold text-dark">{{ $page.props.auth.user.name }}</p>
                            <p class="mb-0 fs-12 text-muted">{{ $page.props.auth.user.email }}</p>
                            <p v-if="authContext.active_scope?.label" class="mb-0 fs-11 text-muted">
                                {{ authContext.active_scope.label }}
                            </p>
                        </div>

                        <div class="dropdown topbar-head-dropdown header-item" :class="{ show: accountMenuOpen }">
                            <button
                                type="button"
                                class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                @click="toggleAccountMenu"
                            >
                                <span class="avatar-xs">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle text-uppercase fw-semibold">
                                        {{ $page.props.auth.user.name.slice(0, 2) }}
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" :class="{ show: accountMenuOpen }">
                                <div class="dropdown-header">
                                    <h6 class="text-overflow m-0">Workspace bootstrap</h6>
                                </div>
                                <Link :href="route('profile.edit')" class="dropdown-item" @click="accountMenuOpen = false">
                                    <i class="ri-user-settings-line text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Profilo</span>
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" class="dropdown-item" @click="accountMenuOpen = false">
                                    <i class="ri-logout-box-r-line text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Logout</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="app-menu navbar-menu" :class="{ 'msn-sidebar-open': sidebarOpen }">
            <div class="navbar-brand-box">
                <Link :href="route('dashboard')" class="logo logo-dark d-flex align-items-center gap-2">
                    <ApplicationLogo />
                    <span class="logo-lg text-dark fw-semibold">Mac Solution Neo</span>
                </Link>
                <Link :href="route('dashboard')" class="logo logo-light d-flex align-items-center gap-2">
                    <ApplicationLogo />
                    <span class="logo-lg text-white fw-semibold">Mac Solution Neo</span>
                </Link>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item btn-vertical-sm-hover" @click="closeSidebar">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span>Workspace</span></li>
                        <li class="nav-item" v-for="item in navigation" :key="item.key">
                            <Link :href="route(item.route)" class="nav-link menu-link" :class="{ active: route().current(item.route) }" @click="closeSidebar">
                                <i class="ri-layout-grid-line"></i>
                                <span>{{ item.label }}</span>
                                <span v-if="item.context_label" class="badge bg-soft-info text-info ms-2">{{ item.context_label }}</span>
                            </Link>
                        </li>

                        <li class="menu-title mt-4"><span>Bounded Context</span></li>
                        <li class="nav-item" v-for="context in boundedContexts" :key="context">
                            <span class="nav-link menu-link disabled">
                                <i class="ri-checkbox-blank-circle-line"></i>
                                <span>{{ context }}</span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sidebar-background"></div>
        </div>

        <div class="vertical-overlay" :class="{ active: sidebarOpen }" @click="closeSidebar"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div v-if="$slots.header" class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <slot name="header" />
                        </div>
                    </div>

                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
