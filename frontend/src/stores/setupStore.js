import { defineStore } from "pinia";
import axios from "@/plugins/axios";

export const useSetupStore = defineStore("setup", {
    state: () => ({
        steps: [],
        stepsData: {},
        currentStep: null,
        progress: {
            total_steps: 0,
            completed_steps: 0,
            progress_percentage: 0,
        },
        isLoading: false,
        error: null,
    }),

    getters: {
        currentStepData: (state) =>
            state.steps.find((step) => step.key === state.currentStep) || null,

        canSkipCurrentStep: (state) => state.currentStepData?.can_skip || false,

        isStepAvailable: (state) => (stepKey) => {
            const step = state.steps.find((s) => s.key === stepKey);
            return step && !step.completed;
        },

        setupProgress: (state) => state.progress.progress_percentage,

        getStepData: (state) => (stepKey) => {
            return state.stepsData[stepKey] || null;
        },
    },

    actions: {
        async checkSetupStatus() {
            try {
                this.isLoading = true;
                const { data } = await axios.get("/setup/status");

                this.steps = data.progress.steps;
                this.progress = {
                    total_steps: data.progress.total_steps,
                    completed_steps: data.progress.completed_steps,
                    progress_percentage: data.progress.progress_percentage,
                };

                this.currentStep = data.progress.current_step || "school_info";

                return data.setup_required;
            } catch (error) {
                this.error =
                    error.response?.data?.error || "Erro ao verificar status";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },
        async saveStepData(stepKey, data) {
            try {
                this.isLoading = true;
                const response = await axios.post(`/setup/step/${stepKey}`, {
                    data: data,
                });
                
                this.stepsData[stepKey] = data;
                this.steps = response.data.progress.steps;
                this.progress = {
                    total_steps: response.data.progress.total_steps,
                    completed_steps: response.data.progress.completed_steps,
                    progress_percentage: response.data.progress.progress_percentage,
                };

                return response.data;
            } catch (error) {
                console.error("Erro ao salvar dados do step:", error);
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        async moveToNextStep() {
            const currentIndex = this.steps.findIndex(
                (step) => step.key === this.currentStep
            );
            if (currentIndex < this.steps.length - 1) {
                this.currentStep = this.steps[currentIndex + 1].key;
            }
        },

        async skipStep(stepKey) {
            try {
                this.isLoading = true;
                const { data } = await axios.post(
                    `/setup/step/${stepKey}/skip`
                );

                this.progress = data.progress;
                const stepIndex = this.steps.findIndex(
                    (s) => s.key === stepKey
                );
                if (stepIndex !== -1) {
                    this.steps[stepIndex].completed = true;
                    this.steps[stepIndex].status = "completed";
                    this.steps[stepIndex].skipped = true;
                }

                this.currentStep = data.progress.current_step;

                return data;
            } catch (error) {
                this.error =
                    error.response?.data?.error || "Erro ao pular etapa";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },
    },
});
