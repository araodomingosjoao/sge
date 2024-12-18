<template>
    <div class="step-content" :class="{ 'is-loading': setupStore.isLoading }">
        <div v-if="setupStore.isLoading" class="loading-overlay">
            <div class="spinner-border text-primary"></div>
        </div>
        <header class="step-header mb-4">
            <h5>{{ title }}</h5>
            <p class="text-muted">{{ description }}</p>
        </header>

        <slot></slot>
    </div>
</template>

<script setup>
import { useSetupStore } from '@/stores/setupStore'
const setupStore = useSetupStore()

defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    }
})
</script>

<style scoped>
.step-content {
    position: relative;
    min-height: 300px;
}

.is-loading {
    pointer-events: none;
    opacity: 0.7;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}
</style>
