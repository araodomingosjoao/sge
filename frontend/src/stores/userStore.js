import { defineStore } from 'pinia';

export const useUserStore = defineStore({
    id: 'user',
    state: () => ({
        user: null,
        permissions: [],
        role: '',
    }),
    getters: {
        isAdmin: (state) => state.role  === 'school_admin',
    },
    actions: {
        setUser(data) {
            this.user = data.user;
            this.permissions = data.permissions;
            this.role = data.role;
        },
        clearUser() {
            this.user = null;
            this.permissions = [];
            this.role = '';
        },
    },
});
