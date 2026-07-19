<template>
  <header class="bg-white border-b border-gray-200 h-16 flex items-center px-6 sticky top-0 z-10">
    <!-- Логотип -->
    <router-link to="/" class="flex items-center space-x-2 text-gray-800 hover:text-indigo-600 transition">
      <ClipboardIcon class="w-8 h-8 text-indigo-600" />
      <span class="text-xl font-bold">TaskManager</span>
    </router-link>

    <div class="flex-1 flex justify-center">
      <!-- Пока пусто -->
    </div>

    <div class="flex items-center space-x-4">
      <div class="flex items-center space-x-2">
        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-medium text-indigo-700 border-2 border-white shadow-sm">
          {{ userInitials }}
        </div>
        <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ userName }}</span>
      </div>

      <button @click="logout" class="btn btn-secondary btn-sm flex items-center space-x-1">
        <ArrowRightOnRectangleIcon class="w-4 h-4" />
        <span>Выйти</span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';
import { ClipboardIcon, ArrowRightOnRectangleIcon } from '../components/icons';

const router = useRouter();
const authStore = useAuthStore();

const userName = computed(() => authStore.userName);
const userInitials = computed(() => {
  const name = authStore.user?.name || '';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
});

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>