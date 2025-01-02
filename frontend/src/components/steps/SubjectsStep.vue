<template>
    <BaseStep
        title="Disciplinas"
        description="Configure as disciplinas por nível de ensino e curso"
    >
        <AppForm @event:submit="handleSubmit">
            <!-- Wizard Steps -->
            <div class="wizard-steps mb-4">
                <div
                    class="step"
                    :class="{ active: !currentLevel, completed: currentLevel }"
                >
                    <div class="step-number">1</div>
                    <div class="step-title">Selecione o Nível</div>
                </div>
                <div
                    class="step"
                    :class="{
                        active: currentLevel && !currentCourse,
                        completed: currentCourse,
                    }"
                >
                    <div class="step-number">2</div>
                    <div class="step-title">Selecione o Curso</div>
                </div>
                <div
                    class="step"
                    :class="{ active: currentLevel && currentCourse }"
                >
                    <div class="step-number">3</div>
                    <div class="step-title">Configure Disciplinas</div>
                </div>
            </div>

            <!-- Step 1: Seleção de Nível -->
            <div v-if="!currentLevel" class="step-content">
                <StepSection title="Selecione o Nível de Ensino">
                    <div class="level-grid">
                        <div
                            v-for="level in disciplineStructure?.levels"
                            :key="level.id"
                            class="level-card"
                            :class="{
                                'has-disciplines': level.disciplines.length > 0,
                            }"
                            @click="selectLevel(level)"
                        >
                            <div class="level-info">
                                <h4>{{ level.name }}</h4>
                                <p>
                                    {{ level.disciplines.length }} disciplinas
                                    configuradas
                                </p>
                            </div>
                            <div class="level-icon">
                                <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </div>
                </StepSection>
            </div>

            <!-- Step 2: Seleção de Curso -->
            <div v-else-if="!currentCourse" class="step-content">
                <StepSection title="Selecione o Curso">
                    <div class="course-grid">
                        <div
                            v-for="course in disciplineStructure?.courses"
                            :key="course.id"
                            class="course-card"
                            @click="selectCourse(course)"
                        >
                            <div class="course-info">
                                <h4>{{ course.name }}</h4>
                                <p>
                                    {{ course.disciplines.length }} disciplinas
                                    disponíveis
                                </p>
                            </div>
                            <div class="course-icon">
                                <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="currentLevel = null"
                        >
                            <i class="ri-arrow-left-line me-1"></i>
                            Voltar para Seleção de Nível
                        </button>
                    </div>
                </StepSection>
            </div>

            <!-- Step 3: Configuração de Disciplinas -->
            <div v-else class="step-content">
                <StepSection>
                    <div class="selected-context mb-4">
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div>
                                <h5 class="mb-1">
                                    Configurando disciplinas para:
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary me-2">{{
                                        selectedLevel?.name
                                    }}</span>
                                    <span class="badge bg-secondary">{{
                                        selectedCourse?.name
                                    }}</span>
                                </p>
                            </div>
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                @click="currentCourse = null"
                            >
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

                    <!-- Disciplinas -->
                    <div class="disciplines-management">
                        <!-- Lista de Disciplinas Disponíveis -->
                        <div class="available-disciplines">
                            <h6 class="list-title">
                                Disciplinas Disponíveis
                                <span
                                    class="badge rounded-pill bg-secondary ms-2"
                                >
                                    {{ courseDisciplines.length }}
                                </span>
                            </h6>
                            <div class="discipline-list">
                                <div
                                    v-for="discipline in filteredDisciplines"
                                    :key="discipline.id"
                                    class="discipline-item"
                                    :class="{
                                        'is-selected': isSelected(
                                            discipline.id
                                        ),
                                    }"
                                    @click="addDiscipline(discipline)"
                                >
                                    <div class="discipline-name">
                                        {{ discipline.name }}
                                    </div>
                                    <div class="discipline-action">
                                        <i
                                            class="ri-add-line"
                                            v-if="!isSelected(discipline.id)"
                                        ></i>
                                        <i
                                            class="ri-check-line text-success"
                                            v-else
                                        ></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Disciplinas Selecionadas -->
                        <div class="selected-disciplines">
                            <h6 class="list-title">
                                Disciplinas do Nível
                                <span
                                    class="badge rounded-pill bg-primary ms-2"
                                >
                                    {{ levelDisciplines.length }}
                                </span>
                            </h6>
                            <div class="discipline-list">
                                <draggable
                                    v-model="levelDisciplines"
                                    item-key="id"
                                    handle=".drag-handle"
                                >
                                    <template #item="{ element: discipline }">
                                        <div class="discipline-item">
                                            <div class="drag-handle">
                                                <i
                                                    class="ri-drag-move-2-line"
                                                ></i>
                                            </div>
                                            <div class="discipline-name">
                                                {{ discipline.name }}
                                            </div>
                                            <button
                                                class="btn-remove"
                                                @click="
                                                    removeDiscipline(discipline)
                                                "
                                            >
                                                <i
                                                    class="ri-delete-bin-line"
                                                ></i>
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

        <!-- Navegação -->
        <SetupNavButtons
            :is-first-step="false"
            :is-last-step="false"
            :disabled="!canSave"
            @save="handleSubmitForm"
            @previous="$emit('previous')"
        />

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
import { ref, computed, watch, onMounted } from "vue";
import { useSubjectsStep } from "@/composables/steps/useSubjectsStep";
import BaseStep from "../SetupBaseStep.vue";
import StepSection from "../SetupStepSection.vue";
import AppForm from "../UI/AppForm.vue";
import AppFormInput from "../UI/AppFormInput.vue";
import AppFormSelect from "../UI/AppFormSelect.vue";
import AppModal from "../UI/AppModal.vue";
import SetupNavButtons from "../SetupNavButtons.vue";
import draggable from "vuedraggable";

