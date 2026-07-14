<template>
  <AuthenticatedLayout>
    <Toast :message="toastMessage" :type="toastType" />

    <div class="max-w-7xl mx-auto">
      <!-- Хедер -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Все задачи</h1>
        <button
          @click="router.push('/tasks/create')"
          class="btn btn-primary inline-flex items-center space-x-1"
        >
          <PlusCircleIcon class="w-4 h-4" />
          <span>Новая задача</span>
        </button>
      </div>

      <!-- Фильтры -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4 mb-6 flex flex-wrap gap-4">
        <select
          v-model="filters.project_id"
          @change="loadTasks"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
        >
          <option :value="null">Все проекты</option>
          <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>

        <select
          v-model="filters.status"
          @change="loadTasks"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white"
        >
          <option :value="null">Все статусы</option>
          <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.name }}</option>
        </select>
      </div>

      <!-- Список задач -->
      <div v-if="tasks.length > 0" class="space-y-4">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200/80 hover:shadow-md transition p-5"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="text-lg font-semibold text-gray-800">{{ task.title }}</h3>
                <!-- ✅ Статус с цветом из бэкенда -->
                <span
                  class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0"
                  :style="{
                    backgroundColor: getStatusColor(task.status),
                    color: '#fff'
                  }"
                >
                  {{ getStatusName(task.status) }}
                </span>
              </div>

              <p class="text-gray-500 text-sm mt-1 line-clamp-2">
                {{ task.description || 'Без описания' }}
              </p>

              <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-400">
                <span class="flex items-center gap-1">
                  <FolderIcon class="w-3.5 h-3.5" />
                  {{ task.project?.name || 'Без проекта' }}
                </span>
                <span class="flex items-center gap-1">
                  <UserIcon class="w-3.5 h-3.5" />
                  {{ task.assignee?.name || 'Не назначен' }}
                </span>
                <span v-if="task.deadline_at" class="flex items-center gap-1">
                  <CalendarIcon class="w-3.5 h-3.5" />
                  {{ formatDate(task.deadline_at) }}
                </span>
              </div>

              <!-- Теги -->
              <div v-if="task.labels && task.labels.length > 0" class="flex flex-wrap gap-1 mt-2">
                <span
                  v-for="label in task.labels"
                  :key="label.id"
                  class="px-2 py-0.5 rounded-full text-xs"
                  :style="{ backgroundColor: label.color || '#e5e7eb', color: '#1f2937' }"
                >
                  {{ label.name }}
                </span>
              </div>
            </div>

            <!-- Смена статуса -->
            <div class="flex-shrink-0">
              <select
                :value="task.status"
                @change="changeStatus(task, $event.target.value)"
                class="btn btn-secondary text-sm px-3 py-1.5"
                :style="{
                  borderColor: getStatusColor(task.status)
                }"
                :disabled="isUpdating === task.id"
              >
                <option
                  v-for="status in statuses"
                  :key="status.value"
                  :value="status.value"
                >
                  {{ status.name }}
                </option>
              </select>
              <span v-if="isUpdating === task.id" class="ml-2 text-xs text-gray-400">⏳</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пустое состояние -->
      <div v-else class="text-center py-16 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
        <FolderIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">Задач пока нет</p>
        <p class="text-sm text-gray-400 mt-1">Создайте первую задачу</p>
        <button
          @click="router.push('/tasks/create')"
          class="mt-4 btn btn-primary inline-flex items-center space-x-1"
        >
          <PlusCircleIcon class="w-4 h-4" />
          <span>Создать задачу</span>
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import Toast from '../components/Toast.vue';
import {
  PlusCircleIcon,
  FolderIcon,
  UserIcon,
  CalendarIcon,
} from '../components/icons';

const router = useRouter();

// ----------------------------------------------
// СОСТОЯНИЕ
// ----------------------------------------------
const tasks = ref([]);
const projects = ref([]);
const statuses = ref([]);
const filters = ref({ project_id: null, status: null });
const isUpdating = ref(null);
const toastMessage = ref('');
const toastType = ref('success');

// ----------------------------------------------
// ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
// ----------------------------------------------
const getStatusName = (status) => {
  const found = statuses.value.find(s => s.value === status);
  return found?.name || status || 'Не указан';
};

const getStatusColor = (status) => {
  const found = statuses.value.find(s => s.value === status);
  return found?.color || '#6b7280';
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

const showToast = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
};

const loadTasks = async () => {
  try {
    const params = {};
    if (filters.value.project_id) params.project_id = filters.value.project_id;
    if (filters.value.status) params.status = filters.value.status;
    const res = await axios.get('/api/tasks', { params });
    // Преобразуем статус в строку, если пришёл объект
    tasks.value = res.data.data.map(task => ({
      ...task,
      status: typeof task.status === 'object' ? task.status.value : task.status,
    }));
  } catch (error) {
    showToast('Ошибка загрузки задач', 'error');
  }
};

const changeStatus = async (task, newStatus) => {
  if (newStatus === task.status) return;

  isUpdating.value = task.id;
  const oldStatus = task.status;

  try {
    await axios.put(`/api/tasks/${task.id}/status`, { status: newStatus });
    task.status = newStatus;
    showToast(`Статус задачи "${task.title}" обновлён`, 'success');
  } catch (error) {
    task.status = oldStatus;
    const message = error.response?.data?.error || 'Не удалось изменить статус';
    showToast(message, 'error');
  } finally {
    isUpdating.value = null;
  }
};

// ----------------------------------------------
// ЗАГРУЗКА ДАННЫХ
// ----------------------------------------------
onMounted(async () => {
  try {
    const [projectsRes, statusesRes] = await Promise.all([
      axios.get('/api/projects'),
      axios.get('/api/task-statuses'),
    ]);
    projects.value = projectsRes.data.data;
    statuses.value = statusesRes.data;
    await loadTasks();
  } catch (error) {
    showToast('Ошибка загрузки данных', 'error');
  }
});
</script>