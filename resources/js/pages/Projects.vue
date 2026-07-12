<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Проекты</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <router-link
                v-for="project in projects"
                :key="project.id"
                :to="`/projects/${project.id}`"
                class="border rounded p-4 hover:shadow-lg transition"
            >
                <h2 class="text-xl font-semibold">{{ project.name }}</h2>
                <!-- <p class="text-sm text-gray-500">{{ project.description }}</p> -->
                <p class="text-xs text-gray-400 mt-2">Задач: {{ project.tasks_count }}</p>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const projects = ref([]);

onMounted(async () => {
    const res = await axios.get('/api/projects');
    projects.value = res.data.data;
});
</script>