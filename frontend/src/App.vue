<template>
    <div id="app">
        <component :is="layout">
            <router-view />
        </component>
    </div>
</template>

<script>
import MainLayout from './layouts/MainLayout.vue'
import AuthLayout from './layouts/AuthLayout.vue'

export default {
  computed: {
    layout() {
      const layoutName = this.$route.meta.layout || 'MainLayout'
      return layoutName === 'AuthLayout' ? AuthLayout : MainLayout
    }
  }
}
</script>
