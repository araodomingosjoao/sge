import { ref } from "vue";
import { useSetupStore } from "@/stores/setupStore";

export function useSetupNavigation() {
    const setupStore = useSetupStore();
    const isLoading = ref(false);

    const handleNext = async (stepData) => {
        try {
            isLoading.value = true;
            await setupStore.saveStepData(setupStore.currentStep, stepData);
            await setupStore.moveToNextStep();
        } catch (error) {
            console.error("Erro ao avançar:", error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        isLoading,
        handleNext,
    };
}
