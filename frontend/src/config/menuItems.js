export default [
    {
        label: 'Dashboard',
        icon: 'bx bxs-dashboard',
        link: '/dashboard',
    },
    {
        label: 'Gestão Acadêmica',
        icon: 'bx bx-book',
        submenu: [
            {
                label: 'Alunos',
                id: 'sidebarAlunos',
                submenu: [
                    { label: 'Alunos', link: '/alunos' },
                    { label: 'Matrículas', link: '/alunos/matriculas' },
                    { label: 'Histórico', link: '/alunos/historico' },
                ],
            },
            // {
            //     label: 'Professores',
            //     id: 'sidebarProfessores',
            //     submenu: [
            //         { label: 'Professores', link: '/professores' },
            //     ],
            // },
            // {
            //     label: 'Turmas',
            //     id: 'sidebarTurmas',
            //     link: '/turmas',
            // },
            // {
            //     label: 'Disciplinas',
            //     id: 'sidebarDisciplinas',
            //     link: '/disciplinas',
            // },
            // {
            //     label: 'Horários',
            //     icon: 'bx bx-time',
            //     link: '/horarios',
            // },
        ],
    },
    // {
    //     label: 'Finanças',
    //     icon: 'bx bx-money',
    //     submenu: [
    //         { label: 'Receitas', link: '/financas/receitas' },
    //         { label: 'Despesas', link: '/financas/despesas' },
    //         { label: 'Relatórios Financeiros', link: '/financas/relatorios' },
    //     ],
    // },
    // {
    //     label: 'Relatórios',
    //     icon: 'bx bx-chart',
    //     submenu: [
    //         { label: 'Relatório Acadêmico', link: '/relatorios/academico' },
    //         { label: 'Relatório Financeiro', link: '/relatorios/financeiro' },
    //     ],
    // },
    // {
    //     label: 'Configurações',
    //     icon: 'bx bx-cog',
    //     submenu: [
    //         { label: 'Usuários', link: '/configuracoes/usuarios' },
    //         { label: 'Permissões', link: '/configuracoes/permissoes' },
    //     ],
    // },
];
