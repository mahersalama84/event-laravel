<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/20/solid";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import SelectOptions from "@/Components/SelectOptions.vue";

const emit = defineEmits(["perPageChanged"]);
const props = defineProps([
    "dir",
    "links",
    "next",
    "prev",
    "from",
    "to",
    "total",
    "per_page",
    "show_per_page",
]);
const options = ref([
    { value: 1, selected: props.per_page == 1 },
    { value: 5, selected: props.per_page == 5 },
    { value: 10, selected: props.per_page == 10 },
    { value: 25, selected: props.per_page == 25 },
    { value: 50, selected: props.per_page == 50 },
    { value: 100, selected: props.per_page == 100 },
    { value: 200, selected: props.per_page == 200 },
]);
const perPageChanged = (value) => {
    emit("perPageChanged", value);
};
</script>

<template>
    <div
        class="flex items-center justify-between border-t border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-400 px-4 py-3 sm:px-6"
    >
        <div class="flex flex-1 justify-between lg:hidden">
            <Link
                preserve-scroll
                preserve-state
                :disabled="!prev"
                as="button"
                :href="prev ?? '#'"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white dark:hover:text-gray-400 dark:text-white dark:bg-gray-400 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >{{ __("previous") }}</Link
            >
            <Link
                preserve-scroll
                preserve-state
                :disabled="!next"
                as="button"
                :href="next ?? '#'"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white dark:hover:text-gray-400 dark:text-white dark:bg-gray-400 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >{{ __("next") }}</Link
            >
        </div>
        <div
            class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-between"
        >
            <div>
                <p class="text-sm text-gray-700 dark:text-white">
                    <span class="font-medium">{{ from }}</span>
                    {{ "~" }}
                    <span class="font-medium">{{ to }}</span>
                    {{ " " }}
                    {{ __("of") }}
                    {{ " " }}
                    <span class="font-medium">{{ total }}</span>
                </p>
            </div>
            <SelectOptions
                v-if="show_per_page"
                :options="options"
                :per_page="per_page"
                @optionSelected="perPageChanged"
            />
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <Link
                        preserve-scroll
                        preserve-state
                        v-for="(link, index) in links"
                        :key="index"
                        :href="link.url ?? '#'"
                        as="button"
                        :disabled="link.url == null"
                        aria-current="page"
                        :class="
                            link.active
                                ? 'relative z-10 inline-flex items-center bg-indigo-600 dark:bg-white dark:text-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-indigo-600 hover:text-white dark:text-white dark:hover:bg-white dark:hover:text-indigo-600 focus:z-20 focus:outline-offset-0 disabled:text-gray-400 disabled:hover:bg-transparent disabled:dark:text-gray-500'
                        "
                        >{{ __(link.label) }}</Link
                    >
                </nav>
            </div>
        </div>
    </div>
</template>
