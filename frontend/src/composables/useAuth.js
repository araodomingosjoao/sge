import { ref } from 'vue';
import axios from '../plugins/axios';
import { storage } from '../utils/storage';

const token = ref(storage.get('access_token'));

export function useAuth() {
    const login = async (email, password) => {
        try {
            const response = await axios.post('/auth/login', { email, password });
            token.value = response.data.access_token;
            storage.set('access_token', token.value);
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Erro ao fazer login');
        }
    };

    const logout = () => {
        token.value = null;
        storage.remove('access_token');
    };

    const isAuthenticated = () => !!token.value;

    return { token, login, logout, isAuthenticated };
}
