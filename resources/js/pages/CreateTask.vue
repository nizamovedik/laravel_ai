<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center space-x-4 mb-6">
                <button @click="router.back()" class="text-gray-500 hover:text-gray-700 transition">
                    ← Назад
                </button>
                <h1 class="text-2xl font-bold text-gray-800">✅ Создание задачи</h1>
            </div>

            <form @submit.prevent="handleSubmit" class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <!-- Проект (обязательно) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Проект *</label>
                    <select
                        v-model="form.project_id"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="{ 'border-red-500': errors.project_id }"
                    >
                        <option :value="null">Выберите проект</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.name }}
                        </option>
                    </select>
                    <p v-if="errors.project_id" class="mt-1 text-sm text-red-500">{{ errors.project_id }}</p>
                </div>

                <!-- Название -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="{ 'border-red-500': errors.title }"
                        placeholder="Введите название задачи"
                    />
                    <p v-if="errors.title" class="mt-1 text-sm text-red-500">{{ errors.title }}</p>
                </div>

                <!-- Описание -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="{ 'border-red-500': errors.description }"
                        placeholder="Опишите задачу (необязательно)"
                    />
                    <p v-if="errors.description" class="mt-1 text-sm text-red-500">{{ errors.description }}</p>
                </div>

                <!-- Исполнитель -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Исполнитель</label>
                    <select
                        v-model="form.assignee_id"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                        <option :value="null">Не назначен</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <!-- Приоритет -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
                    <select
                        v-model="form.priority_id"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                        <option :value="null">Не указан</option>
                        <option v-for="priority in priorities" :key="priority.id" :value="priority.id">
                            {{ priority.name }}
                        </option>
                    </select>
                </div>

                <!-- Дедлайн -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дедлайн</label>
                    <input
                        v-model="form.deadline_at"
                        type="date"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                    <p v-if="errors.deadline_at" class="mt-1 text-sm text-red-500">{{ errors.deadline_at }}</p>
                </div>

                <!-- Оценка времени -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Оценка времени (часы)</label>
                    <input
                        v-model="form.estimated_hours"
                        type="number"
                        step="0.5"
                        min="0"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Например: 4.5"
                    />
                    <p v-if="errors.estimated_hours" class="mt-1 text-sm text-red-500">{{ errors.estimated_hours }}</p>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center space-x-4 mt-6">
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center space-x-2 shadow-md"
                    >
                        <span v-if="isSubmitting">⏳</span>
                        <span>{{ isSubmitting ? 'Создание...' : 'Создать задачу' }}</span>
                    </button>
                    <button
                        type="button"
                        @click="router.back()"
                        class="px-6 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition"
                    >
                        Отмена
                    </button>
                </div>

                <!-- Общая ошибка -->
                <p v-if="error" class="mt-4 text-sm text-red-500">{{ error }}</p>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';

const router = useRouter();
const route = useRoute();

const form = ref({
    project_id: null,
    title: '',
    description: '',
    assignee_id: null,
    priority_id: null,
    deadline_at: '',
    estimated_hours: null,
});

const projects = ref([]);
const users = ref([]);
const priorities = ref([]);
const errors = ref({});
const error = ref('');
const isSubmitting = ref(false);

const loadProjects = async () => {
    try {
        const res = await axios.get('/api/projects');
        projects.value = res.data.data;
    } catch (e) {
        console.error('Ошибка загрузки проектов', e);
    }
};

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/users');
        users.value = res.data;
    } catch (e) {
        console.error('Ошибка загрузки пользователей', e);
    }
};

const loadPriorities = async () => {
    try {
        const res = await axios.get('/api/task-priorities');
        priorities.value = res.data.data;
    } catch (e) {
        console.error('Ошибка загрузки приоритетов', e);
    }
};

const handleSubmit = async () => {
    errors.value = {};
    error.value = '';
    isSubmitting.value = true;

    const payload = {
        project_id: form.value.project_id,
        title: form.value.title,
        description: form.value.description || null,
        assignee_id: form.value.assignee_id || null,
        priority_id: form.value.priority_id || null,
        deadline_at: form.value.deadline_at || null,
        estimated_hours: form.value.estimated_hours || null,
    };

    try {
        await axios.post('/api/tasks', payload);
        await router.push('/tasks');
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
            error.value = err.response.data.message || 'Ошибка валидации';
        } else {
            error.value = 'Произошла ошибка при создании задачи';
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    const projectId = route.query.project_id || route.params.projectId || null;
    if (projectId) {
        form.value.project_id = parseInt(projectId);
    }
    loadProjects();
    loadUsers();
    loadPriorities();
});
</script>