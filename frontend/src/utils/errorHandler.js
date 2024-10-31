export const errorHandler = (error) => {
    return error.response?.data?.message || 'Erro desconhecido';
};
