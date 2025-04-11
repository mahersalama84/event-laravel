<script setup>
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
</script>
<template>
    <div class="flex items-center">
        <div class="flex w-full bg-white dark:bg-gray-400 rounded shadow">
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
            <input
                class="relative px-6 py-3 w-full ltr:rounded-r rtl:rounded-l border-black focus:outline-none focus:ring focus:border-blue-100 dark:border-white dark:placeholder-white dark:bg-gray-400 dark:focus:bg-gray-500"
                autocomplete="off"
                type="text"
                name="search"
                :placeholder="__('forms')['search...']"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
        <button
            class="mx-3 text-gray-500 dark:text-white dark:hover:text-gray-500 hover:text-gray-700 dark:focus:text-gray-500 focus:text-indigo-500 text-sm"
            type="button"
            @click="$emit('reset')"
        >
            {{ __("forms")["reset"] }}
        </button>
    </div>
</template>

<script>
import Dropdown from "@/Shared/Dropdown.vue";

export default {
    components: {
        Dropdown,
    },
    props: {
        modelValue: String,
        maxWidth: {
            type: Number,
            default: 300,
        },
    },
    emits: ["update:modelValue", "reset"],
};
</script>
