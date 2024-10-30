import { createRouter, createWebHistory } from 'vue-router';
import AlunosRoutes from './modules/alunos';

const routes = [
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
    ...AlunosRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
