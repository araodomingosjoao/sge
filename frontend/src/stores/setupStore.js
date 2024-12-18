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
            state.steps.find((step) => step.key === state.currentStep),
        setupProgress: (state) => state.progress.progress_percentage,
        getStepData: (state) => (stepKey) => state.stepsData[stepKey],

        canSkipCurrentStep: (state) => {
            const step = state.steps.find((s) => s.key === state.currentStep);
            return step?.can_skip ?? false;
        },

        isStepAvailable: (state) => (stepKey) => {
            const step = state.steps.find((s) => s.key === stepKey);
            if (!step) return false;

            const currentIndex = state.steps.findIndex(
                (s) => s.key === state.currentStep
            );
            const stepIndex = state.steps.findIndex((s) => s.key === stepKey);

            return stepIndex <= currentIndex + 1;
        },
    },

    actions: {
        setLoading(status) {
            this.isLoading = status;
        },
        updateProgress(progressData) {
            this.progress = {
                total_steps: progressData.total_steps,
                completed_steps: progressData.completed_steps,
                progress_percentage: progressData.progress_percentage,
            };
        },

        async checkSetupStatus() {
            try {
                this.setLoading(true);
                const { data } = await axios.get("/setup/status");

                this.steps = data.progress.steps;
                this.updateProgress(data.progress);
                this.currentStep = data.progress.current_step || "school_info";

                return data.setup_required;
            } catch (error) {
                this.error =
                    error.response?.data?.error || "Erro ao verificar status";
                throw error;
            } finally {
                this.setLoading(false);
            }
        },

        async saveStepData(stepKey, stepData) {
            if (!stepKey || !stepData) throw new Error("Dados inválidos");

            try {
                this.setLoading(true);
                const { data } = await axios.post(`/setup/step/${stepKey}`, {
                    data: stepData,
                });

                this.stepsData[stepKey] = stepData;
                this.steps = data.progress.steps;
                this.updateProgress(data.progress);

                return data;
            } catch (error) {
                throw new Error(
                    error.response?.data?.error || "Erro ao salvar dados"
                );
            } finally {
                this.setLoading(false);
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
                this.setLoading(true);
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
                this.setLoading(false);
            }
        },
    },
});
