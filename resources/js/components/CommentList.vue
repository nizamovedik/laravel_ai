<template>
  <div class="mt-8">
    <h3 class="text-md font-semibold text-gray-800 mb-4 flex item-center gap-2">
         <ChatBubbleLeftIcon class="w-5 h-5 text-gray-500" />
         Комментарии
    </h3>

    <!-- Список комментариев -->
    <div v-if="comments.length > 0" class="space-y-4">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="card p-4 hover:shadow-md transition group"
      >
        <div class="flex items-start space-x-3">
          <!-- Аватар -->
          <div
            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium text-white flex-shrink-0"
            :style="{ backgroundColor: getAvatarColor(comment.user?.name || 'Аноним') }"
          >
            {{ getInitials(comment.user?.name || 'Аноним') }}
          </div>

          <!-- Контент -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <span class="font-semibold text-gray-800 text-sm">
                  {{ comment.user?.name || 'Аноним' }}
                </span>
                <span class="text-xs text-gray-400">
                  {{ formatDate(comment.created_at) }}
                </span>
              </div>

              <!-- Кнопка удаления (появляется при ховере) -->
              <button
                v-if="comment.can_delete"
                @click="deleteComment(comment.id)"
                class="text-gray-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100 text-sm"
                title="Удалить"
              >
            <TrashIcon class="w-4 h-4" />
              </button>
            </div>

            <!-- Текст -->
            <p class="text-gray-700 text-sm mt-1 break-words">
              {{ comment.body }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Если комментариев нет -->
    <div v-else class="text-center py-6 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
      Пока нет комментариев. Напишите первый!
    </div>

    <!-- Форма добавления комментария -->
    <form @submit.prevent="submitComment" class="mt-4 flex items-start space-x-3">
      <!-- Аватар текущего пользователя -->
      <div
        class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium text-white flex-shrink-0"
        :style="{ backgroundColor: getAvatarColor(authStore.userName) }"
      >
        {{ getInitials(authStore.userName) }}
      </div>

      <div class="flex-1">
        <textarea
          v-model="newComment"
          rows="2"
          class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
          placeholder="Напишите комментарий..."
          @keydown.ctrl.enter="submitComment"
        />
        <div class="flex justify-end mt-2">
          <button
            type="submit"
            :disabled="isSubmitting || !newComment.trim()"
            class="btn btn-primary btn-sm"
          >
            <ArrowRightIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </form>

    <!-- Сообщения об ошибках/успехе -->
    <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
    <p v-if="success" class="mt-2 text-sm text-green-500">{{ success }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../store/auth';
import { ChatBubbleLeftIcon, TrashIcon, ArrowRightIcon } from '../components/icons';

const props = defineProps({
  type: {
    type: String,
    required: true, // 'task' или 'project'
  },
  id: {
    type: [Number, String],
    required: true,
  },
});

const authStore = useAuthStore();

const comments = ref([]);
const newComment = ref('');
const isSubmitting = ref(false);
const error = ref('');
const success = ref('');

// Цвета для аватаров
const avatarColors = [
  '#4f46e5', '#7c3aed', '#2563eb', '#0891b2',
  '#059669', '#65a30d', '#ca8a04', '#d97706',
  '#dc2626', '#db2777', '#9333ea',
];

const getAvatarColor = (name) => {
  if (!name) return '#6b7280';
  const index = name.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
  return avatarColors[index % avatarColors.length];
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = Math.floor((now - d) / 1000 / 60); // минуты

  if (diff < 1) return 'только что';
  if (diff < 60) return `${diff} мин. назад`;
  if (diff < 1440) return `${Math.floor(diff / 60)} ч. назад`;
  if (diff < 4320) return `${Math.floor(diff / 1440)} д. назад`;

  return d.toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const loadComments = async () => {
  try {
    const res = await axios.get(`/api/comments/${props.type}/${props.id}`);
    comments.value = res.data.data || [];
  } catch (e) {
    console.error('Ошибка загрузки комментариев', e);
    error.value = 'Не удалось загрузить комментарии';
  }
};

const submitComment = async () => {
  const text = newComment.value.trim();
  if (!text) return;

  error.value = '';
  success.value = '';
  isSubmitting.value = true;

  try {
    const res = await axios.post(`/api/comments/${props.type}/${props.id}`, {
      body: text,
    });
    comments.value.unshift(res.data.data);
    newComment.value = '';
    success.value = 'Комментарий добавлен';
    setTimeout(() => success.value = '', 3000);
  } catch (e) {
    error.value = e.response?.data?.message || 'Ошибка при добавлении комментария';
  } finally {
    isSubmitting.value = false;
  }
};

const deleteComment = async (commentId) => {
  if (!confirm('Удалить комментарий?')) return;

  try {
    await axios.delete(`/api/comments/${commentId}`);
    comments.value = comments.value.filter(c => c.id !== commentId);
    success.value = 'Комментарий удалён';
    setTimeout(() => success.value = '', 3000);
  } catch (e) {
    error.value = 'Ошибка при удалении комментария';
  }
};

onMounted(() => {
  loadComments();
});
</script>