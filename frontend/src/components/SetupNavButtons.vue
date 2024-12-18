<template>
    <div class="d-flex justify-content-between mt-4">
        <button 
            v-if="!isFirstStep"
            class="btn btn-light" 
            @click="$emit('previous')"
            :disabled="setupStore.isLoading"
        >
            <i class="ri-arrow-left-line me-1"></i>
            Voltar
        </button>

        <div class="ms-auto">
            <button
                v-if="canSkip"
                class="btn btn-light me-2"
                @click="$emit('skip')"
                :disabled="setupStore.isLoading"
            >
                Pular
                <i class="ri-skip-forward-mini-line ms-1"></i>
            </button>

            <button 
                class="btn"
                :class="submitButtonClass"
                @click="emit('save')"
                :disabled="setupStore.isLoading"
            >
                <span v-if="setupStore.isLoading" class="spinner-border spinner-border-sm me-1"></span>
                {{ submitButtonText }}
                <i v-if="!isLastStep" class="ri-arrow-right-line ms-1"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useSetupStore } from '@/stores/setupStore'

const setupStore = useSetupStore()

const props = defineProps({
    isFirstStep: Boolean,
    isLastStep: Boolean,
    canSkip: Boolean
})

const submitButtonClass = computed(() => ({
    'btn-success': props.isLastStep,
    'btn-primary': !props.isLastStep
}))

const submitButtonText = computed(() => 
    props.isLastStep ? 'Finalizar Setup' : 'Próximo'
)

const emit = defineEmits(['save', 'previous'])
</script>