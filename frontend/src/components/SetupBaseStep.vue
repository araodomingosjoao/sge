<template>
    <div class="step-content">
        <header class="step-header mb-4">
            <h5>{{ title }}</h5>
            <p class="text-muted">{{ description }}</p>
        </header>

        <AppForm @event:submit="handleSubmit">
            <slot></slot>
        </AppForm>
    </div>
</template>

<script setup>
import AppForm from '@/components/UI/AppForm.vue';

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

const emit = defineEmits(['submit'])

const handleSubmit = (data) => {
    // emit('submit', data)
}
</script>