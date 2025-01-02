import { push } from 'notivue'

export function useNotification() {

    const showSuccess = (message) => {
        push.success({
            title: 'Sucesso',
            text: message
        })
    }

    const showError = (message) => {
        push.error({
            title: 'Erro',
            text: message,
        })
    }

    const showWarning = (message) => {
        push.warning({
            title: 'Atenção',
            text: message
        })
    }

    const showInfo = (message) => {
        push({
            title: 'Informação',
            text: message
        })
    }

    const showLoading = (message = 'Carregando...') => {
        return push.load({
            title: 'Processando',
            text: message,
        })
    }

    // Helper para mostrar erro da API
    const showApiError = (error) => {
        const message = error.response?.data?.message || 
                       'Ocorreu um erro inesperado. Tente novamente.'
        showError(message)
    }

    return {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showLoading,
        showApiError
    }
}