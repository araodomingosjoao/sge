<template>
    <BaseStep 
        title="Informações Básicas da Escola"
        description="Preencha os dados fundamentais da instituição"
        @submit="handleSubmit"
    >
        <!-- Informações da Escola -->
        <StepSection title="Informações da Escola">
            <div class="row g-3">
                <div class="col-md-12">
                    <AppFormInput
                        id="school_name"
                        label="Nome da Escola"
                        v-model="formData.school_name"
                        rules="required"
                        placeholder="Digite o nome da escola"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormSelect
                        id="type_education_id"
                        label="Tipo de Ensino"
                        v-model="formData.type_education_id"
                        :options="options.optionsTypeEducation"
                        rules="required"
                        placeholder="Selecione o tipo de ensino"
                    />
                </div>

                <div class="col-md-6" v-if="formData.type_education_id == '4'">
                    <AppFormSelect
                        id="category_id"
                        label="Categoria"
                        v-model="formData.category_id"
                        :options="options.optionsCategory"
                        rules="required"
                        placeholder="Selecione a categoria"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="registration_number"
                        label="Número de Registro"
                        v-model="formData.registration_number"
                        rules="required"
                        placeholder="Ex: REG123456"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="founded_year"
                        label="Ano de Fundação"
                        type="number"
                        v-model="formData.founded_year"
                        rules="required"
                        placeholder="Ex: 1999"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="school_email"
                        label="Email da Escola"
                        type="email"
                        v-model="formData.school_email"
                        rules="required|email"
                        placeholder="escola@exemplo.com"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="school_phone"
                        label="Telefone da Escola"
                        v-model="formData.school_phone"
                        rules="required"
                        placeholder="Ex: +244 923 456 789"
                    />
                </div>

                <div class="col-12">
                    <AppFormInput
                        id="website"
                        label="Website"
                        type="url"
                        v-model="formData.website"
                        placeholder="https://www.exemplo.com"
                    />
                </div>
            </div>
        </StepSection>

        <!-- Informações do Responsável -->
        <StepSection title="Informações do Responsável">
            <div class="row g-3">
                <div class="col-md-6">
                    <AppFormInput
                        id="first_name"
                        label="Nome"
                        v-model="formData.first_name"
                        rules="required"
                        placeholder="Digite o nome"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="last_name"
                        label="Apelido"
                        v-model="formData.last_name"
                        rules="required"
                        placeholder="Digite o apelido"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="email"
                        label="Email"
                        type="email"
                        v-model="formData.email"
                        rules="required|email"
                        placeholder="nome@exemplo.com"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormInput
                        id="phone"
                        label="Telefone"
                        v-model="formData.phone"
                        rules="required"
                        placeholder="Ex: +244 923 456 789"
                    />
                </div>
            </div>
        </StepSection>

        <!-- Endereço da Escola -->
        <StepSection title="Endereço da Escola">
            <div class="row g-3">
                <div class="col-md-6">
                    <AppFormSelect
                        id="state"
                        label="Província"
                        v-model="formData.state"
                        :options="provinces"
                        option-label="name"
                        option-value="id"
                        rules="required"
                        placeholder="Selecione a província"
                        @update:model-value="handleProvinceChange"
                    />
                </div>

                <div class="col-md-6">
                    <AppFormSelect
                        id="city"
                        label="Município"
                        v-model="formData.city"
                        :options="municipalities"
                        rules="required"
                        placeholder="Selecione o município"
                        :disabled="!formData.state"
                    />
                </div>

                <div class="col-md-12">
                    <AppFormInput
                        id="address"
                        label="Endereço Completo"
                        v-model="formData.address"
                        rules="required"
                        placeholder="Ex: Rua Principal, Nº 123, Bairro"
                    />
                </div>
            </div>
        </StepSection>
        <SetupNavButtons
            :is-first-step="true"
            :is-last-step="false"
            @save="handleSubmit"
        />
    </BaseStep>
</template>

<script setup>
import { onMounted } from 'vue'
import { useSchoolInfoStep } from '@/composables/steps/useSchoolInfoStep'
import BaseStep from '../SetupBaseStep.vue'
import StepSection from '../SetupStepSection.vue'
import AppFormInput from '../UI/AppFormInput.vue'
import AppFormSelect from '../UI/AppFormSelect.vue'
import SetupNavButtons from "../../components/SetupNavButtons.vue"

const {
    formData,
    options,
    provinces,
    municipalities,
    handleProvinceChange,
    handleSubmit,
    initialize
} = useSchoolInfoStep()

onMounted(async () => {
    await initialize() 
})
</script>