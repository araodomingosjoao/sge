<template>
    <div class="setup-wrapper">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Configuração Inicial da Escola</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Menu Lateral -->
                            <div class="col-lg-3">
                                <SetupNavigation
                                    :steps="steps"
                                    :current-step="currentStep"
                                    :completed-steps="completedStepKeys"
                                    @step-click="handleStepClick"
                                />
                            </div>

                            <!-- Conteúdo -->
                            <div class="col-lg-6">
                                <div class="setup-content">
                                    <Suspense>
                                        <template #default>
                                            <component
                                                :is="currentStepComponent"
                                                v-if="currentStep"
                                                @complete="handleStepComplete"
                                                @skip="handleStepSkip"
                                            />
                                        </template>
                                        <template #fallback>
                                            <div class="text-center p-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Carregando...</span>
                                                </div>
                                            </div>
                                        </template>
                                    </Suspense>
                                </div>
                            </div>

                            <!-- Progresso -->
                            <div class="col-lg-3">
                                <SetupProgress
                                    :percentage="setupProgress"
                                    :completed="completedStepKeys.length"
                                    :total="steps.length"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, defineAsyncComponent } from "vue"
import { useSetup } from '../../composables/useSetup'
import SetupNavigation from "../../components/SetupNavigation.vue"
import SetupProgress from "../../components/SetupProgress.vue"
import SetupNavButtons from "../../components/SetupNavButtons.vue"

const stepComponents = {
    school_info: defineAsyncComponent(() => 
        import("../../components/steps/SchoolInfoStep.vue")
    ),
    // 'academic_periods': () => import("@/components/steps/AcademicPeriodsStep.vue"),
    // 'subjects': () => import("@/components/steps/SubjectsStep.vue"),
    // 'classes': () => import("@/components/steps/ClassesStep.vue"),
    // 'departments': () => import("@/components/steps/DepartmentsStep.vue"),
    // 'staff_roles': () => import("@/components/steps/StaffRolesStep.vue"),
    // 'final_review': () => import("@/components/steps/FinalReviewStep.vue")
}

const {
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
} = useSetup()

const currentStepComponent = computed(() => {
    if (!currentStep.value) return null
    return stepComponents[currentStep.value]
})

onMounted(() => {
    initializeSetup()
})
</script>