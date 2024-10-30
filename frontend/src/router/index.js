import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "../composables/useAuth";
import AlunosRoutes from "./modules/alunos";
import AuthRoutes from "./modules/auth";

const routes = [
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: import("@/views/NotFound.vue"),
        meta: { layout: 'ErrorLayout' },
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: () => import("@/views/Dashboard.vue"),
        meta: { requiresAuth: true },
    },
    ...AuthRoutes,
    ...AlunosRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const { isAuthenticated, logout } = useAuth();

router.beforeEach((to, from, next) => {
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!isAuthenticated()) {
            logout();
            next({ name: "Login" });
        } else {
            next();
        }
    } else {
        next();
    }
});
export default router;
