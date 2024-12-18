<template>
    <BaseStep 
        title="Períodos Acadêmicos"
        description="Configure o ano letivo e os trimestres"
    >
        <AppForm @event:submit="() => handleSubmit(formData)">
            <StepSection title="Ano Letivo">
                <div class="row g-3" v-if="currentYear">
                    <div class="col-md-4">
                        <AppFormInput
                            id="year"
                            label="Ano"
                            v-model="formData.year"
                            type="number"
                            :value="currentYear.year"
                            disabled
                        />
                    </div>
                    <div class="col-md-4">
                        <AppFormInput
                            id="start_date"
                            label="Data Início"
                            v-model="formData.start_date"
                            type="date"
                            rules="required"
                        />
                    </div>
                    <div class="col-md-4">
                        <AppFormInput
                            id="end_date"
                            label="Data Fim"
                            v-model="formData.end_date"
                            type="date"
                            rules="required"
                        />
                    </div>
                </div>
            </StepSection>

            <StepSection title="Trimestres">
                <div v-if="currentYear?.trimesters">
                    <div 
                        v-for="(trimester, index) in formData.trimesters" 
                        :key="trimester.id"
                        class="trimester-item mb-4"
                    >
                        <h6>{{ trimester.name }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <AppFormInput
                                    :id="`trimester_start_${index}`"
                                    label="Início"
                                    v-model="trimester.start_date"
                                    type="date"
                                    rules="required"
                                />
                            </div>
                            <div class="col-md-6">
                                <AppFormInput
                                    :id="`trimester_end_${index}`"
                                    label="Fim"
                                    v-model="trimester.end_date"
                                    type="date"
                                    rules="required"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </StepSection>
        </AppForm>

        <SetupNavButtons
            :is-first-step="false"
            :is-last-step="false"
            @save="() => handleSubmit(formData)"
            @previous="$emit('previous')"
        />
    </BaseStep>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useAcademicPeriodsStep } from '@/composables/steps/useAcademicPeriodsStep'
import BaseStep from '../SetupBaseStep.vue'
import StepSection from '../SetupStepSection.vue'
import AppForm from '../UI/AppForm.vue'
import AppFormInput from '../UI/AppFormInput.vue'
import SetupNavButtons from '../SetupNavButtons.vue'

const {
    academicYears,
    currentYear,
    fetchAcademicYears,
    handleSubmit
} = useAcademicPeriodsStep()

const formData = ref({
    year: null,
    start_date: '',
    end_date: '',
    status: 'active',
    trimesters: []
})

watch(currentYear, (year) => {
    if (year) {
        formData.value = {
            year: year.year,
            start_date: year.start_date,
            end_date: year.end_date,
            status: year.status,
            trimesters: year.trimesters.map(trimester => ({
                id: trimester.id,
                name: trimester.name,
                start_date: trimester.start_date,
                end_date: trimester.end_date
            }))
        }
    }
}, { immediate: true })

onMounted(() => {
    fetchAcademicYears()
})
</script>

<style scoped>
.trimester-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.trimester-item h6 {
    margin-bottom: 1rem;
    color: #495057;
}
</style>