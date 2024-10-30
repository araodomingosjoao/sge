import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Alunos from '../views/Alunos.vue';
import ListarAlunos from '../views/alunos/Listar.vue';
import Matriculas from '../views/alunos/Matriculas.vue';

const routes = [
  { path: '/dashboard', component: Dashboard },
  { 
    path: '/alunos', 
    component: Alunos,
    children: [
      { path: '', component: ListarAlunos },
      { path: 'matriculas', component: Matriculas },
    ],
  },
  // Defina as demais rotas aqui
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
