/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import { useAuthStore } from './store/auth';

// Базовый URL для API (если нужно)
// axios.defaults.baseURL = import.meta.env.VITE_APP_URL || '/';

// CSRF-токен для защиты от межсайтовой подделки запросов
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Всегда отправляем JSON
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// --- Перехватчики (Interceptors) ---

// Добавляем токен авторизации в каждый запрос (если он есть)
axios.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore();
        if (authStore.token) {
            config.headers.Authorization = `Bearer ${authStore.token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Обрабатываем ошибку 401 (неавторизован)
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const authStore = useAuthStore();

        // Если получили 401 и у нас есть токен — значит, токен протух
        if (error.response?.status === 401 && authStore.token) {
            // Выходим из системы
            authStore.logout();

            // Если мы не на странице логина — редиректим
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }

        return Promise.reject(error);
    }
);

// Делаем axios глобально доступным
window.axios = axios;

// Экспортируем axios для использования в компонентах
export { axios };