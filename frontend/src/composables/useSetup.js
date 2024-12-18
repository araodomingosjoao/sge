import { computed } from 'vue'
import { useSetupStore } from '@/stores/setupStore'

export function useSetup() {
    const setupStore = useSetupStore()

    // Computed Properties
    const steps = computed(() => setupStore.steps)
    const currentStep = computed(() => setupStore.currentStep)
    const setupProgress = computed(() => setupStore.setupProgress)
    
    // Encontra o índice do step atual
    const getCurrentStepIndex = () => steps.value.findIndex(step => step.key === currentStep.value)

    // Computed properties simplificadas
    const completedStepKeys = computed(() => steps.value
        .filter(step => step.completed)
        .map(step => step.key)
    )

    const isFirstStep = computed(() => getCurrentStepIndex() === 0)
    const isLastStep = computed(() => getCurrentStepIndex() === steps.value.length - 1)
    const canSkipCurrentStep = computed(() => steps.value
        .find(step => step.key === currentStep.value)?.can_skip ?? false
    )

    // Handlers simplificados
    const handleStepClick = (stepKey) => {
        if (stepKey && steps.value.some(step => step.key === stepKey)) {
            setupStore.currentStep = stepKey
        }
    }

    const handlePrevious = () => {
        const currentIndex = getCurrentStepIndex()
        if (currentIndex > 0) {
            setupStore.currentStep = steps.value[currentIndex - 1].key
        }
    }

    const handleNext = async () => {
        const currentIndex = getCurrentStepIndex()
        if (currentIndex < steps.value.length - 1) {
            try {
                setupStore.setLoading(true)
                setupStore.currentStep = steps.value[currentIndex + 1].key
            } catch (error) {
                console.error('Erro ao navegar:', error)
                throw error
            } finally {
                setupStore.setLoading(false)
            }
        }
    }

    // Handlers com tratamento de erro melhorado
    const handleStepComplete = async (stepData) => {
        if (!currentStep.value || !stepData) return

        try {
            setupStore.setLoading(true)
            await setupStore.completeStep(currentStep.value, stepData)
        } catch (error) {
            console.error('Erro ao completar etapa:', error)
            throw error
        } finally {
            setupStore.setLoading(false)
        }
    }

    const handleStepSkip = async () => {
        if (!currentStep.value || !canSkipCurrentStep.value) return

        try {
            setupStore.setLoading(true)
            await setupStore.skipStep(currentStep.value)
        } catch (error) {
            console.error('Erro ao pular etapa:', error)
            throw error
        } finally {
            setupStore.setLoading(false)
        }
    }

    const initializeSetup = async () => {
        try {
            setupStore.setLoading(true)
            await setupStore.checkSetupStatus()
        } catch (error) {
            console.error('Erro ao inicializar setup:', error)
            throw error
        } finally {
            setupStore.setLoading(false)
        }
    }

    return {
        // Estado
        steps,
        currentStep,
        setupProgress,
        completedStepKeys,
        
        // Navegação
        isFirstStep,
        isLastStep,
        canSkipCurrentStep,
        
        // Handlers
        handleStepClick,
        handlePrevious,
        handleNext,
        handleStepComplete,
        handleStepSkip,
        initializeSetup
    }
}