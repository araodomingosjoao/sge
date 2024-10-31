<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <router-link to="/dashboard" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="/assets_velzon/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets_velzon/images/logo-dark.png" alt="" height="17">
                            </span>
                        </router-link>

                        <router-link to="/dashboard" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="/assets_velzon/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets_velzon/images/logo-light.png" alt="" height="17">
                            </span>
                        </router-link>
                    </div>

                    <!-- Toggle Menu Button -->
                    <button type="button" class="btn btn-sm px-3 fs-16 header-item topnav-hamburger">
                        <span class="hamburger-icon">
                            <span></span><span></span><span></span>
                        </span>
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <!-- Dark/Light Mode Toggle -->
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        @click="toggleDarkMode">
                        <i :class="isDarkMode ? 'bx bx-sun' : 'bx bx-moon'"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle header-profile-user" src="/assets_velzon/images/users/avatar-1.jpg"
                                    alt="Header Avatar">
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">Anna
                                        Adame</span>
                                    <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Founder</span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Welcome Anna!</h6>
                            <template v-for="item in menuHeader" :key="item.name">
                                <a v-if="item.action !== 'logout'" class="dropdown-item" :href="item.link">
                                    <i :class="item.icon + ' text-muted fs-16 align-middle me-1'"></i>
                                    <span class="align-middle">{{ item.label }}</span>
                                </a>
                                <a v-else class="dropdown-item" @click.prevent="handleLogout">
                                    <i :class="item.icon + ' text-muted fs-16 align-middle me-1'"></i>
                                    <span class="align-middle">{{ item.label }}</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import { menuHeader } from '../../config/menuHeader';
import { useAuth } from '../../composables/useAuth';

export default {
    name: 'Header',
    data() {
        return {
            isDarkMode: false,
            menuHeader: menuHeader
        };
    },
    setup() {
        const { logout } = useAuth();

        const handleLogout = () => {
            logout();
            window.location.href = '/auth/login';
        };

        return { handleLogout };
    },
    methods: {
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
        }
    }
};
</script>

