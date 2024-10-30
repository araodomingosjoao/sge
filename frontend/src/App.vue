<template>
  <div id="app">
    <component :is="layout">
      <router-view />
    </component>
  </div>
</template>

<script>
import MainLayout from './layouts/MainLayout.vue';
import AuthLayout from './layouts/AuthLayout.vue';
import ErrorLayout from './layouts/ErrorLayout.vue';

export default {
  computed: {
    layout() {
      const layouts = {
        MainLayout,
        AuthLayout,
        ErrorLayout,
      };

      const layoutName = this.$route.meta.layout || 'MainLayout';
      
      return layouts[layoutName] || layouts.MainLayout;
    },
  },
};
</script>
