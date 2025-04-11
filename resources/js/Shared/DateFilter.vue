<script setup>
import { router, usePage, Head } from "@inertiajs/vue3";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
import DatePicker from "@/Shared/DatePicker.vue";
import Dropdown from "@/Shared/Dropdown.vue";
import { formatDate } from "@/services/Functions.ts";

const props = defineProps(["start", "end"]);

const emit = defineEmits(["startSelected", "endSelected", "reset"]);

const maxWidth = 200;
const startSelected = (d) => {
    let formattedDate = formatDate(d);
    router.get(
        route("dashboard", {
            start: d ? formattedDate : null,
            end: props.end ? formatDate(props.end) : null,
        })
    );
};
const endSelected = (d) => {
    let formattedDate = formatDate(d);
    router.get(
        route("dashboard", {
            start: props.start ? formatDate(props.start) : null,
            end: d ? formattedDate : null,
        })
    );
};
</script>
<template>
    <div class="flex items-center">
        <div class="flex bg-white dark:bg-gray-400 rounded shadow">
            <dropdown
                :auto-close="false"
                class="focus:z-10 px-4 hover:bg-gray-100 dark:hover:bg-gray-500 border-r dark:focus:border-gray-500 focus:border-white rounded-l focus:ring md:px-6"
                placement="bottom-start"
            >
                <template #default>
                    <div class="flex items-baseline">
                        <span
                            class="hidden text-gray-700 dark:text-white md:inline"
                            >{{ __("forms")["filter"] }}</span
                        >
                        <ChevronDownIcon
                            class="w-2 h-2 fill-gray-700 dark:fill-white md:mx-2"
                            name="cheveron-down"
                        />
                    </div>
                </template>
                <template #dropdown>
                    <div
                        class="mt-2 px-4 py-6 w-screen bg-white dark:bg-gray-400 rounded shadow-xl"
                        :style="{ maxWidth: `${maxWidth}px` }"
                    >
                        <slot />
                    </div>
                </template>
            </dropdown>
            <DatePicker
                class="ltr:ml-6 rtl:mr-6"
                @startSelected="startSelected"
                :date="start"
                type="start"
            />
            <DatePicker
                class="ltr:ml-6 rtl:mr-6"
                @endSelected="endSelected"
                :date="end"
                type="end"
                :from="start"
            />
            <!-- <input
                class="relative px-6 py-3 w-full rounded-r border-black focus:outline-none focus:ring focus:border-blue-100 dark:border-white dark:placeholder-white dark:bg-gray-400 dark:focus:bg-gray-500"
                autocomplete="off"
                type="text"
                name="search"
                :placeholder="__('forms')['search...']"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            /> -->
        </div>
        <button
            class="mx-3 text-gray-500 dark:text-white dark:hover:text-gray-500 hover:text-gray-700 dark:focus:text-gray-500 focus:text-indigo-500 text-sm"
            type="button"
            @click="emit('reset')"
        >
            {{ __("forms")["reset"] }}
        </button>
    </div>
</template>
