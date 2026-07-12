<template>
  <div
    class="bg-white rounded-lg border border-gray-200 p-3 cursor-pointer hover:border-indigo-300 transition shadow-sm hover:shadow-md group"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between">
      <h4 class="text-sm font-medium text-gray-800 group-hover:text-indigo-600 transition line-clamp-2">
        {{ task.name }}
      </h4>
      <span class="text-[10px] text-gray-400 flex-shrink-0 ml-2">
        #{{ task.id }}
      </span>
    </div>

    <!-- Теги -->
    <div v-if="task.labels && task.labels.length" class="flex flex-wrap gap-1 mt-1.5">
      <span
        v-for="label in task.labels.slice(0, 3)"
        :key="label.id"
        class="badge text-[10px]"
        :style="{ backgroundColor: label.color || '#e5e7eb', color: '#1f2937' }"
      >
        {{ label.name }}
      </span>
      <span v-if="task.labels.length > 3" class="badge text-[10px] bg-gray-100 text-gray-500">
        +{{ task.labels.length - 3 }}
      </span>
    </div>

    <!-- Нижняя часть -->
    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
      <div class="flex items-center space-x-1.5">
        <!-- Аватар исполнителя -->
        <div
          v-if="task.assignee"
          class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-medium text-indigo-700"
        >
          {{ getInitials(task.assignee.name) }}
        </div>
        <span v-else class="text-[10px] text-gray-400"><UserIcon class="w-3 h-3" />Не назначен</span>
      </div>
      <div class="flex items-center space-x-1.5">
        <!-- Приоритет -->
        <span
          v-if="task.priority"
          class="badge text-[10px]"
          :style="{
            backgroundColor: task.priority.color || '#e5e7eb',
            color: '#1f2937',
          }"
        >
          {{ task.priority.name }}
        </span>
        <!-- Дедлайн -->
        <span
          v-if="task.deadline_at"
          class="text-[10px] text-gray-400 flex items-center space-x-0.5"
        >
          <CalendarIcon class="w-3 h-3" />
          <span>{{ formatDate(task.deadline_at) }}</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});

defineEmits(['click']);

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};
</script>