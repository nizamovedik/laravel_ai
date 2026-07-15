<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4 transform transition-all scale-100">
      <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
          <TrashIcon class="w-5 h-5 text-red-600" />
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ message }}</p>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button
          @click="$emit('cancel')"
          class="btn btn-secondary"
          :disabled="isLoading"
        >
          Отмена
        </button>
        <button
          @click="$emit('confirm')"
          :disabled="isLoading"
          class="btn btn-danger inline-flex items-center gap-1"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span>{{ confirmText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { TrashIcon } from '../components/icons';

defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Подтверждение',
  },
  message: {
    type: String,
    default: 'Вы уверены, что хотите выполнить это действие?',
  },
  confirmText: {
    type: String,
    default: 'Удалить',
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['confirm', 'cancel']);
</script>