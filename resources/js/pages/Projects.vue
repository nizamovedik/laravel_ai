<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Хедер -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Проекты</h1>
        <button
          @click="router.push('/projects/create')"
          class="btn btn-primary flex items-center space-x-1"
        >
          <PlusCircleIcon class="w-4 h-4" />
          <span>Новый проект</span>
        </button>
      </div>

      <!-- Сетка карточек -->
      <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="project in projects"
          :key="project.id"
          :to="`/projects/${project.id}`"
          class="group bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 hover:shadow-lg hover:border-indigo-200 transition-all duration-200"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h2 class="text-lg font-semibold text-gray-800 group-hover:text-indigo-600 transition">
                {{ project.name }}
              </h2>
              <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                {{ project.description || 'Без описания' }}
              </p>
            </div>
            <span class="flex-shrink-0 ml-4 px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full">
              {{ project.tasks_count || 0 }}
            </span>
          </div>

          <div class="mt-4 flex items-center justify-between text-sm text-gray-400 border-t border-gray-100 pt-3">
            <span class="flex items-center space-x-1.5">
              <UserIcon class="w-3.5 h-3.5" />
              <span>{{ project.owner?.name || 'Без владельца' }}</span>
            </span>
            <span class="flex items-center space-x-1 text-indigo-500 font-medium group-hover:text-indigo-700">
              <span>Открыть</span>
              <ArrowRightIcon class="w-3.5 h-3.5" />
            </span>
          </div>
        </router-link>
      </div>

      <!-- Пустое состояние -->
      <div v-else class="text-center py-16 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
        <FolderIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">У вас пока нет проектов</p>
        <p class="text-sm text-gray-400 mt-1">Создайте первый проект, чтобы начать работу</p>
        <button
          @click="router.push('/projects/create')"
          class="mt-4 btn-primary inline-flex items-center space-x-1"
        >
          <PlusCircleIcon class="w-4 h-4" />
          <span>Создать проект</span>
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
import { PlusCircleIcon, UserIcon, FolderIcon, ArrowRightIcon } from '../components/icons';

const router = useRouter();
const projects = ref([]);

const loadProjects = async () => {
  try {
    const res = await axios.get('/api/projects');
    projects.value = res.data.data;
  } catch (error) {
    console.error('Ошибка загрузки проектов:', error);
  }
};

onMounted(() => {
  loadProjects();
});
</script>