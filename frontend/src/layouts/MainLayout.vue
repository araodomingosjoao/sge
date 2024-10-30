<template>
    <div>
        <!-- Begin page -->
        <div id="layout-wrapper">
            <Header />

            <!-- Modal Notification -->
            <NotificationModal
                v-if="isModalOpen"
                @close="isModalOpen = false"
                @confirm="handleDeleteNotification"
            />

            <!-- Navbar -->
            <Navbar />

            <!-- Main Content -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <Breadcrumb 
                                    title="Starter" 
                                    :breadcrumbItems="[
                                        { label: 'Pages', link: 'javascript: void(0);', active: false },
                                        { label: 'Starter', link: '', active: true }
                                    ]"
                                />
                            </div>
                        </div>
                        <slot name="content" /> <!-- Para inserir o conteúdo das páginas -->
                    </div>
                </div>
                <!-- Footer -->
                <Footer />
            </div>
        </div>

        <!-- Botão Back to Top -->
        <BackToTopButton />

        <!-- Preloader -->
        <Preloader v-if="isLoading" />
    </div>
</template>

<script>
import Sidebar from '../components/Layout/Sidebar.vue';
import Header from '../components/Layout/Header.vue';
import Navbar from '../components/Layout/Navbar.vue';
import Footer from '../components/Layout/Footer.vue';
import Breadcrumb from '../components/Layout/Breadcrumb.vue';
import NotificationModal from '../components/UI/NotificationModal.vue';
import BackToTopButton from '../components/UI/BackToTopButton.vue';
import Preloader from '../components/UI/Preloader.vue';

export default {
    name: 'MainLayout',
    components: {
        Sidebar,
        Navbar,
        Header,
        Footer,
        Breadcrumb,
        NotificationModal,
        BackToTopButton,
        Preloader,
    },
    data() {
        return {
            isModalOpen: false,
            isLoading: false,
        };
    },
    methods: {
        handleDeleteNotification() {
            this.isModalOpen = false;
        },
    },
};
</script>
