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
            <span class="flex items-center gap-1"><UserIcon class="w-3.5 h-3.5" /> Владелец: {{ project.owner?.name || 'Не указан' }}</span>
            <span class="flex items-center gap-1"><UserIcon class="w-3.5 h-3.5" /> Тимлид: {{ project.team_lead?.name || 'Не указан' }}</span>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <router-link
            :to="`/projects/${project.id}/edit`"
            class="btn btn-secondary"
          >
          <PencilSquareIcon class="w-4 h-4" />
          Редактировать
          </router-link>
          <router-link
            :to="`/projects/${project.id}/tasks/create`"
            class="btn btn-primary"
          >
          <PlusCircleIcon class="w-4 h-4" />
          Новая задача
          </router-link>
        </div>
      </div>

      <!-- Канбан-доска -->
      <div class="card p-4 mb-6">
        <KanbanBoard 
          :tasks="project.tasks || []" 
          :project-id="project.id"
        />
      </div>

      <!-- Комментарии -->
      <div class="card p-6">
        <CommentList type="project" :id="projectId" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import KanbanBoard from '../components/KanbanBoard.vue';
import CommentList from '../components/CommentList.vue';
import { PencilSquareIcon, PlusCircleIcon, UserIcon } from '../components/icons';

const route = useRoute();
const projectId = route.params.id;

const project = ref({
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
  } catch (e) {
    console.error('Ошибка загрузки проекта', e);
  }
};

onMounted(() => {
  loadProject();
});
</script>