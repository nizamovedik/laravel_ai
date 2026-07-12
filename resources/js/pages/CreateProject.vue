<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center space-x-4 mb-6">
                <button @click="router.back()" class="text-gray-500 hover:text-gray-700 transition">
                    ← Назад
                </button>
                <h1 class="text-2xl font-bold text-gray-800">📁 Создание проекта</h1>
            </div>

            <form @submit.prevent="handleSubmit" class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <!-- Название -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="{ 'border-red-500': errors.name }"
                        placeholder="Введите название проекта"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-500">{{ errors.name }}</p>
                </div>

                <!-- Описание -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="{ 'border-red-500': errors.description }"
                        placeholder="Опишите проект (необязательно)"
                    />
                    <p v-if="errors.description" class="mt-1 text-sm text-red-500">{{ errors.description }}</p>
                </div>

                <!-- Тимлид (опционально) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Тимлид (необязательно)</label>
                    <select
                        v-model="form.team_lead_id"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                        <option :value="null">Не выбран</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <!-- Дата начала -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата начала</label>
                    <input
                        v-model="form.started_at"
                        type="date"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                </div>

                <!-- Дата окончания -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата окончания</label>
                    <input
                        v-model="form.deadline_at"
                        type="date"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                    <p v-if="errors.deadline_at" class="mt-1 text-sm text-red-500">{{ errors.deadline_at }}</p>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center space-x-4">
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="btn btn-primary"
                    >
                        <span v-if="isSubmitting">⏳</span>
                        <span>{{ isSubmitting ? 'Создание...' : 'Создать проект' }}</span>
                    </button>
                    <button
                        type="button"
                        @click="router.back()"
                        class="btn btn-secondary"
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';

const router = useRouter();

const form = ref({
    name: '',
    description: '',
    team_lead_id: null,
    started_at: '',
    deadline_at: '',
});

const users = ref([]);
const errors = ref({});
const error = ref('');
const isSubmitting = ref(false);

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/users');
        users.value = res.data;
    } catch (e) {
        console.error('Ошибка загрузки пользователей', e);
    }
};

const handleSubmit = async () => {
    // Сбрасываем ошибки
    errors.value = {};
    error.value = '';
    isSubmitting.value = true;

    // Формируем данные
    const payload = {
        name: form.value.name,
        description: form.value.description || null,
        team_lead_id: form.value.team_lead_id || null,
        started_at: form.value.started_at || null,
        deadline_at: form.value.deadline_at || null,
    };

    try {
        await axios.post('/api/projects', payload);
        await router.push('/projects');
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
            error.value = err.response.data.message || 'Ошибка валидации';
        } else {
            error.value = 'Произошла ошибка при создании проекта';
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    loadUsers();
});
</script>