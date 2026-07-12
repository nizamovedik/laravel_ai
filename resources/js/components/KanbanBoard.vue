<template>
  <div v-if="loading" class="flex justify-center py-8">
    <span class="text-gray-400">Загрузка статусов...</span>
  </div>

  <div v-else-if="columns.length === 0" class="text-gray-400 text-center py-8">
    Статусы не найдены
  </div>

  <div v-else class="overflow-x-auto pb-4 -mx-4 px-4">
    <div class="flex gap-4 min-w-max">
      <div
        v-for="column in columns"
        :key="column.value"
        class="w-80 flex-shrink-0 bg-gray-50/80 rounded-xl p-3 border border-gray-200/60"
      >
        <!-- Заголовок колонки -->
        <div class="flex items-center justify-between mb-3 px-1">
          <div class="flex items-center space-x-2">
            <span
              class="w-2 h-2 rounded-full flex-shrink-0"
              :style="{ backgroundColor: column.color }"
            ></span>
            <span class="font-semibold text-gray-700 text-sm">{{ column.label }}</span>
            <span class="text-xs text-gray-400 bg-white px-1.5 py-0.5 rounded-full border border-gray-200">
              {{ getTasksForColumn(column.value).length }}
            </span>
          </div>
          <button
            @click="addTask(column.value)"
            class="text-gray-400 hover:text-indigo-600 transition text-sm font-medium"
          >
            + Добавить
          </button>
        </div>

        <!-- Карточки задач -->
        <div
          class="space-y-2 min-h-[400px] max-h-[600px] overflow-y-auto pr-1 custom-scrollbar"
        >
          <TaskCard
            v-for="task in getTasksForColumn(column.value)"
            :key="task.id"
            :task="task"
            @click="openTask(task.id)"
          />
          <!-- Если задач нет -->
          <div
            v-if="getTasksForColumn(column.value).length === 0"
            class="text-center py-6 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg mt-1"
          >
            Нет задач
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import TaskCard from './TaskCard.vue';

const props = defineProps({
  tasks: {
    type: Array,
    required: true,
  },
  projectId: {
    type: [Number, String],
    required: true,
  },
});

const router = useRouter();

const columns = ref([]);
const loading = ref(true);

const statusColors = {
  new: '#3b82f6',
  in_progress: '#f59e0b',
  review: '#8b5cf6',
  done: '#10b981',
  closed: '#6b7280',
  on_hold: '#ef4444',
};

const loadStatuses = async () => {
  try {
    loading.value = true;
    const res = await axios.get('/api/task-statuses');
    
    let data = res.data;
    if (data.data && Array.isArray(data.data)) {
      data = data.data;
    }
    
    if (!Array.isArray(data)) {
      throw new Error('Неверный формат данных статусов');
    }
    
    columns.value = data.map(status => ({
      value: status.value,
      label: status.name,
      color: statusColors[status.value] || '#6b7280',
    }));
  } catch (error) {
    console.error('Ошибка загрузки статусов:', error);
    columns.value = [];
  } finally {
    loading.value = false;
  }
};

const getTasksForColumn = (status) => {
  return props.tasks.filter(task => task.status === status);
};

const openTask = (taskId) => {
  router.push(`/tasks/${taskId}`);
};

const addTask = (status) => {
  router.push(`/projects/${props.projectId}/tasks/create?status=${status}`);
};

onMounted(() => {
  loadStatuses();
});
</script>