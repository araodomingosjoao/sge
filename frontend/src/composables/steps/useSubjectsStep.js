import { ref } from "vue";
import { useSetupStore } from "@/stores/setupStore";
import { useNotification } from "@/composables/useNotification";
import axios from "@/plugins/axios";

export function useSubjectsStep() {
    const setupStore = useSetupStore();
    const { showSuccess, showError } = useNotification();

    const disciplineStructure = ref(null);
    const loading = ref(false);

    /**
     * Busca a estrutura completa de disciplinas
     * Inclui níveis, cursos e suas disciplinas associadas
     */
    const fetchDisciplineStructure = async () => {
        try {
            loading.value = true;
            const { data } = await axios.get("/disciplines");
            disciplineStructure.value = data.data;
        } catch (error) {
            console.error("Erro ao carregar estrutura de disciplinas:", error);
            showError("Não foi possível carregar as disciplinas");
            throw error;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Associa disciplinas a um nível específico
     */
    const associateWithLevel = async (
        levelId,
        disciplineIds,
        courseId = null
    ) => {
        try {
            loading.value = true;
            const { data } = await axios.post(
                `/disciplines/by-level/${levelId}/batch`,
                {
                    discipline_ids: disciplineIds,
                    course_id: courseId,
                }
            );
            showSuccess("Disciplinas associadas com sucesso");
            return data;
        } catch (error) {
            console.error("Erro ao associar disciplinas:", error);
            showError("Falha ao associar disciplinas");
            throw error;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Remove uma disciplina de um nível
     */
    const removeDisciplineFromLevel = async (levelId, disciplineId) => {
        try {
            loading.value = true
            // Usa o endpoint específico para remover
            await axios.delete(`/disciplines/by-level/${levelId}/${disciplineId}`)
            showSuccess("Disciplina removida com sucesso")
            
            // Atualiza a estrutura após remover
            await fetchDisciplineStructure()
        } catch (error) {
            console.error("Erro ao remover disciplina:", error)
            showError("Falha ao remover disciplina")
            throw error
        } finally {
            loading.value = false
        }
    }

    /**
     * Busca disciplinas por curso
     */
    const getDisciplinesByCourse = async (courseId) => {
        try {
            loading.value = true;
            const { data } = await axios.get(
                `/disciplines/by-course/${courseId}`
            );
            return data.data;
        } catch (error) {
            console.error("Erro ao buscar disciplinas do curso:", error);
            showError("Não foi possível carregar as disciplinas do curso");
            throw error;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Busca disciplinas por nível
     */
    const getDisciplinesByLevel = async (levelId) => {
        try {
            loading.value = true;
            const { data } = await axios.get(
                `/disciplines/by-level/${levelId}`
            );
            return data.data;
        } catch (error) {
            console.error("Erro ao buscar disciplinas do nível:", error);
            showError("Não foi possível carregar as disciplinas do nível");
            throw error;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Cria uma nova disciplina
     */
    const createDiscipline = async (disciplineData) => {
        try {
            loading.value = true;
            const { data } = await axios.post(
                "/disciplines/all",
                disciplineData
            );
            showSuccess("Disciplina criada com sucesso");
            return data.data;
        } catch (error) {
            console.error("Erro ao criar disciplina:", error);
            showError("Falha ao criar disciplina");
            throw error;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Handler principal para salvar o step
     */
    // No useSubjectsStep.js
    const handleSubmit = async (formData) => {
        // Verifica se temos os dados necessários
        if (!formData?.level_id || !formData?.discipline_ids) {
            showError("Dados incompletos para submissão");
            return false;
        }

        try {
            setupStore.setLoading(true);

            // Associa as disciplinas ao nível/curso
            await associateWithLevel(
                formData.level_id,
                formData.discipline_ids,
                formData.course_id
            );

            // Atualiza o store de setup
            await setupStore.saveStepData("subjects", {
                level_id: formData.level_id,
                course_id: formData.course_id,
                total_disciplines: formData.discipline_ids.length,
            });

            await fetchDisciplineStructure();
            return true;
        } catch (error) {
            console.error(
                "Erro ao salvar configuração das disciplinas:",
                error
            );
            showError("Falha ao salvar configuração");
            return false;
        } finally {
            setupStore.setLoading(false);
        }
    };

    // No SubjectsStep.vue, ajuste a função removeDiscipline:
    const removeDiscipline = async (discipline) => {
        if (!currentLevel.value || !discipline.id) {
            console.error("Dados inválidos para remoção");
            return;
        }

        try {
            const updatedDisciplineIds = levelDisciplines.value
                .filter((d) => d.id !== discipline.id)
                .map((d) => d.id);

            await handleSubmit({
                level_id: currentLevel.value,
                discipline_ids: updatedDisciplineIds,
                course_id: currentCourse.value,
            });
        } catch (error) {
            console.error("Erro ao remover disciplina:", error);
        }
    };

    return {
        // Estado
        disciplineStructure,
        loading,

        // Métodos de API
        fetchDisciplineStructure,
        getDisciplinesByCourse,
        getDisciplinesByLevel,
        createDiscipline,
        associateWithLevel,
        removeDisciplineFromLevel,

        // Handlers
        handleSubmit,
    };
}
