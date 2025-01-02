<template>
    <div 
        class="modal fade zoomIn" 
        :class="{ 'show': modelValue }" 
        tabindex="-1" 
        aria-hidden="true"
    >
        <div 
            class="modal-dialog modal-dialog-centered"
            :class="[sizeClass]"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-if="title" class="modal-title">
                        <slot name="title">{{ title }}</slot>
                    </h5>
                    <button 
                        v-if="showClose" 
                        type="button" 
                        class="btn-close" 
                        @click="close" 
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <slot>
                        <slot name="body"></slot>
                    </slot>
                </div>
                <div v-if="$slots.footer" class="modal-footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    showClose: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'close'])

const sizeClass = computed(() => {
    const sizes = {
        sm: 'modal-sm',
        md: '',
        lg: 'modal-lg',
        xl: 'modal-xl'
    }
    return sizes[props.size]
})

const close = () => {
    emit('update:modelValue', false)
    emit('close')
}

watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        document.body.classList.add('modal-open')
    } else {
        document.body.classList.remove('modal-open')
    }
})
</script>

<style scoped>
.modal.show {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
}

/* Animação de Zoom */
.zoomIn {
    animation: zoomIn 0.3s ease-out;
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale3d(0.3, 0.3, 0.3);
    }
    50% {
        opacity: 1;
    }
}
</style>