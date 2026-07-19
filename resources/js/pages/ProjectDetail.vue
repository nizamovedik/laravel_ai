<template>
  <AuthenticatedLayout>
    <div class="max-w-full">
      <!-- Хедер проекта -->
      <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2 text-sm text-gray-400">
            <router-link to="/projects" class="hover:text-indigo-600">Проекты</router-link>
            <span>/</span>
            <span class="text-gray-600">{{ project.name }}</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ project.name }}</h1>
          <p class="text-gray-500 text-sm mt-0.5">{{ project.description || 'Без описания' }}</p>
          <div class="flex items-center space-x-4 mt-1 text-xs text-gray-400">
            <span class="flex items-center space-x-1">
              <UserIcon class="w-3.5 h-3.5" />
              <span>Владелец: {{ project.owner?.name || 'Не указан' }}</span>
            </span>
            <span class="flex items-center space-x-1">
              <UserIcon class="w-3.5 h-3.5" />
              <span>Тимлид: {{ project.team_lead?.name || 'Не указан' }}</span>
            </span>
            <span class="flex items-center space-x-1">
              <UserIcon class="w-3.5 h-3.5" />
              <span>Задач в проекте: {{ project.tasks_count }}</span>
            </span>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <button
            @click="generateReport"
            :disabled="isGeneratingReport"
            class="btn btn-secondary inline-flex items-center space-x-1"
          >
            <span v-if="isGeneratingReport" class="w-4 h-4 border-2 border-gray-600 border-t-transparent rounded-full animate-spin"></span>
            <span v-else>📊</span>
            <span>{{ isGeneratingReport ? 'Генерация...' : 'Отчёт' }}</span>
          </button>
          <router-link
            :to="`/projects/${project.id}/edit`"
            class="btn btn-secondary inline-flex items-center space-x-1"
          >
            <PencilSquareIcon class="w-4 h-4" />
            <span>Редактировать</span>
          </router-link>
          <button
            @click="showDeleteModal = true"
            class="btn btn-danger inline-flex items-center space-x-1"
          >
            <TrashIcon class="w-4 h-4" />
            <span>Удалить</span>
          </button>
          <router-link :to="`/projects/${project.id}/tasks/create`" class="btn btn-primary flex items-center space-x-1">
            <PlusCircleIcon class="w-4 h-4" />
            <span>Новая задача</span>
          </router-link>
        </div>
      </div>

      <!-- Канбан-доска (рендерится только когда есть массив tasks) -->
      <div v-if="project.tasks" class="card p-4 mb-6">
        <KanbanBoard :tasks="project.tasks" :project-id="project.id" />
      </div>

      <!-- Комментарии -->
      <div class="card p-6">
        <CommentList type="project" :id="projectId" />
      </div>

      <ConfirmModal
        :show="showDeleteModal"
        title="Удаление проекта"
        :message="`Вы уверены, что хотите удалить проект «${project.name}»? Все задачи внутри него будут удалены безвозвратно.`"
        confirm-text="Удалить проект"
        :is-loading="isDeleting"
        @confirm="deleteProject"
        @cancel="showDeleteModal = false"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import KanbanBoard from '../components/KanbanBoard.vue';
import CommentList from '../components/CommentList.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import {
  UserIcon,
  PencilSquareIcon,
  PlusCircleIcon,
  ChatBubbleLeftIcon,
  TrashIcon,
} from '../components/icons';

const isGeneratingReport = ref(false);

const route = useRoute();
const router = useRouter();
const projectId = route.params.id;

const project = ref({
  id: null,
  name: '',
  description: '',
  owner: null,
  team_lead: null,
  tasks: [],
});

const loadProject = async () => {
  try {
    const res = await axios.get(`/api/projects/${projectId}`);
    project.value = res.data.data;
    console.log('📌 Проект загружен. Задач:', project.value.tasks?.length || 0);
  } catch (e) {
    console.error('Ошибка загрузки проекта', e);
  }
};

const showDeleteModal = ref(false);
const isDeleting = ref(false);

const deleteProject = async () => {
  isDeleting.value = true;

  try {
    await axios.delete(`/api/projects/${projectId}`);
    showDeleteModal.value = false;
    await router.push('/projects');
  } catch (error) {
    console.error('Ошибка удаления проекта:', error);
    alert('Не удалось удалить проект');
  } finally {
    isDeleting.value = false;
  }
};

const generateReport = async () => {
  isGeneratingReport.value = true;

  try {
    await axios.post(`/api/projects/${projectId}/generate-report`);
    alert('Отчёт генерируется. Ссылка на скачивание будет в логах (пока что).');
  } catch (error) {
    console.error('Ошибка генерации отчёта:', error);
    alert('Не удалось начать генерацию отчёта');
  } finally {
    isGeneratingReport.value = false;
  }
};

onMounted(() => {
  loadProject();
});
</script>