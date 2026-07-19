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
        class="w-80 flex-shrink-0 bg-gray-50/80 rounded-xl p-3 border border-gray-200/60 transition-colors duration-300"
        :class="{
          'ring-2': draggedOverColumn === column.value,
          'ring-indigo-400': draggedOverColumn === column.value,
          'ring-inset': draggedOverColumn === column.value,
          'bg-indigo-50/70': draggedOverColumn === column.value
        }"
        @dragover.prevent="draggedOverColumn = column.value"
        @dragleave="draggedOverColumn = null"
        @drop="onDrop(column.value)"
      >
        <!-- Заголовок -->
        <div class="flex items-center justify-between mb-3 px-1">
          <div class="flex items-center space-x-2">
            <span
              class="w-2 h-2 rounded-full flex-shrink-0"
              :style="{ backgroundColor: column.color }"
            ></span>
            <span class="font-semibold text-gray-700 text-sm">{{ column.label }}</span>
            <span class="text-xs text-gray-400 bg-white px-1.5 py-0.5 rounded-full border border-gray-200">
              {{ columnTasks[column.value]?.length || 0 }}
            </span>
          </div>
          <button
            @click="addTask(column.value)"
            class="text-gray-400 hover:text-indigo-600 transition text-sm font-medium"
          >
            + Добавить
          </button>
        </div>

        <draggable
          v-model="columnTasks[column.value]"
          group="shared"
          item-key="id"
          class="space-y-2 min-h-[400px] max-h-[600px] overflow-y-auto pr-1 custom-scrollbar"
          :animation="300"
          :ghost-class="'opacity-50'"
          :drag-class="['scale-105', 'shadow-lg']"
          :chosen-class="['ring-2', 'ring-indigo-400']"
          @start="onDragStart($event, column.value)"
          @change="onListChange($event, column.value)"
        >
          <div
            v-for="element in columnTasks[column.value]"
            :key="element.id"
            class="relative"
          >
            <TaskCard 
              :task="element" 
              @click="openTask(element.id)"
              :class="{
                'opacity-50 pointer-events-none': isTaskUpdating(element.id)
              }"
            />
            <div
              v-if="isTaskUpdating(element.id)"
              class="absolute inset-0 flex items-center justify-center bg-white/60 rounded-lg"
            >
              <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
          </div>

          <div
            v-if="!columnTasks[column.value] || columnTasks[column.value].length === 0"
            class="text-center py-6 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg mt-1"
          >
            Перетащите задачу сюда
          </div>
        </draggable>

        <div
          v-if="getColumnHistory(column.value).length > 0"
          class="mt-2 text-xs text-gray-400 border-t border-gray-100 pt-2"
        >
          <span class="cursor-help" title="Последние изменения статуса">
            📋 История: {{ getColumnHistory(column.value).slice(0, 3).join(', ') }}
            <span v-if="getColumnHistory(column.value).length > 3" class="text-gray-300">
              + ещё {{ getColumnHistory(column.value).length - 3 }}
            </span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import TaskCard from './TaskCard.vue';

// ----------------------------------------------
// PROPS
// ----------------------------------------------
const props = defineProps({
  tasks: {
    type: Array,
    required: true,
    default: () => [],
  },
  projectId: {
    type: [Number, String],
    required: true,
  },
});

// ----------------------------------------------
// EMITS
// ----------------------------------------------
const emit = defineEmits(['update-task-status']);

// ----------------------------------------------
// СОСТОЯНИЕ
// ----------------------------------------------
const router = useRouter();
const columns = ref([]);
const loading = ref(true);
const columnTasks = ref({});
const pendingStatusUpdate = ref(null);
const draggedOverColumn = ref(null);
const updatingTasks = ref(new Set());
const history = ref({});

// ----------------------------------------------
// ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
// ----------------------------------------------
const isTaskUpdating = (taskId) => updatingTasks.value.has(taskId);

const getColumnHistory = (status) => history.value[status] || [];

// ----------------------------------------------
// СИНХРОНИЗАЦИЯ ЗАДАЧ С КОЛОНКАМИ
// ----------------------------------------------
const syncTasks = () => {
  if (!columns.value.length) return;

  columns.value.forEach(col => {
    const filteredTasks = props.tasks.filter(task => task.status === col.value);
    
    if (!columnTasks.value[col.value]) {
      columnTasks.value[col.value] = [];
    }
    
    columnTasks.value[col.value].splice(0, columnTasks.value[col.value].length, ...filteredTasks);
  });

  Object.keys(columnTasks.value).forEach(key => {
    if (!columns.value.some(col => col.value === key)) {
      delete columnTasks.value[key];
    }
  });
};

// ----------------------------------------------
// WATCH
// ----------------------------------------------
watch(
  () => columns.value,
  () => {
    if (columns.value.length > 0 && props.tasks.length > 0) {
      syncTasks();
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.tasks,
  (newTasks) => {
    if (columns.value.length > 0 && newTasks.length > 0) {
      syncTasks();
    }
  },
  { deep: true, immediate: true }
);

// ----------------------------------------------
// DRAG & DROP
// ----------------------------------------------
const onDragStart = (event, oldStatus) => {
  const taskId = event.item._underlying_vm_.id;
  
  pendingStatusUpdate.value = {
    taskId,
    oldStatus,
  };
};

const onDrop = (newStatus) => {
  // Обработка drop для синхронизации подсветки
};

const onListChange = async (event, newStatus) => {
  if (!event.added) {
    return;
  }

  const taskId = event.added.element.id;

  if (!pendingStatusUpdate.value || pendingStatusUpdate.value.taskId !== taskId) {
    return;
  }

  const { oldStatus } = pendingStatusUpdate.value;

  if (oldStatus === newStatus) {
    pendingStatusUpdate.value = null;
    return;
  }

  updatingTasks.value.add(taskId);

  try {
    await axios.put(`/api/tasks/${taskId}/status`, { status: newStatus });
  

    const task = props.tasks.find(t => t.id === taskId);
    if (task) {
      task.status = newStatus;
      emit('update-task-status', taskId, newStatus);
    }
    
    syncTasks();
  } catch (error) {
    console.error('❌ Ошибка обновления статуса:', error);
    alert(`Не удалось изменить статус задачи: ${error.response?.data?.message || error.message}`);
    
    const task = props.tasks.find(t => t.id === taskId);
    if (task) {
      task.status = oldStatus;
    }
    syncTasks();
  } finally {
    updatingTasks.value.delete(taskId);
    pendingStatusUpdate.value = null;
  }
};

// ----------------------------------------------
// МЕТОДЫ
// ----------------------------------------------
const openTask = (taskId) => {
  router.push(`/tasks/${taskId}`);
};

const addTask = (status) => {
  router.push(`/projects/${props.projectId}/tasks/create?status=${status}`);
};

// ----------------------------------------------
// ЗАГРУЗКА СТАТУСОВ
// ----------------------------------------------
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

    // ✅ Используем цвет из данных, а не из хардкода
    columns.value = data.map(status => ({
      value: status.value,
      label: status.name,
      color: status.color || '#6b7280', // fallback, если цвет не пришёл
    }));

    if (props.tasks.length > 0) {
      syncTasks();
    }
  } catch (error) {
    console.error('❌ Ошибка загрузки статусов:', error);
    columns.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadStatuses();
});
</script>

<style scoped>
.animate-spin {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.scale-105 {
  transform: scale(1.05);
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
</style>