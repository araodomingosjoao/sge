<template>
    <div class="d-flex justify-content-between mt-4">
        <!-- Botão Voltar -->
        <button 
            class="btn btn-light" 
            @click="$emit('previous')"
            v-if="!isFirstStep"
        >
            <i class="ri-arrow-left-line me-1"></i>
            Voltar
        </button>
        <div v-else></div>

        <div class="ms-auto">
            <!-- Botão Pular (se disponível) -->
            <button
                v-if="canSkip"
                class="btn btn-light me-2"
                @click="$emit('skip')"
            >
                Pular
                <i class="ri-skip-forward-mini-line ms-1"></i>
            </button>

            <!-- Botão Próximo/Finalizar -->
            <button 
                class="btn"
                :class="isLastStep ? 'btn-success' : 'btn-primary'"
                @click="handleSave"
                :disabled="isSubmitting"
            >
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                {{ isLastStep ? 'Finalizar Setup' : 'Próximo' }}
                <i v-if="!isLastStep" class="ri-arrow-right-line ms-1"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { useSetupNavigation } from "@/composables/useSetupNavigation";

const props = defineProps({
    isFirstStep: {
        type: Boolean,
        required: true
    },
    isLastStep: {
        type: Boolean,
        required: true
    },
    canSkip: {
        type: Boolean,
        default: false
    },
    isSubmitting: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['save', 'previous'])

const { isLoading, handleNext } = useSetupNavigation()

const handleSave = async () => {
    try {
        const stepData = await emit('save')
        if (stepData) {
            await handleNext(stepData)
        }
    } catch (error) {
        console.error('Erro ao salvar:', error)
    }
}
</script>