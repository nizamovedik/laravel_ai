<template>
    <AuthenticatedLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Мои проекты</h1>
            <button 
                @click="router.push('/projects/create')"
                class="btn btn-primary"
            >
                <PlusCircleIcon class="w-4 h-4" />
                <span>Новый проект</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <router-link
                v-for="project in projects"
                :key="project.id"
                :to="`/projects/${project.id}`"
                class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200 group"
            >
                <div class="flex items-start justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition">
                        {{ project.name }}
                    </h2>
                    <span class="text-sm bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                        {{ project.tasks_count || 0 }}
                    </span>
                </div>
                <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ project.description || 'Без описания' }}</p>

                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-400">{{ project.owner?.name || 'Не указан' }}</span>
                    <router-link
                        :to="`/projects/${project.id}/tasks/create`"
                        @click.stop
                        class="btn btn-primary"
                    >
                        + Добавить задачу
                    </router-link>
                </div>
            </router-link>
        </div>

        <div v-if="projects.length === 0" class="text-center py-12">
            <p class="text-gray-400 text-lg">У вас пока нет проектов</p>
            <p class="text-sm text-gray-300">Создайте первый проект, чтобы начать работу</p>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { PlusCircleIcon } from '../components/icons';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';

const router = useRouter();
const projects = ref([]);

onMounted(async () => {
    const res = await axios.get('/api/projects');
    projects.value = res.data.data;
});
</script>