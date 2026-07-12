<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Кнопка назад -->
            <button @click="router.back()" class="text-gray-500 hover:text-gray-700 transition mb-4 flex items-center space-x-1">
                ← Назад
            </button>

            <!-- Основная информация о задаче -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 mb-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ task.title }}</h1>
                            <span 
                                class="px-3 py-1 rounded-full text-sm font-medium"
                                :style="getStatusStyle(task.status)"
                            >
                                {{ getStatusName(task.status) }}
                            </span>
                        </div>
                        <p class="text-gray-500 mt-2">{{ task.description || 'Без описания' }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <select
                            :value="task.status"
                            @change="changeStatus($event.target.value)"
                            class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white cursor-pointer transition"
                            :style="{ borderColor: getStatusBorderColor(task.status) }"
                            :disabled="isUpdating"
                        >
                            <option 
                                v-for="status in statuses" 
                                :key="status.value" 
                                :value="status.value"
                            >
                                {{ status.name }}
                            </option>
                        </select>
                        <span v-if="isUpdating" class="text-xs text-gray-400 ml-1">⏳</span>
                    </div>
                </div>

                <!-- Детали задачи -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0"><FolderIcon class="w-4 h-4" />Проект</span>
                            <router-link 
                                :to="`/projects/${task.project?.id}`" 
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                {{ task.project?.name || 'Не указан' }}
                            </router-link>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0"><UserIcon class="w-4 h-4" /> Создал</span>
                            <span class="text-gray-700">{{ task.creator?.name || 'Неизвестно' }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0"><UserIcon class="w-4 h-4" /> Исполнитель</span>
                            <span class="text-gray-700">{{ task.assignee?.name || 'Не назначен' }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0"><BoltIcon class="w-4 h-4" /> Приоритет</span>
                            <span 
                                v-if="task.priority"
                                class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :style="{ backgroundColor: task.priority.color || '#e5e7eb', color: '#1f2937' }"
                            >
                                {{ task.priority.name }}
                            </span>
                            <span v-else class="text-gray-400">Не указан</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0"><ClockIcon class="w-4 h-4" /> Дедлайн</span>
                            <span class="text-gray-700">{{ formatDate(task.deadline_at) || 'Не указан' }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-400 w-28 flex-shrink-0">📊 Оценка</span>
                            <span class="text-gray-700">{{ task.estimated_hours ? task.estimated_hours + ' ч' : 'Не указана' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Теги -->
                <div v-if="task.labels && task.labels.length > 0" class="mt-4 pt-4 border-t border-gray-100">
                    <span class="text-sm text-gray-400 mr-2">🏷️ Теги:</span>
                    <span
                        v-for="label in task.labels"
                        :key="label.id"
                        class="inline-block px-2 py-0.5 rounded-full text-xs mr-1"
                        :style="{ backgroundColor: label.color || '#e5e7eb', color: '#1f2937' }"
                    >
                        {{ label.name }}
                    </span>
                </div>

                <!-- Даты создания и обновления -->
                <div class="mt-4 text-xs text-gray-400 border-t border-gray-100 pt-4">
                    <span>Создана: {{ formatDate(task.created_at) }}</span>
                    <span class="ml-4">Обновлена: {{ formatDate(task.updated_at) }}</span>
                </div>
            </div>

            <!-- Комментарии -->
            <CommentList type="task" :id="taskId" />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import CommentList from '../components/CommentList.vue';
import { ClockIcon, BoltIcon, FolderIcon, UserIcon } from '../components/icons';

const router = useRouter();
const route = useRoute();
const taskId = route.params.id;

const task = ref({
    title: '',
    description: '',
    status: 'new',
    priority: null,
    project: null,
    creator: null,
    assignee: null,
    labels: [],
    deadline_at: null,
    estimated_hours: null,
    created_at: null,
    updated_at: null,
});

const statuses = ref([]);
const isUpdating = ref(false);

const statusColors = {
    new: '#3b82f6',
    in_progress: '#f59e0b',
    review: '#8b5cf6',
    done: '#10b981',
    closed: '#6b7280',
    on_hold: '#ef4444',
};

const statusNames = {
    new: 'Новая',
    in_progress: 'В работе',
    review: 'На ревью',
    done: 'Готово',
    closed: 'Закрыта',
    on_hold: 'Отложена',
};

const getStatusStyle = (status) => {
    const color = statusColors[status] || '#6b7280';
    return {
        backgroundColor: color,
        color: '#fff',
    };
};

const getStatusName = (status) => {
    return statusNames[status] || status || 'Не указан';
};

const getStatusBorderColor = (status) => {
    return statusColors[status] || '#6b7280';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const loadTask = async () => {
    try {
        const res = await axios.get(`/api/tasks/${taskId}`);
        task.value = res.data.data;
    } catch (e) {
        console.error('Ошибка загрузки задачи', e);
    }
};

const loadStatuses = async () => {
    try {
        const res = await axios.get('/api/task-statuses');
        statuses.value = res.data;
    } catch (e) {
        console.error('Ошибка загрузки статусов', e);
    }
};

const changeStatus = async (newStatus) => {
    if (newStatus === task.value.status) return;

    isUpdating.value = true;
    const oldStatus = task.value.status;

    try {
        await axios.put(`/api/tasks/${taskId}/status`, { status: newStatus });
        task.value.status = newStatus;
        await loadTask(); // Обновляем данные с сервера
    } catch (e) {
        task.value.status = oldStatus;
        const message = e.response?.data?.error || 'Не удалось изменить статус';
        alert(message);
    } finally {
        isUpdating.value = false;
    }
};

onMounted(() => {
    loadTask();
    loadStatuses();
});
</script>