<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="/assets_velzon/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets_velzon/images/logo-dark.png" alt="" height="17">
                            </span>
                        </a>

                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="/assets_velzon/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets_velzon/images/logo-light.png" alt="" height="17">
                            </span>
                        </a>
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
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" @click="toggleDarkMode">
                        <i :class="isDarkMode ? 'bx bx-sun' : 'bx bx-moon'"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown ms-3 header-item topbar-user">
                        <button type="button" class="btn" data-bs-toggle="dropdown">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle header-profile-user" src="/assets_velzon/images/users/avatar-1.jpg" alt="User Avatar">
                                <span class="d-none d-xl-inline-block ms-2">Anna Adame</span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="/profile"><i class="mdi mdi-account-circle"></i> Profile</a>
                            <a class="dropdown-item" href="/logout"><i class="mdi mdi-logout"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
export default {
    name: 'Header',
    data() {
        return {
            isDarkMode: false,
        };
    },
    methods: {
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
        }
    }
};
</script>

<style scoped>
/* Estilize conforme necessário */
</style>
