<script setup>
import Dropdown from "@/Shared/Dropdown.vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/24/solid";
import Spacer from "@/Components/Spacer.vue";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import {
    ArrowLongRightIcon,
    ArrowLongLeftIcon,
} from "@heroicons/vue/24/outline";
import { formatDate } from "@/services/Functions.ts";
import { defineAsyncComponent, inject } from "vue";

const WishesModal = defineAsyncComponent(() =>
    import("@/Components/Occasions/WishesModal.vue")
);

const locale = usePage().props.locale;
const props = defineProps(["item"]);
const emit = defineEmits(["deleteOccasion", "decreaseWishesCount"]);
const deleteOccasion = (item) => {
    emit("deleteOccasion", item);
};
const decreaseWishesCount = (wish) => {
    emit("decreaseWishesCount", wish);
};
const emitter = inject("emitter");
const openWishesModal = (occasion) => {
    emitter.emit("open-wishes-modal", occasion);
};
</script>
<template>
    <div class="max-w-sm max-h-44 overflow-hidden shadow-lg dark:bg-gray-500">
        <WishesModal @decreaseWishesCount="decreaseWishesCount" />
        <div class="px-4">
            <div class="font-bold text-xl my-2 dark:text-white">
                {{ item.title }}
            </div>
            <p
                v-if="item.description"
                class="ellipsis-wrapper text-gray-700 text-base dark:text-white"
            >
                {{ item.description }}
            </p>
            <p v-else class="text-gray-700 text-base dark:text-white">---</p>

            <p class="flex mt-4 text-gray-700 text-base dark:text-white">
                {{ formatDate(item.start_date) }}
            </p>
            <p class="flex text-gray-700 text-base dark:text-white">
                {{
                    new Date(item.start_time).toLocaleTimeString("en-GB", {
                        timeZone: "UTC",
                    })
                }}
            </p>
        </div>
        <div class="mb-4 px-2">
            <dropdown class="w-full flex flex-end">
                <template #default>
                    <Spacer :w-full="true" />
                    <div class="group">
                        <EllipsisVerticalIcon
                            class="w-6 h-6 fill-gray-700 dark:fill-white group-hover:fill-indigo-600 dark:group-hover:text-gray-400 focus:fill-indigo-600 dark:focus:text-gray-400"
                        />
                    </div>
                </template>
                <template #dropdown>
                    <div
                        class="mt-2 py-2 text-sm bg-white dark:text-white dark:bg-gray-400 rounded shadow-xl"
                    >
                        <Link
                            as="button"
                            class="block w-full text-left px-6 py-2 disabled:text-gray-400 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                            href="#"
                            preserve-scroll
                            preserve-state
                            @click.prevent="openWishesModal(item)"
                            :disabled="item.wishes_count <= 0"
                            >{{ __("wishes")["wishes"] }}
                            <span v-if="item.wishes_count > 0">
                                ({{ item.wishes_count }})</span
                            >
                        </Link>
                        <Link
                            class="block w-full text-left px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                            href="#"
                            preserve-scroll
                            preserve-state
                            @click.prevent="deleteOccasion(item)"
                            >{{ __("occasions")["delete_occasion"] }}</Link
                        >
                    </div>
                </template>
            </dropdown>
        </div>
    </div>
</template>
