<template>
    <BaseStep 
        title="Cursos"
        description="Configure os cursos e suas disciplinas"
    >
        <AppForm @event:submit="handleSubmit">
            <!-- Wizard Steps -->
            <div class="wizard-steps mb-4">
                <div class="step" :class="{ 'active': !currentCourse, 'completed': currentCourse }">
                    <div class="step-number">1</div>
                    <div class="step-title">Selecione o Curso</div>
                </div>
                <div class="step" :class="{ 'active': currentCourse && !selectedLevels.length, 'completed': selectedLevels.length }">
                    <div class="step-number">2</div>
                    <div class="step-title">Níveis do Curso</div>
                </div>
                <div class="step" :class="{ 'active': currentCourse && selectedLevels.length }">
                    <div class="step-number">3</div>
                    <div class="step-title">Configure Disciplinas</div>
                </div>
            </div>

            <!-- Step 1: Seleção de Curso -->
            <div v-if="!currentCourse" class="step-content">
                <StepSection title="Selecione o Curso">
                    <div class="d-flex justify-content-end mb-4">
                        <button 
                            type="button" 
                            class="btn btn-primary"
                            @click="showCourseModal = true"
                        >
                            <i class="ri-add-line me-1"></i>
                            Novo Curso
                        </button>
                    </div>
                    
                    <div class="course-grid">
                        <div 
                            v-for="course in courseStructure?.courses" 
                            :key="course.id"
                            class="course-card"
                            :class="{ 'has-content': course.disciplines.length > 0 }"
                            @click="selectCourse(course)"
                        >
                            <div class="course-info">
                                <h4>{{ course.name }}</h4>
                                <p>{{ course.disciplines.length }} disciplinas</p>
                            </div>
                            <div class="course-icon">
                                <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </div>
                </StepSection>
            </div>

            <!-- Step 2: Seleção de Níveis -->
            <div v-else-if="!selectedLevels.length" class="step-content">
                <StepSection>
                    <div class="selected-context mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">{{ currentCourse.name }}</h5>
                                <p class="mb-0">Selecione os níveis para este curso</p>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" @click="currentCourse = null">
                                <i class="ri-arrow-left-line me-1"></i>
                                Voltar
                            </button>
                        </div>
                    </div>

                    <div class="levels-grid">
                        <div 
                            v-for="level in courseStructure?.levels" 
                            :key="level.id"
                            class="level-card"
                            :class="{ 'selected': isLevelSelected(level.id) }"
                            @click="toggleLevel(level)"
                        >
                            <div class="level-info">
                                <h4>{{ level.name }}</h4>
                                <p>{{ level.year }}º Ano</p>
                            </div>
                            <div class="level-icon">
                                <i class="ri-checkbox-blank-line" v-if="!isLevelSelected(level.id)"></i>
                                <i class="ri-checkbox-fill text-success" v-else></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button 
                            type="button" 
                            class="btn btn-primary"
                            :disabled="!tempSelectedLevels.length"
                            @click="confirmLevelSelection"
                        >
                            Continuar
                            <i class="ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>
                </StepSection>
            </div>

            <!-- Step 3: Configuração de Disciplinas -->
            <div v-else class="step-content">
                <StepSection>
                    <div class="selected-context mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">{{ currentCourse.name }}</h5>
                                <div class="selected-levels">
                                    <span 
                                        v-for="level in selectedLevels" 
                                        :key="level.id"
                                        class="badge bg-secondary me-2"
                                    >
                                        {{ level.name }}
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" @click="selectedLevels = []">
                                <i class="ri-arrow-left-line me-1"></i>
                                Voltar
                            </button>
                        </div>
                    </div>

                    <!-- Barra de Ações -->
                    <div class="action-bar mb-4">
                        <div class="search-box">
                            <AppFormInput
                                id="search"
                                v-model="searchTerm"
                                placeholder="Buscar disciplinas..."
                            >
                                <template #prepend>
                                    <i class="ri-search-line"></i>
                                </template>
                            </AppFormInput>
                        </div>
                        <button 
                            type="button" 
                            class="btn btn-primary"
                            @click="showDisciplineModal = true"
                        >
                            <i class="ri-add-line me-1"></i>
                            Nova Disciplina
                        </button>
                    </div>

                    <!-- Lista de Disciplinas -->
                    <div class="disciplines-management">
                        <!-- Disciplinas Disponíveis -->
                        <div class="available-disciplines">
                            <h6 class="list-title">
                                Disciplinas Disponíveis
                                <span class="badge rounded-pill bg-secondary ms-2">
                                    {{ availableDisciplines.length }}
                                </span>
                            </h6>
                            <div class="discipline-list">
                                <div 
                                    v-for="discipline in filteredDisciplines" 
                                    :key="discipline.id"
                                    class="discipline-item"
                                    :class="{ 'is-selected': isDisciplineSelected(discipline.id) }"
                                    @click="toggleDiscipline(discipline)"
                                >
                                    <div class="discipline-name">{{ discipline.name }}</div>
                                    <div class="discipline-action">
                                        <i class="ri-checkbox-blank-line" v-if="!isDisciplineSelected(discipline.id)"></i>
                                        <i class="ri-checkbox-fill text-success" v-else></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Disciplinas Selecionadas -->
                        <div class="selected-disciplines">
                            <h6 class="list-title">
                                Disciplinas do Curso
                                <span class="badge rounded-pill bg-primary ms-2">
                                    {{ selectedDisciplines.length }}
                                </span>
                            </h6>
                            <div class="discipline-list">
                                <draggable 
                                    v-model="selectedDisciplines" 
                                    item-key="id"
                                    handle=".drag-handle"
                                >
                                    <template #item="{ element: discipline }">
                                        <div class="discipline-item">
                                            <div class="drag-handle">
                                                <i class="ri-drag-move-2-line"></i>
                                            </div>
                                            <div class="discipline-name">{{ discipline.name }}</div>
                                            <button 
                                                class="btn-remove"
                                                @click="removeDiscipline(discipline)"
                                            >
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </StepSection>
            </div>
        </AppForm>

        <SetupNavButtons
            :is-first-step="false"
            :is-last-step="false"
            :disabled="!canSave"
            @save="handleSubmitForm"
            @previous="$emit('previous')"
        />

        <!-- Modal Novo Curso -->
        <AppModal
            v-model="showCourseModal"
            title="Novo Curso"
            size="md"
        >
            <template #body>
                <AppForm @submit="handleNewCourse">
                    <div class="mb-3">
                        <AppFormInput
                            id="course_name"
                            label="Nome do Curso"
                            v-model="newCourse.name"
                            rules="required"
                        />
                    </div>
                    <div class="mb-3">
                        <AppFormInput
                            id="course_code"
                            label="Código"
                            v-model="newCourse.code"
                            rules="required"
                        />
                    </div>
                </AppForm>
            </template>
            <template #footer>
                <button 
                    type="button" 
                    class="btn btn-secondary" 
                    @click="showCourseModal = false"
                >
                    Cancelar
                </button>
                <button 
                    type="button" 
                    class="btn btn-primary"
                    @click="handleNewCourse"
                >
                    Salvar
                </button>
            </template>
        </AppModal>

        <!-- Modal Nova Disciplina -->
        <AppModal
            v-model="showDisciplineModal"
            title="Nova Disciplina"
            size="md"
        >
            <template #body>
                <AppForm @submit="handleNewDiscipline">
                    <div class="mb-3">
                        <AppFormInput
                            id="discipline_name"
                            label="Nome da Disciplina"
                            v-model="newDiscipline.name"
                            rules="required"
                        />
                    </div>
                    <div class="mb-3">
                        <AppFormInput
                            id="discipline_code"
                            label="Código"
                            v-model="newDiscipline.code"
                            rules="required"
                        />
                    </div>
                </AppForm>
            </template>
            <template #footer>
                <button 
                    type="button" 
                    class="btn btn-secondary" 
                    @click="showDisciplineModal = false"
                >
                    Cancelar
                </button>
                <button 
                    type="button" 
                    class="btn btn-primary"
                    @click="handleNewDiscipline"
                >
                    Salvar
                </button>
            </template>
        </AppModal>
    </BaseStep>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCoursesStep } from '@/composables/steps/useCoursesStep'
