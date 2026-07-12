<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <form @submit.prevent="handleLogin" class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6">Вход</h1>

            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" class="w-full border rounded px-3 py-2" required />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium">Пароль</label>
                <input v-model="form.password" type="password" class="w-full border rounded px-3 py-2" required />
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Войти
            </button>

            <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({ email: 'admin@test.com', password: 'password' });
const error = ref('');

const handleLogin = async () => {
    try {
        await authStore.login(form.value.email, form.value.password);
        router.push('/');
    } catch (e) {
        error.value = e.response?.data?.message || 'Ошибка входа';
    }
};
</script>