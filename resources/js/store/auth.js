import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user?.name || 'Гость',
    },

    actions: {
        async login(email, password) {
            try {
                const response = await axios.post('/api/login', { email, password });

                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));

                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            } catch (error) {
                console.error('Ошибка входа:', error);
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Ошибка выхода:', error);
            } finally {
                this.token = null;
                this.user = null;

                localStorage.removeItem('token');
                localStorage.removeItem('user');

                delete axios.defaults.headers.common['Authorization'];
            }
        },

        restoreSession() {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));

            if (token && user) {
                this.token = token;
                this.user = user;
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                return true;
            }

            return false;
        },

        updateUser(userData) {
            this.user = { ...this.user, ...userData };
            localStorage.setItem('user', JSON.stringify(this.user));
        },

        clearUser() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
        },
    },
});