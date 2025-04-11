<script setup>
import { Link } from "@inertiajs/vue3";
import Dropdown from "@/Shared/Dropdown.vue";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";

const emit = defineEmits(["optionSelected"]);
const props = defineProps(["options", "per_page", "selectedEvent"]);
const optionSelected = (value) => {
    emit("optionSelected", value);
};
</script>
<template>
    <dropdown placement="bottom-end">
        <template #default>
            <div class="group flex items-center cursor-pointer select-none">
                <div
                    class="ltr:mr-1 rtl:ml-1 text-gray-700 dark:text-white dark:group-hover:text-gray-500 group-hover:text-indigo-600 focus:text-indigo-600 whitespace-nowrap"
                >
                    <span>{{ per_page }}</span>
                </div>
                <ChevronDownIcon
                    class="w-4 h-4 fill-gray-700 group-hover:rotate-180 dark:fill-white dark:bg-gray-400 dark:group-hover:fill-gray-500 group-hover:fill-indigo-600 focus:fill-indigo-600"
                    name="cheveron-down"
                />
            </div>
        </template>
        <template #dropdown>
            <div
                class="mt-2 py-2 text-sm bg-white rounded shadow-xl dark:text-white dark:bg-gray-400"
            >
                <Link
                    href="#"
                    as="button"
                    class="block px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                    v-for="option in options"
                    @click="optionSelected(option.value)"
                    >{{ option.value }}</Link
                >
            </div>
        </template>
    </dropdown>
</template>
