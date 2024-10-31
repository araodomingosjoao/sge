import axios from 'axios';
import { storage } from '../utils/storage';
import router from '../router';
import { useAuth } from '../composables/useAuth';

const { logout } = useAuth();

const instance = axios.create({
    baseURL: 'http://localhost:8000/api',
});

instance.interceptors.request.use((config) => {
    const token = storage.get('access_token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

instance.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            logout();
            router.push({ name: 'Login' });
        }
        return Promise.reject(error);
    }
);

const expirationTime = 60 * 30000;
setTimeout(() => {
    logout();
    router.push({ name: 'Login' });
}, expirationTime);

export default instance;
