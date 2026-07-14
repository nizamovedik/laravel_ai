<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center space-x-3 mb-6">
        <button @click="router.back()" class="text-gray-400 hover:text-gray-600 transition">
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Редактирование проекта</h1>
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
            class="btn btn-primary inline-flex items-center space-x-1"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ isSubmitting ? 'Сохранение...' : 'Сохранить изменения' }}</span>
          </button>
          <button
            type="button"
            @click="router.back()"
            class="btn btn-secondary"
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
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import { ArrowLeftIcon } from '../components/icons';

const router = useRouter();
const route = useRoute();
const projectId = route.params.id;

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

// ✅ Хелпер для форматирования даты
const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  try {
    const date = new Date(dateString);
    // Проверяем, что дата валидна
    if (isNaN(date.getTime())) return '';
    return date.toISOString().split('T')[0];
  } catch {
    return '';
  }
};

const loadUsers = async () => {
  try {
    const res = await axios.get('/api/users');
    users.value = res.data;
  } catch (e) {
    console.error('Ошибка загрузки пользователей', e);
  }
};

const loadProject = async () => {
  try {
    const res = await axios.get(`/api/projects/${projectId}`);
    const project = res.data.data;
    
    form.value = {
      name: project.name || '',
      description: project.description || '',
      team_lead_id: project.team_lead?.id || null,
      started_at: formatDateForInput(project.started_at),
      deadline_at: formatDateForInput(project.deadline_at),
    };
  } catch (e) {
    console.error('Ошибка загрузки проекта', e);
    error.value = 'Не удалось загрузить данные проекта';
  }
};

const handleSubmit = async () => {
  errors.value = {};
  error.value = '';
  isSubmitting.value = true;

  // Формируем payload без пустых значений
  const payload = {};
  if (form.value.name) payload.name = form.value.name;
  if (form.value.description) payload.description = form.value.description;
  if (form.value.team_lead_id) payload.team_lead_id = form.value.team_lead_id;
  if (form.value.started_at) payload.started_at = form.value.started_at;
  if (form.value.deadline_at) payload.deadline_at = form.value.deadline_at;

  try {
    await axios.put(`/api/projects/${projectId}`, payload);
    await router.push(`/projects/${projectId}`);
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      error.value = err.response.data.message || 'Ошибка валидации';
    } else if (err.response?.status === 403) {
      error.value = 'У вас нет прав на редактирование этого проекта';
    } else {
      error.value = 'Произошла ошибка при обновлении проекта';
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  loadUsers();
  loadProject();
});
</script>