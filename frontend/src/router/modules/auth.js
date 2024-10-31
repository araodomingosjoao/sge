export default [
    {
        path: '/auth/login',
        name: 'Login',
        component: () => import('@/views/auth/Login.vue'),
        meta: { layout: 'AuthLayout' },
    },
];
