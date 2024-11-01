import axios from 'axios';
import { storage } from '../utils/storage';
import router from '../router';

const instance = axios.create({
    baseURL: 'http://localhost:8000/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
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
            storage.remove('access_token');
            router.push({ name: 'Login' });
        }
        return Promise.reject(error);
    }
);

export default instance;
