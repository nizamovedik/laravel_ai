<template>
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto">
      <button @click="router.back()" class="flex items-center space-x-2 text-gray-400 hover:text-gray-600 transition mb-6">
        <ArrowLeftIcon class="w-5 h-5" />
        <span>Назад</span>
      </button>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 flex-wrap">
              <h1 class="text-2xl font-bold text-gray-800">{{ task.title }}</h1>
              <span
                class="px-3 py-1 rounded-full text-xs font-medium"
                :style="{
                  backgroundColor: getStatusColor(task.status),
                  color: '#fff'
                }"
              >
                {{ getStatusName(task.status) }}
              </span>
            </div>
            <p class="text-gray-500 mt-2">{{ task.description || 'Без описания' }}</p>
          </div>

          <!-- ✅ Кнопка "Редактировать" и смена статуса -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <router-link
              :to="`/tasks/${task.id}/edit`"
              class="btn btn-secondary text-sm px-3 py-1.5 inline-flex items-center gap-1"
            >
              <PencilSquareIcon class="w-4 h-4" />
              <span>Редактировать</span>
            </router-link>

            <select
              v-model="task.status"
              @change="changeStatus"
              class="btn btn-secondary text-sm px-3 py-1.5"
              :style="{ borderColor: getStatusColor(task.status) }"
              :disabled="isUpdating"
            >
              <option
                v-for="status in statuses"
                :key="status.value"
                :value="status.value"
              >
                {{ status.name }}
              </option>
            </select>
            <span v-if="isUpdating" class="ml-2 text-sm text-gray-400">⏳</span>
          </div>
        </div>

        <!-- Детали -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
          <div class="space-y-2">
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <FolderIcon class="w-4 h-4" />
                <span>Проект</span>
              </span>
              <router-link
                :to="`/projects/${task.project?.id}`"
                class="text-indigo-600 hover:text-indigo-800 font-medium"
              >
                {{ task.project?.name || 'Не указан' }}
              </router-link>
            </div>
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <UserIcon class="w-4 h-4" />
                <span>Создал</span>
              </span>
              <span class="text-gray-700">{{ task.creator?.name || 'Неизвестно' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <UserIcon class="w-4 h-4" />
                <span>Исполнитель</span>
              </span>
              <span class="text-gray-700">{{ task.assignee?.name || 'Не назначен' }}</span>
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <BoltIcon class="w-4 h-4" />
                <span>Приоритет</span>
              </span>
              <span
                v-if="task.priority"
                class="px-2 py-0.5 rounded-full text-xs font-medium"
                :style="{
                  backgroundColor: task.priority.color || '#6b7280',
                  color: '#fff'
                }"
              >
                {{ task.priority.name }}
              </span>
              <span v-else class="text-gray-400">Не указан</span>
            </div>
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <CalendarIcon class="w-4 h-4" />
                <span>Дедлайн</span>
              </span>
              <span class="text-gray-700">{{ formatDate(task.deadline_at) || 'Не указан' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <span class="text-gray-400 w-28 flex-shrink-0 flex items-center gap-1">
                <ClockIcon class="w-4 h-4" />
                <span>Оценка</span>
              </span>
              <span class="text-gray-700">{{ task.estimated_hours ? task.estimated_hours + ' ч' : 'Не указана' }}</span>
            </div>
          </div>
        </div>

        <div v-if="task.labels && task.labels.length > 0" class="mt-4 pt-4 border-t border-gray-100">
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">🏷️ Теги:</span>
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

        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-400">
          <span>Создана: {{ formatDate(task.created_at) }}</span>
          <span>Обновлена: {{ formatDate(task.updated_at) }}</span>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <CommentList type="task" :id="taskId" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import CommentList from '../components/CommentList.vue';
import {
  ArrowLeftIcon,
  FolderIcon,
  UserIcon,
  BoltIcon,
  CalendarIcon,
  ClockIcon,
  ChatBubbleLeftIcon,
  PencilSquareIcon,
} from '../components/icons';

const router = useRouter();
const route = useRoute();
const taskId = route.params.id;

const task = ref({
  id: null,
  title: '',
  description: '',
  status: 'new',
  priority: null,
  project: null,
  creator: null,
  assignee: null,
  labels: [],
  deadline_at: null,
  estimated_hours: null,
  created_at: null,
  updated_at: null,
});

const statuses = ref([]);
const isUpdating = ref(false);

// ----------------------------------------------
// МЕТОДЫ ДЛЯ СТАТУСОВ (используем данные с бэкенда)
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
    hour: '2-digit',
    minute: '2-digit',
  });
};

const loadTask = async () => {
  try {
    const res = await axios.get(`/api/tasks/${taskId}`);
    const data = res.data.data;
    
    task.value = {
      ...data,
      status: typeof data.status === 'object' ? data.status.value : data.status,
    };
  } catch (e) {
    console.error('Ошибка загрузки задачи', e);
  }
};

const loadStatuses = async () => {
  try {
    const res = await axios.get('/api/task-statuses');
    statuses.value = res.data;
  } catch (e) {
    console.error('Ошибка загрузки статусов', e);
  }
};

const changeStatus = async () => {
  const newStatus = task.value.status;
  const oldStatus = task.value._originalStatus || task.value.status;
  
  if (newStatus === oldStatus) return;

  isUpdating.value = true;

  try {
    await axios.put(`/api/tasks/${taskId}/status`, { status: newStatus });
    task.value._originalStatus = newStatus;
  } catch (e) {
    task.value.status = oldStatus;
    const message = e.response?.data?.error || 'Не удалось изменить статус';
    alert(message);
  } finally {
    isUpdating.value = false;
  }
};

watch(
  () => task.value.status,
  (newVal) => {
    if (newVal && !task.value._originalStatus) {
      task.value._originalStatus = newVal;
    }
  },
  { immediate: true }
);

onMounted(() => {
  loadTask();
  loadStatuses();
});
</script>