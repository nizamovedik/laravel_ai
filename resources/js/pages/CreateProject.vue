<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center space-x-3 mb-6">
        <button @click="router.back()" class="text-gray-400 hover:text-gray-600 transition">
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Создание проекта</h1>
      </div>

      <form @submit.prevent="handleSubmit" class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-5">
        <!-- Название -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            :class="{ 'border-red-400 ring-1 ring-red-400': errors.name }"
            placeholder="Введите название проекта"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-500">{{ errors.name }}</p>
        </div>

        <!-- Описание -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
            :class="{ 'border-red-400 ring-1 ring-red-400': errors.description }"
            placeholder="Опишите проект (необязательно)"
          />
          <p v-if="errors.description" class="mt-1 text-sm text-red-500">{{ errors.description }}</p>
        </div>

        <!-- Тимлид -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Тимлид</label>
          <select
            v-model="form.team_lead_id"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
          >
            <option :value="null">Не выбран</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Дата начала -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Дата начала</label>
            <input
              v-model="form.started_at"
              type="date"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
          </div>

          <!-- Дата окончания -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Дата окончания</label>
            <input
              v-model="form.deadline_at"
              type="date"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <p v-if="errors.deadline_at" class="mt-1 text-sm text-red-500">{{ errors.deadline_at }}</p>
          </div>
        </div>

        <!-- Кнопки -->
        <div class="flex items-center space-x-3 pt-2">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="btn-primary flex items-center space-x-1"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ isSubmitting ? 'Создание...' : 'Создать проект' }}</span>
          </button>
          <button
            type="button"
            @click="router.back()"
            class="btn-secondary"
          >
            Отмена
          </button>
        </div>

        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import { ArrowLeftIcon } from '../components/icons';

const router = useRouter();

const form = ref({
  name: '',
  description: '',
  team_lead_id: null,
  started_at: '',
  deadline_at: '',
});

const users = ref([]);
const errors = ref({});
const error = ref('');
const isSubmitting = ref(false);

const loadUsers = async () => {
  try {
    const res = await axios.get('/api/users');
    users.value = res.data;
  } catch (e) {
    console.error('Ошибка загрузки пользователей', e);
  }
};

const handleSubmit = async () => {
  errors.value = {};
  error.value = '';
  isSubmitting.value = true;

  try {
    await axios.post('/api/projects', form.value);
    await router.push('/projects');
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      error.value = err.response.data.message || 'Ошибка валидации';
    } else {
      error.value = 'Произошла ошибка при создании проекта';
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  loadUsers();
});
</script>