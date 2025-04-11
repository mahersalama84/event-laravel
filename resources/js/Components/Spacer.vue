<script setup>
const props = defineProps({
    wFull: {
        type: Boolean,
        required: true,
    },
    width: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div
        :style="
            wFull
                ? 'flex:1'
                : width
                ? `height:100%; width: ${width}`
                : height
                ? `height:${height}; width: 100%`
                : ''
        "
    ></div>
</template>
