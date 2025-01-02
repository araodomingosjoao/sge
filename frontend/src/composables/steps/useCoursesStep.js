import { ref } from 'vue'
import { useSetupStore } from '@/stores/setupStore'
import { useNotification } from '@/composables/useNotification'
import axios from '@/plugins/axios'

export function useCoursesStep() {
    const setupStore = useSetupStore()
    const { showSuccess, showError } = useNotification()
    
    // Estado
    const loading = ref(false)
    const courseStructure = ref(null)

    // Busca estrutura completa (igual ao endpoint de disciplines que já retorna cursos)
    const fetchCourseStructure = async () => {
        try {
            loading.value = true
            const { data } = await axios.get('/disciplines')
            courseStructure.value = data.data
        } catch (error) {
            console.error('Erro ao carregar estrutura:', error)
            showError('Não foi possível carregar os dados')
            throw error
        } finally {
            loading.value = false
        }
    }

    // Associa disciplinas a um curso
    const associateDisciplinesToCourse = async (courseId, disciplineIds, levelIds) => {
        try {
            loading.value = true
            const { data } = await axios.post(`/disciplines/courses/${courseId}/batch`, {
                discipline_ids: disciplineIds,
                level_ids: levelIds
            })
            showSuccess('Disciplinas associadas com sucesso')
            return data
        } catch (error) {
            console.error('Erro ao associar disciplinas:', error)
            showError('Falha ao associar disciplinas')
            throw error
        } finally {
            loading.value = false
        }
    }

    // Remove disciplina do curso
    const removeDisciplineFromCourse = async (courseId, disciplineId) => {
        try {
            loading.value = true
            await axios.delete(`/disciplines/courses/${courseId}/${disciplineId}`)
            showSuccess('Disciplina removida com sucesso')
        } catch (error) {
            console.error('Erro ao remover disciplina:', error)
            showError('Falha ao remover disciplina')
            throw error
        } finally {
            loading.value = false
        }
    }

    // Handler principal do step
    const handleSubmit = async (formData) => {
        if (!formData?.course_id || !formData?.discipline_ids || !formData?.level_ids) {
            showError('Dados incompletos para submissão')
            return false
        }

        try {
            setupStore.setLoading(true)
            
            // Associa disciplinas ao curso e níveis
            await associateDisciplinesToCourse(
                formData.course_id,
                formData.discipline_ids,
                formData.level_ids
            )
            
            // Atualiza o store de setup
            await setupStore.saveStepData('courses', {
                course_id: formData.course_id,
                total_disciplines: formData.discipline_ids.length,
                total_levels: formData.level_ids.length
            })

            await fetchCourseStructure()
            
            showSuccess('Configuração salva com sucesso')
            return true
        } catch (error) {
            console.error('Erro ao salvar configuração:', error)
            showError('Falha ao salvar configuração')
            return false
        } finally {
            setupStore.setLoading(false)
        }
    }

    return {
        // Estado
        courseStructure,
        loading,

        // Métodos
        fetchCourseStructure,
        associateDisciplinesToCourse,
        removeDisciplineFromCourse,
        handleSubmit
    }
}