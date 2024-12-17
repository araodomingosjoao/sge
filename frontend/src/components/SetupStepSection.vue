<template>
    <fieldset class="step-section mb-4">
        <legend class="fs-5 mb-3">{{ title }}</legend>
        <slot></slot>
    </fieldset>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    }
})
</script>

<style scoped>
.step-section {
    border: none;
    padding: 0;
    margin: 0;
}

.step-section legend {
    color: #495057;
    font-weight: 500;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
    width: 100%;
}
</style>