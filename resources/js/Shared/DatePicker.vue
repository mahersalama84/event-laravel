<script setup>
import { ref, watch } from "vue";
import Datepicker from "vue3-datepicker";

const props = defineProps(["type", "date", "from"]);
const emit = defineEmits(["startSelected", "endSelected"]);
const picked = ref(props.date ? new Date(props.date) : null);

watch(
    () => picked.value,
    (newDate, oldDate) => {
        picked.value = newDate;
        if (props.type == "start") emit("startSelected", newDate);
        else if (props.type == "end") emit("endSelected", newDate);
    }
);
</script>

<template>
    <Datepicker
        class="px-6 ltr:rounded-r rtl:rounded-l border-black focus:outline-none focus:ring focus:border-blue-100 dark:border-white dark:placeholder-white dark:bg-gray-400 dark:focus:bg-gray-500"
        v-model="picked"
        inputFormat="yyyy-MM-dd"
        :lower-limit="from ? new Date(from) : new Date('1970-1-1')"
    />
</template>
