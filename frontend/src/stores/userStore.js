import { defineStore } from 'pinia';

export const useUserStore = defineStore({
    id: 'user',
    state: () => ({
        user: null,
        permissions: [],
        role: '',
        setup_required: false
    }),
    getters: {
        isAdmin: (state) => state.role  === 'school_admin',
    },
    actions: {
        setUser(data) {
            this.user = data.user;
            this.permissions = data.permissions;
            this.role = data.role;
            this.setup_required = data.setup_required;
        },
        clearUser() {
            this.user = null;
            this.permissions = [];
            this.role = '';
        },
    },
    persist: {
        enabled: true,
        strategies: [
            { storage: localStorage, paths: ['user', 'permissions', 'role', 'setup_required'] }
        ],
    },
});
