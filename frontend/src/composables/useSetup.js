import { ref, computed } from 'vue'
import { useSetupStore } from '@/stores/setupStore'

export function useSetup() {
    const setupStore = useSetupStore()
    const isSubmitting = ref(false)

    // Computed Properties
    const steps = computed(() => setupStore.steps)
    const currentStep = computed(() => setupStore.currentStep)
    const setupProgress = computed(() => setupStore.setupProgress)
    const completedStepKeys = computed(() => 
        steps.value.filter(step => step.completed).map(step => step.key)
    )

    // Estados de Navegação
    const isFirstStep = computed(() => {
        const currentIndex = steps.value.findIndex(step => step.key === currentStep.value)
        return currentIndex === 0
    })

    const isLastStep = computed(() => {
        const currentIndex = steps.value.findIndex(step => step.key === currentStep.value)
        return currentIndex === steps.value.length - 1
    })

    const canSkipCurrentStep = computed(() => {
        const currentStepData = steps.value.find(step => step.key === currentStep.value)
        return currentStepData?.can_skip || false
    })

    // Handlers
    const handleStepClick = (stepKey) => {
        setupStore.currentStep = stepKey
    }

    const handlePrevious = () => {
        const currentIndex = steps.value.findIndex(step => step.key === currentStep.value)
        if (currentIndex > 0) {
            setupStore.currentStep = steps.value[currentIndex - 1].key
        }
    }

    const handleNext = async () => {
        isSubmitting.value = true
        try {
            const currentIndex = steps.value.findIndex(step => step.key === currentStep.value)
            setupStore.currentStep = steps.value[currentIndex + 1].key
        } catch (error) {
            console.error('Erro ao navegar:', error)
        } finally {
            isSubmitting.value = false
        }
    }

    const handleStepComplete = async (stepData) => {
        try {
            await setupStore.completeStep(currentStep.value, stepData)
        } catch (error) {
            console.error('Erro ao completar etapa:', error)
        }
    }

    const handleStepSkip = async () => {
        try {
            await setupStore.skipStep(currentStep.value)
        } catch (error) {
            console.error('Erro ao pular etapa:', error)
        }
    }

    const initializeSetup = async () => {
        await setupStore.checkSetupStatus()
    }

    return {
        isSubmitting,
        steps,
        currentStep,
        setupProgress,
        completedStepKeys,
        isFirstStep,
        isLastStep,
        canSkipCurrentStep,
        handleStepClick,
        handlePrevious,
        handleNext,
        handleStepComplete,
        handleStepSkip,
        initializeSetup
    }
}