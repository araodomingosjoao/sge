export default [
    {
        label: 'Dashboard',
        icon: 'bx bxs-dashboard',
        link: '/dashboard',
    },
    {
        label: 'Alunos',
        icon: 'bx bx-user',
        submenu: [
            { label: 'Listar Alunos', link: '/alunos' },
            { label: 'Matrículas', link: '/alunos/matriculas' },
            { label: 'Histórico', link: '/alunos/historico' },
        ],
    },
    // {
    //     label: 'Professores',
    //     icon: 'bx bx-chalkboard',
    //     link: '/professores',
    // },
    // {
    //     label: 'Turmas',
    //     icon: 'bx bx-group',
    //     link: '/turmas',
    // },
    // {
    //     label: 'Disciplinas',
    //     icon: 'bx bx-book',
    //     link: '/disciplinas',
    // },
    // {
    //     label: 'Horários',
    //     icon: 'bx bx-time',
    //     link: '/horarios',
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
