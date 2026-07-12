<template>
  <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 h-screen sticky top-0 overflow-y-auto custom-scrollbar">
    <div class="p-4">
      <nav class="space-y-1">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition"
          active-class="bg-indigo-50 text-indigo-700"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span>{{ item.label }}</span>
        </router-link>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { FolderIcon, CheckCircleIcon, HomeIcon } from '../components/icons';

const menuItems = [
  { path: '/projects', icon: FolderIcon, label: 'Проекты' },
  { path: '/tasks', icon: CheckCircleIcon, label: 'Задачи' },
  // { path: '/dashboard', icon: HomeIcon, label: 'Дашборд' },
];
</script>