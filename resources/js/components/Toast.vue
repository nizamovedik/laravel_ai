<template>
    <div
        v-if="visible"
        class="fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300"
        :class="{
            'bg-red-500 text-white': type === 'error',
            'bg-green-500 text-white': type === 'success',
            'bg-yellow-500 text-white': type === 'warning',
        }"
    >
        {{ message }}
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success',
    },
    duration: {
        type: Number,
        default: 4000,
    },
});

const visible = ref(false);
let timer = null;

watch(
    () => props.message,
    (newVal) => {
        if (newVal) {
            visible.value = true;
            clearTimeout(timer);
            timer = setTimeout(() => {
                visible.value = false;
            }, props.duration);
        }
    },
    { immediate: true }
);
</script>