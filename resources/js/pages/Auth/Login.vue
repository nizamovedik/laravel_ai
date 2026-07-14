<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-blue-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md border border-gray-100/80">
      <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-2 mb-2">
          <ClipboardIcon class="w-10 h-10 text-indigo-600" />
          <span class="text-2xl font-bold text-gray-800">TaskManager</span>
        </div>
        <p class="text-gray-500 text-sm">Войдите в свой аккаунт</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            placeholder="example@mail.com"
            required
          />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            placeholder="••••••••"
            required
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="btn btn-primary w-full inline-flex items-center justify-center space-x-1"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span>{{ isLoading ? 'Вход...' : 'Войти' }}</span>
        </button>

        <p v-if="error" class="mt-4 text-sm text-red-500 text-center">{{ error }}</p>
      </form>

      <p class="mt-6 text-center text-sm text-gray-400">
        Нет аккаунта?
        <router-link to="/register" class="text-indigo-600 hover:text-indigo-800 font-medium transition">
          Зарегистрироваться
        </router-link>
      </p>

      <p class="mt-2 text-center text-xs text-gray-400">
        Демо: admin@test.com / password
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../store/auth';
import { ClipboardIcon } from '../../components/icons';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  email: 'admin@test.com',
  password: 'password',
});
const error = ref('');
const isLoading = ref(false);

const handleLogin = async () => {
  error.value = '';
  isLoading.value = true;

  try {
    await authStore.login(form.value.email, form.value.password);
    await router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || 'Ошибка входа. Проверьте email и пароль.';
  } finally {
    isLoading.value = false;
  }
};
</script>