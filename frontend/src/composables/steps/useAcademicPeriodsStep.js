import { ref } from 'vue'
import { useSetupStore } from '@/stores/setupStore'
import axios from '@/plugins/axios'

export function useAcademicPeriodsStep() {
    const setupStore = useSetupStore()
    const academicYears = ref([])
    const currentYear = ref(null)

    const fetchAcademicYears = async () => {
        try {
            setupStore.setLoading(true)
            const { data } = await axios.get('/academic-years')
            academicYears.value = data.data
            if (data.data.length > 0) {
                currentYear.value = data.data[0]
            }
        } catch (error) {
            console.error('Erro ao carregar anos letivos:', error)
        } finally {
            setupStore.setLoading(false)
        }
    }

    const updateAcademicYear = async (yearId, yearData) => {
        try {
            setupStore.setLoading(true)
            const { data } = await axios.put(`/academic-years/${yearId}`, yearData)
            // Atualiza o ano na lista
            const index = academicYears.value.findIndex(year => year.id === yearId)
            if (index !== -1) {
                academicYears.value[index] = { ...academicYears.value[index], ...data.data }
            }
            return data
        } catch (error) {
            console.error('Erro ao atualizar ano letivo:', error)
            throw error
        } finally {
            setupStore.setLoading(false)
        }
    }

    const updateTrimesters = async (yearId, trimestersData) => {
        try {
            setupStore.setLoading(true)
            const { data } = await axios.put(
                `/academic-years/${yearId}/trimesters`, 
                { trimesters: trimestersData }
            )
            // Atualiza os trimestres do ano
            const yearIndex = academicYears.value.findIndex(year => year.id === yearId)
            if (yearIndex !== -1) {
                academicYears.value[yearIndex].trimesters = data.data
            }
            return data
        } catch (error) {
            console.error('Erro ao atualizar trimestres:', error)
            throw error
        } finally {
            setupStore.setLoading(false)
        }
    }

    const handleSubmit = async (formData) => {
        if (!currentYear.value || !formData) {
            console.error('Dados inválidos para submissão')
            return false
        }

        try {
            setupStore.setLoading(true)
            
            // Atualiza ano letivo
            await updateAcademicYear(currentYear.value.id, {
                start_date: formData.start_date,
                end_date: formData.end_date,
                status: formData.status
            })

            // Atualiza trimestres
            await updateTrimesters(currentYear.value.id, formData.trimesters)

            // Salva no store de setup e avança
            await setupStore.saveStepData('academic_periods', formData)
            await setupStore.moveToNextStep()
            
            return true
        } catch (error) {
            console.error('Erro ao salvar períodos acadêmicos:', error)
            return false
        } finally {
            setupStore.setLoading(false)
        }
    }

    return {
        academicYears,
        currentYear,
        fetchAcademicYears,
        handleSubmit
    }
}