export default [
    {
        path: '/alunos',
        name: 'Alunos',
        component: () => import('@/views/alunos/AlunosList.vue'),
        meta: { requiresAuth: true, layout: 'MainLayout' },
    },
    {
        path: '/alunos/matriculas',
        name: 'Matriculas',
        component: () => import('@/views/alunos/Matriculas.vue'),
        meta: { requiresAuth: true, layout: 'MainLayout' },
    },
    {
        path: '/alunos/historico',
        name: 'Historico',
        component: () => import('@/views/alunos/Historico.vue'),
        meta: { requiresAuth: true, layout: 'MainLayout' },
    },
];
