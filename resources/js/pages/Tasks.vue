<template>
    <AuthenticatedLayout>
        <!-- Toast уведомления -->
        <Toast :message="toastMessage" :type="toastType" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">✅ Все задачи</h1>
            <button 
                @click="router.push('/tasks/create')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2 shadow-md"
            >
                <span>+</span>
                <span>Новая задача</span>
            </button>
        </div>

        <!-- Фильтры -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-4">
            <select v-model="filters.project_id" @change="loadTasks" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option :value="null">Все проекты</option>
                <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <select v-model="filters.status" @change="loadTasks" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option :value="null">Все статусы</option>
                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.name }}</option>
            </select>
        </div>

        <!-- Список задач -->
        <div class="space-y-4">
            <div
                v-for="task in tasks"
                :key="task.id"
                class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h3 class="text-lg font-semibold text-gray-800">{{ task.title }}</h3>
                            <span 
                                class="px-2 py-1 rounded-full text-xs font-medium"
                                :style="getStatusStyle(task.status?.value)"
                            >
                                {{ task.status?.name || 'Не указан' }}
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ task.description || 'Без описания' }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-400">
                            <span>📁 {{ task.project?.name }}</span>
                            <span>👤 {{ task.assignee?.name || 'Не назначен' }}</span>
                            <span v-if="task.deadline_at">⏰ {{ formatDate(task.deadline_at) }}</span>
                        </div>
                        <!-- Теги -->
                        <div class="flex flex-wrap gap-1 mt-2">
                            <span
                                v-for="label in task.labels"
                                :key="label.id"
                                class="px-2 py-0.5 rounded-full text-xs"
                                :style="{ backgroundColor: label.color || '#e5e7eb', color: '#1f2937' }"
                            >
                                {{ label.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Выпадающий список статусов -->
                    <div class="ml-4 flex-shrink-0">
                        <select
                            v-model="task.status.value"
                            @change="changeStatus(task)"
                            class="px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white cursor-pointer transition"
                            :style="{
                                borderColor: getStatusBorderColor(task.status?.value),
                            }"
                            :disabled="isUpdating === task.id"
                        >
                            <option 
                                v-for="status in statuses"
                                :key="status.value" 
                                :value="status.value"
                            >
                                {{ status.name }}
                            </option>
                        </select>
                        <span v-if="isUpdating === task.id" class="text-xs text-gray-400 ml-1">⏳</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="tasks.length === 0" class="text-center py-12">
            <p class="text-gray-400 text-lg">Задач пока нет</p>
            <p class="text-sm text-gray-300">Создайте первую задачу</p>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import Toast from '../components/Toast.vue';

const router = useRouter();
const tasks = ref([]);
const projects = ref([]);
const statuses = ref([]);
const filters = ref({ project_id: null, status: null });
const isUpdating = ref(null); // id задачи, которая сейчас обновляется
const toastMessage = ref('');
const toastType = ref('success');

const getStatusStyle = (status) => {
    const colors = {
        new: { backgroundColor: '#3b82f6', color: '#fff' },
        in_progress: { backgroundColor: '#f59e0b', color: '#fff' },
        review: { backgroundColor: '#8b5cf6', color: '#fff' },
        done: { backgroundColor: '#10b981', color: '#fff' },
        closed: { backgroundColor: '#6b7280', color: '#fff' },
        on_hold: { backgroundColor: '#ef4444', color: '#fff' },
    };
    return colors[status] || { backgroundColor: '#6b7280', color: '#fff' };
};

const getStatusBorderColor = (status) => {
    const colors = {
        new: '#3b82f6',
        in_progress: '#f59e0b',
        review: '#8b5cf6',
        done: '#10b981',
        closed: '#6b7280',
        on_hold: '#ef4444',
    };
    return colors[status] || '#6b7280';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('ru-RU');
};

const showToast = (message, type = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    // Автоматически скроется через 4 секунды (в компоненте Toast)
};

const loadTasks = async () => {
    try {
        const params = {};
        if (filters.value.project_id) params.project_id = filters.value.project_id;
        if (filters.value.status) params.status = filters.value.status;
        const res = await axios.get('/api/tasks', { params });
        tasks.value = res.data.data;
    } catch (error) {
        showToast('Ошибка загрузки задач', 'error');
    }
};

const changeStatus = async (task) => {
    const newStatus = task.status.value;
    const oldStatus = task.status.value; // сохраняем на случай ошибки

    isUpdating.value = task.id;

    try {
        await axios.put(`/api/tasks/${task.id}/status`, { status: newStatus });
        showToast(`Статус задачи "${task.title}" обновлён`, 'success');
        await loadTasks(); // перезагружаем список, чтобы обновить данные
    } catch (error) {
        // Если ошибка 422 — показываем сообщение от бэкенда
        if (error.response?.status === 422) {
            const message = error.response?.data?.error || 'Нельзя изменить статус';
            showToast(message, 'error');
        } else {
            showToast('Ошибка при обновлении статуса', 'error');
        }
        // Возвращаем старый статус в select
        await loadTasks();
    } finally {
        isUpdating.value = null;
    }
};

onMounted(async () => {
    try {
        const [projectsRes, statusesRes] = await Promise.all([
            axios.get('/api/projects'),
            axios.get('/api/task-statuses'),
        ]);
        projects.value = projectsRes.data.data;
        statuses.value = statusesRes.data.map(item => ({
            value: item.value,
            name: item.name,
        }));
        await loadTasks();
    } catch (error) {
        showToast('Ошибка загрузки данных', 'error');
    }
});
</script>

<style scoped>
select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>