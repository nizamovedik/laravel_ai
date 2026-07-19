<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center space-x-3 mb-6">
        <button @click="router.back()" class="text-gray-400 hover:text-gray-600 transition">
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Создание задачи</h1>
      </div>

      <form @submit.prevent="handleSubmit" class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-5">
        <!-- Проект -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Проект *</label>
          <select
            v-model="form.project_id"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
            :class="{ 'border-red-400 ring-1 ring-red-400': errors.project_id }"
          >
            <option :value="null">Выберите проект</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
          <p v-if="errors.project_id" class="mt-1 text-sm text-red-500">{{ errors.project_id }}</p>
        </div>

        <!-- Название -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            :class="{ 'border-red-400 ring-1 ring-red-400': errors.title }"
            placeholder="Введите название задачи"
          />
          <p v-if="errors.title" class="mt-1 text-sm text-red-500">{{ errors.title }}</p>
        </div>

        <!-- Описание -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
            :class="{ 'border-red-400 ring-1 ring-red-400': errors.description }"
            placeholder="Опишите задачу (необязательно)"
          />
          <p v-if="errors.description" class="mt-1 text-sm text-red-500">{{ errors.description }}</p>
        </div>

        <!-- Исполнитель -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Исполнитель</label>
          <select
            v-model="form.assignee_id"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
          >
            <option :value="null">Не назначен</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>

        <!-- Приоритет -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
          <select
            v-model="form.priority_id"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
          >
            <option :value="null">Не указан</option>
            <option v-for="priority in priorities" 
              :key="priority.id" 
              :value="priority.id"  
              :style="{ backgroundColor: priority.color || '#e5e7eb', color: '#1f2937' }">
              {{ priority.name }}
            </option>
          </select>
        </div>

        <!-- Теги -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Теги</label>
          <select
            v-model="form.label_ids"
            multiple
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white min-h-[80px]"
          >
            <option v-for="label in labels" :key="label.id" :value="label.id">
              {{ label.name }}
            </option>
          </select>
          <p class="mt-1 text-xs text-gray-400">Удерживайте Ctrl (Cmd) для выбора нескольких тегов</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Дедлайн -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Дедлайн</label>
            <input
              v-model="form.deadline_at"
              type="date"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <p v-if="errors.deadline_at" class="mt-1 text-sm text-red-500">{{ errors.deadline_at }}</p>
          </div>

          <!-- Оценка времени -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Оценка (часы)</label>
            <input
              v-model="form.estimated_hours"
              type="number"
              step="0.5"
              min="0"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
              placeholder="Например: 4.5"
            />
            <p v-if="errors.estimated_hours" class="mt-1 text-sm text-red-500">{{ errors.estimated_hours }}</p>
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
            <span>{{ isSubmitting ? 'Создание...' : 'Создать задачу' }}</span>
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

// ✅ Если передан project_id в query (?project_id=1)
const projectIdFromQuery = route.query.project_id || null;

const form = ref({
  project_id: projectIdFromQuery,
  title: '',
  description: '',
  assignee_id: null,
  priority_id: null,
  label_ids: [],
  deadline_at: '',
  estimated_hours: null,
});

const projects = ref([]);
const users = ref([]);
const priorities = ref([]);
const labels = ref([]);
const errors = ref({});
const error = ref('');
const isSubmitting = ref(false);

const loadProjects = async () => {
  try {
    const res = await axios.get('/api/projects');
    projects.value = res.data.data;
  } catch (e) {
    console.error('Ошибка загрузки проектов', e);
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

const loadPriorities = async () => {
  try {
    const res = await axios.get('/api/task-priorities');
    priorities.value = res.data.data || res.data;
  } catch (e) {
    console.error('Ошибка загрузки приоритетов', e);
  }
};

const loadLabels = async () => {
  try {
    const res = await axios.get('/api/task-labels');
    labels.value = res.data.data || res.data;
  } catch (e) {
    console.error('Ошибка загрузки тегов', e);
  }
};

const handleSubmit = async () => {
  errors.value = {};
  error.value = '';
  isSubmitting.value = true;

  const payload = {
    project_id: form.value.project_id,
    title: form.value.title,
    description: form.value.description || null,
    assignee_id: form.value.assignee_id || null,
    priority_id: form.value.priority_id || null,
    label_ids: form.value.label_ids || [],
    deadline_at: form.value.deadline_at || null,
    estimated_hours: form.value.estimated_hours || null,
  };

  try {
    await axios.post('/api/tasks', payload);
    await router.push('/tasks');
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      error.value = err.response.data.message || 'Ошибка валидации';
    } else {
      error.value = 'Произошла ошибка при создании задачи';
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  loadProjects();
  loadUsers();
  loadPriorities();
  loadLabels();
});
</script>