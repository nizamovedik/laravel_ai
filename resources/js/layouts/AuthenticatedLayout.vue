<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 flex flex-col">
    <!-- Хедер -->
    <AppHeader />

    <!-- Основной блок (сайдбар + контент) -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Левое меню -->
      <Sidebar />

      <!-- Основной контент -->
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';
</script>