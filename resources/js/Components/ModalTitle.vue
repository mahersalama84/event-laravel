<script setup>
defineProps({
    title: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div
        class="flex items-center px-4 py-4 bg-gray-200 text-black dark:text-white dark:bg-gray-500"
    >
        <slot />
    </div>
</template>
