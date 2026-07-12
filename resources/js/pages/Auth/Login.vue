<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <div class="text-4xl mb-2">📋</div>
                <h1 class="text-2xl font-bold text-gray-800">TaskManager</h1>
                <p class="text-gray-500 text-sm">Войдите в свой аккаунт</p>
            </div>

            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="example@mail.com"
                        required
                    />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition shadow-md"
                >
                    Войти
                </button>

                <p v-if="error" class="mt-4 text-red-500 text-sm text-center">{{ error }}</p>
            </form>

            <p class="mt-6 text-center text-sm text-gray-400">
                Демо: admin@test.com / password
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../store/auth';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
    email: 'admin@test.com',
    password: 'password',
});
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