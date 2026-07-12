<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Задачи</h1>

        <!-- Фильтр по проекту -->
        <div class="mb-4 flex gap-4">
            <select v-model="filterProjectId" @change="loadTasks" class="border rounded px-3 py-2">
                <option :value="null">Все проекты</option>
                <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                </option>
            </select>
        </div>

        <div class="space-y-4">
            <div v-for="task in tasks" :key="task.id" class="border rounded p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ task.title }}</h3>
                        <p class="text-sm text-gray-500">{{ task.status?.name }}</p>
                        <p class="text-xs text-gray-400">Проект: {{ task.project?.name }}</p>
                    </div>
                    <select v-model="task.status_id" @change="changeStatus(task)" class="border rounded px-2">
                        <option v-for="status in statuses" :key="status.id" :value="status.id">
                            {{ status.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const tasks = ref([]);
const projects = ref([]);
const statuses = ref([]);
const filterProjectId = ref(null);

onMounted(async () => {
    await Promise.all([
        loadTasks(),
        loadProjects(),
        loadStatuses(),
    ]);
});

const loadTasks = async () => {
    const url = filterProjectId.value
        ? `/api/tasks?project_id=${filterProjectId.value}`
        : '/api/tasks';
    const res = await axios.get(url);
    tasks.value = res.data.data;
};

const loadProjects = async () => {
    const res = await axios.get('/api/projects');
    projects.value = res.data.data;
};

const loadStatuses = async () => {
    const res = await axios.get('/api/task-statuses');
    statuses.value = res.data.data;
};

const changeStatus = async (task) => {
    await axios.put(`/api/tasks/${task.id}/status`, {
        status_id: task.status_id,
    });
};
</script>