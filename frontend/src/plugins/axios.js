import axios from 'axios';
import { storage } from '../utils/storage';

const instance = axios.create({
    baseURL: 'http://localhost:8000/api',
});

instance.interceptors.request.use((config) => {
    const token = storage.get('auth_token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

export default instance;