import draggable from 'vuedraggable'
import BaseStep from '../SetupBaseStep.vue'
import StepSection from '../SetupStepSection.vue'
import AppForm from '../UI/AppForm.vue'
import AppFormInput from '../UI/AppFormInput.vue'
import AppModal from '../UI/AppModal.vue'
import SetupNavButtons from '../SetupNavButtons.vue'

const {
    courseStructure,
    loading,
    fetchCourseStructure,
    associateDisciplinesToCourse,
    removeDisciplineFromCourse,
    handleSubmit
} = useCoursesStep()

// Estado do wizard
const currentCourse = ref(null)
const selectedLevels = ref([])
const tempSelectedLevels = ref([])
const selectedDisciplines = ref([])
const searchTerm = ref('')

// Estado dos modais
const showCourseModal = ref(false)
const showDisciplineModal = ref(false)
const newCourse = ref({ name: '', code: '' })
const newDiscipline = ref({ name: '', code: '' })

// Computed Properties
const availableDisciplines = computed(() => {
    return courseStructure.value?.disciplines || []
})

const filteredDisciplines = computed(() => {
    if (!availableDisciplines.value) return []
    
    return availableDisciplines.value.filter(discipline => 
        discipline.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
})

const canSave = computed(() => {
    return currentCourse.value && 
           selectedLevels.value.length > 0 && 
           selectedDisciplines.value.length > 0
})

// Métodos de Seleção
const selectCourse = (course) => {
    currentCourse.value = course
    selectedLevels.value = []
    selectedDisciplines.value = course.disciplines || []
}

const isLevelSelected = (levelId) => {
    return tempSelectedLevels.value.some(level => level.id === levelId)
}

const toggleLevel = (level) => {
    const index = tempSelectedLevels.value.findIndex(l => l.id === level.id)
    if (index === -1) {
        tempSelectedLevels.value.push(level)
    } else {
        tempSelectedLevels.value.splice(index, 1)
    }
}

const confirmLevelSelection = () => {
    selectedLevels.value = [...tempSelectedLevels.value]
}

const isDisciplineSelected = (disciplineId) => {
    return selectedDisciplines.value.some(d => d.id === disciplineId)
}

const toggleDiscipline = async (discipline) => {
    if (isDisciplineSelected(discipline.id)) {
        await removeDisciplineFromCourse(currentCourse.value.id, discipline.id)
        selectedDisciplines.value = selectedDisciplines.value.filter(d => d.id !== discipline.id)
    } else {
        selectedDisciplines.value.push(discipline)
        await associateDisciplinesToCourse(
            currentCourse.value.id, 
            [discipline.id],
            selectedLevels.value.map(l => l.id)
        )
    }
}

// Handlers
const handleNewCourse = async () => {
    // TODO: Implementar criação de curso quando tivermos o endpoint
    showCourseModal.value = false
    newCourse.value = { name: '', code: '' }
    await fetchCourseStructure()
}

const handleNewDiscipline = async () => {
    // TODO: Implementar criação de disciplina
    showDisciplineModal.value = false
    newDiscipline.value = { name: '', code: '' }
    await fetchCourseStructure()
}

const handleSubmitForm = () => {
    handleSubmit({
        course_id: currentCourse.value.id,
        discipline_ids: selectedDisciplines.value.map(d => d.id),
        level_ids: selectedLevels.value.map(l => l.id)
    })
}

// Lifecycle
onMounted(async () => {
    await fetchCourseStructure()
})
</script>

<style scoped>
.wizard-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
}

.step-number {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
}

.step.active .step-number {
    background-color: var(--bs-primary);
    color: white;
}

.step.completed .step-number {
    background-color: var(--bs-success);
    color: white;
}

.course-grid, .levels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.course-card, .level-card {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-card:hover, .level-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
}

.has-content {
    border-left: 4px solid var(--bs-success);
}

.selected {
    background-color: #e8f5e9;
    border-color: var(--bs-success);
}

.disciplines-management {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.discipline-list {
    max-height: 500px;
    overflow-y: auto;
}

.discipline-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    cursor: pointer;
}

.selected-context {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.selected-levels {
    margin-top: 0.5rem;
}

.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.search-box {
    flex: 1;
    max-width: 300px;
}
</style>