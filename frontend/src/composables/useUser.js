import axios from '../plugins/axios';
import { useUserStore } from '../stores/userStore';

export function useUser() {
    const userStore = useUserStore();

    const fetchUser = async () => {
        try {
            const response = await axios.get('/user/profile');
            const { user, permissions, role, setup_required }  = response.data;
            userStore.setUser({ user, permissions, role, setup_required });
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Erro ao recuperar os dados do usuário:');
        }
    };

    return { fetchUser };
}
