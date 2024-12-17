// src/composables/useCepSearch.js
export function useCepSearch() {
    const searchCep = async (cep) => {
      try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
        if (!response.ok) throw new Error('CEP não encontrado')
        return await response.json()
      } catch (error) {
        console.error('Erro ao buscar CEP:', error)
        throw error
      }
    }
  
    return {
      searchCep
    }
  }