const {
    disciplineStructure,
    fetchDisciplineStructure,
    removeDisciplineFromLevel,
    handleSubmit,
    createDiscipline,
} = useSubjectsStep();

const currentLevel = ref(null);
const currentCourse = ref(null);
const searchTerm = ref("");
const showDisciplineModal = ref(false);
const newDiscipline = ref({
    name: "",
    code: "",
});

// Funções de seleção para o wizard
const selectLevel = (level) => {
    currentLevel.value = level.id;
    // Reset do curso quando troca de nível
    currentCourse.value = null;
};

const selectCourse = (course) => {
    currentCourse.value = course.id;
};

// Computed properties atualizadas
const selectedLevel = computed(() => {
    return disciplineStructure.value?.levels?.find(
        (l) => l.id === currentLevel.value
    );
});

const selectedCourse = computed(() => {
    return disciplineStructure.value?.courses?.find(
        (c) => c.id === currentCourse.value
    );
});

const courseDisciplines = computed(() => {
    if (!selectedCourse.value) return [];
    return selectedCourse.value.disciplines;
});

const filteredDisciplines = computed(() => {
    if (!courseDisciplines.value) return [];

    return courseDisciplines.value.filter((discipline) =>
        discipline.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const levelDisciplines = computed(() => {
    if (!selectedLevel.value) return [];
    return selectedLevel.value.disciplines;
});

const canSave = computed(() => {
    return (
        currentLevel.value &&
        currentCourse.value &&
        levelDisciplines.value.length > 0
    );
});

// Métodos existentes atualizados
const handleLevelChange = () => {
    if (!currentLevel.value) return;
};

const handleCourseChange = () => {
    if (!currentCourse.value) return;
};

const isSelected = (disciplineId) => {
    return levelDisciplines.value.some((d) => d.id === disciplineId);
};

const addDiscipline = async (discipline) => {
    if (isSelected(discipline.id)) return;

    try {
        await handleSubmit({
            level_id: currentLevel.value,
            discipline_ids: [
                ...levelDisciplines.value.map((d) => d.id),
                discipline.id,
            ],
            course_id: currentCourse.value,
        });
        await fetchDisciplineStructure();
    } catch (error) {
        console.error("Erro ao adicionar disciplina:", error);
    }
};

const removeDiscipline = async (discipline) => {
    if (!currentLevel.value || !discipline.id) {
        console.error("Dados inválidos para remoção");
        return;
    }

    try {
        await removeDisciplineFromLevel(currentLevel.value, discipline.id);
    } catch (error) {
        console.error("Erro ao remover disciplina:", error);
    }
};

const handleNewDiscipline = async () => {
    try {
        const discipline = await createDiscipline(newDiscipline.value);
        showDisciplineModal.value = false;
        newDiscipline.value = { name: "", code: "" };
        await fetchDisciplineStructure();
    } catch (error) {
        console.error("Erro ao criar disciplina:", error);
    }
};

const handleSubmitForm = () => {
    handleSubmit({
        level_id: currentLevel.value,
        discipline_ids: levelDisciplines.value.map((d) => d.id),
        course_id: currentCourse.value,
    });
};

onMounted(async () => {
    await fetchDisciplineStructure();
});
</script>

<style scoped>
.wizard-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.step {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child):after {
    content: "";
    position: absolute;
    top: 50%;
    left: calc(50% + 2rem);
    right: calc(-50% + 2rem);
    height: 2px;
    background-color: #e9ecef;
    z-index: 1;
}

.step.completed:after {
    background-color: var(--bs-primary);
}

.step-number {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 1rem;
    z-index: 2;
}

.step.active .step-number {
    background-color: var(--bs-primary);
    color: white;
}

.step.completed .step-number {
    background-color: var(--bs-success);
    color: white;
}

.step-title {
    font-weight: 500;
}

.level-grid,
.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.level-card,
.course-card {
    padding: 1.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white;
}

.level-card:hover,
.course-card:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transform: translateY(-2px);
}

.has-disciplines {
    border-left: 4px solid var(--bs-success);
}

.level-info h4,
.course-info h4 {
    margin: 0;
    font-size: 1.1rem;
}

.level-info p,
.course-info p {
    margin: 0.5rem 0 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.selected-context {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.search-box {
    flex: 1;
    max-width: 400px;
}

.disciplines-management {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 1rem;
}

.available-disciplines,
.selected-disciplines {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    border: 1px solid #dee2e6;
}

.list-title {
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.discipline-list {
    max-height: 500px;
    overflow-y: auto;
}

.discipline-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.discipline-item:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.drag-handle {
    cursor: move;
    padding: 0 0.5rem;
    color: #6c757d;
}

.discipline-name {
    flex: 1;
    margin: 0 0.5rem;
}

.btn-remove {
    border: none;
    background: none;
    color: #dc3545;
    padding: 0.25rem;
    opacity: 0.5;
    transition: opacity 0.2s;
}

.btn-remove:hover {
    opacity: 1;
}

.discipline-action {
    width: 24px;
    text-align: center;
}
</style>
