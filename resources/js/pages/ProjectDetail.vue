<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ project.name }}</h1>
                <p class="text-gray-500">{{ project.description }}</p>
            </div>
            <button @click="router.push(`/projects/${project.id}/tasks/create`)" class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer">
                + Новая задача
            </button>
        </div>

        <div class="space-y-4">
            <div v-for="task in tasks" :key="task.id" class="border rounded p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ task.name }}</h3>
                        <p class="text-sm text-gray-500">{{ task.priority.name }}</p>
                        <p class="text-sm text-gray-500">{{ task.status }}</p>
                    </div>
                    <!-- <select v-model="task.status" @change="changeStatus(task)" class="border rounded px-2">
                        <option v-for="status in statuses" :key="status.id" :value="status.id">
                            {{ status.name }}
                        </option>
                    </select> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const projectId = route.params.id;

const project = ref({});
const tasks = ref([]);
// const statuses = ref([]);

onMounted(async () => {
    const [projectRes] = await Promise.all([//, statusesRes] = await Promise.all([
        axios.get(`/api/projects/${projectId}`),
        // axios.get('/api/task-statuses'),
    ]);
    project.value = projectRes.data.data;
    tasks.value = project.value.tasks || [];
    // statuses.value = statusesRes.data.data;
});

// const changeStatus = async (task) => {
//     await axios.put(`/api/tasks/${task.id}/status`, {
//         status_id: task.status_id,
//     });
// };
</script>