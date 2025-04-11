<script setup>
import { v4 as uuid } from "uuid";
import { Link } from "@inertiajs/vue3";
import { ref, computed, watch, nextTick, inject } from "vue";
import Paginate from "@/Components/Paginate.vue";
import Avatar from "@/Shared/Avatar.vue";
import ShowImages from "@/Shared/ShowImages.vue";
import Toggle from "@/Shared/Toggle.vue";
import { useDark } from "@vueuse/core";
import { formatDate, formatTimetoLacle } from "@/services/Functions.ts";
import { defineAsyncComponent } from "vue";
import AdvertisementsApi from "@/services/AdvertisementsApi.ts";
import usersApi from "@/services/UsersApi.ts";
import customersApi from "@/services/CustomersApi.ts";
import occasionsApi from "@/services/OccasionsApi.ts";

const AlertModal = defineAsyncComponent(() =>
    import("@/Components/Modals/AlertModal.vue")
);
const WishModal = defineAsyncComponent(() =>
    import("@/Components/Occasions/WishModal.vue")
);
const OccasionsModal = defineAsyncComponent(() =>
    import("@/Components/Occasions/OccasionsModal.vue")
);
const WishesModal = defineAsyncComponent(() =>
    import("@/Components/Occasions/WishesModal.vue")
);

const isDark = useDark();

import {
    PencilSquareIcon,
    TrashIcon,
    ArrowLongRightIcon,
    ArrowLongLeftIcon,
} from "@heroicons/vue/24/outline";

const emit = defineEmits([
    "deleteItem",
    "editItem",
    "updateSort",
    "perPageChanged",
]);

const loading = ref(false);
const index = ref(0);
const props = defineProps({
    id: {
        type: String,
        default() {
            return `data-table-${uuid()}`;
        },
    },
    host: {
        type: String,
        required: false,
    },
    dir: {
        type: String,
        required: true,
    },
    headers: {
        type: Array,
        required: true,
    },
    paginate: {
        type: Object,
        required: true,
    },

    borderCell: {
        type: Boolean,
        default: false,
    },
    expand: {
        type: Boolean,
        default: false,
    },
    sortBy: {
        type: String,
        required: true,
    },
    sortType: {
        type: String,
        required: true,
    },
    loadingIndex: {
        type: Boolean,
        require: true,
    },
    show_per_page: {
        type: Boolean,
        default: true,
    },
    editButton: {
        type: Boolean,
        default: true,
    },
});

const imgs = computed(() => {
    const arr = [];
    props.paginate?.data?.map((item) => {
        arr.push(item.image);
    });
    return arr;
});
watch(
    () => props.paginate.data,
    (newData, oldData) => {
        console.log("paginate Changed");
        loading.value = false;
    }
);
watch(
    () => props.loadingIndex,
    (newData, oldData) => {
        console.log("loading Index Chnaged");
        loading.value = true;
    }
);
const updateSort = (sortOptions) => {
    setLoading();
    emit("updateSort", sortOptions);
};

const editItem = (item) => {
    emit("editItem", item);
};
const deleteItem = (item) => {
    emit("deleteItem", item);
};

const setLoading = () => {
    loading.value = true;
};
const changeIndex = (item) => {
    index.value = props.paginate.data.findIndex(
        (elem) => elem["id"] == item.id
    );
};
const perPageChanged = (value) => {
    emit("perPageChanged", value);
};

const toggle = (id, who) => {
    if (who == "is_active") customersApi.toggleIsActive(id);
    else if (who == "published") AdvertisementsApi.togglePublished(id);
};

const decreaseWishesCount = (wish) => {
    props.paginate.data.filter((item) => item.id === wish.occasion_id)[0]
        .wishes_count--;
};
const decreaseOccasionsCount = (occasion) => {
    props.paginate.data.filter((item) => item.id === occasion.customer_id)[0]
        .occasions_count--;
};

const emitter = inject("emitter");
const openWishesModal = (occasion) => {
    emitter.emit("open-wishes-modal", occasion);
};
const openOccasionsModal = (customer) => {
    emitter.emit("open-occasions-modal", customer);
};

const showCauser = (causer_type, causer_id) => {
    if (causer_type == "App\\Models\\User") usersApi.editUser(causer_id);
    else if (causer_type == "App\\Models\\Customer")
        customersApi.editCustomer(causer_id);
};

const showSubject = (subject_type, subject_id) => {
    if (subject_type == "App\\Models\\User") usersApi.editUser(subject_id);
    else if (subject_type == "App\\Models\\Customer")
        customersApi.editCustomer(subject_id);
    else if (
        subject_type == "App\\Models\\Occasion" ||
        subject_type == "App\\Models\\Wish"
    )
        occasionsApi.editOccasion(subject_id);
};
</script>
<template>
    <ShowImages :imgs="imgs" :index="index" />
    <AlertModal />
    <WishModal />
    <WishesModal @decreaseWishesCount="decreaseWishesCount" />
    <OccasionsModal @decreaseOccasionsCount="decreaseOccasionsCount" />
    <EasyDataTable
        :id="id"
        table-class-name="customize-table"
        body-expand-row-class-name="expand-row "
        :header-text-direction="dir"
        :body-text-direction="dir"
        :headers="headers"
        :items="paginate.data"
        :rows-per-page="paginate.per_page"
        :rows-per-page-message="__('per_page')"
        :theme-color="isDark ? '#ffffff' : '#5661b3'"
        :border-cell="borderCell"
        :loading="loading"
        @update-sort="updateSort"
        hide-footer
    >
        <!-- <template #loading>
            <img
                src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                style="width: 100px; height: 80px"
            />
        </template> -->
        <template #expand="item" v-if="expand">
            <div style="padding: 15px">
                {{ item.name }}
            </div>
        </template>
        <template #header="header">
            <div class="customize-header">
                {{ __("forms")[header.text] }}
            </div>
        </template>
        <template #empty-message>
            {{ __("no_data") }}
        </template>
        <template #item-image="item">
            <Avatar
                :image="item.image"
                @click="changeIndex(item)"
                :name="item.full_name"
            />
        </template>
        <template #item-is_active="item">
            <Toggle
                :id="item.id"
                :value="item.is_active"
                @toggle="toggle(item.id, 'is_active')"
            />
        </template>
        <template #item-published="item">
            <Toggle
                :id="item.id"
                :value="item.published"
                @toggle="toggle(item.id, 'published')"
            />
        </template>
        <template #item-description="item">
            <p v-if="item.description" class="ellipsis-wrapper">
                {{ item.description }}
            </p>
            <p v-else>---</p>
        </template>
        <template #item-note="item">
            <p v-if="item.note" class="ellipsis-wrapper">
                {{ item.note }}
            </p>
            <p v-else>---</p>
        </template>
        <template #item-start_date="item">
            {{ formatDate(item.start_date) }}
        </template>
        <template #item-created_at="item">
            {{ formatDate(item.created_at) }}
        </template>
        <template #item-start_time="item">
            {{ formatTimetoLacle(item.start_date) }}
        </template>
        <template #item-wishes_count="item">
            <Link
                v-if="item.wishes_count > 0"
                as="button"
                preserve-scroll
                preserve-state
                @click="openWishesModal(item)"
                :disabled="item.wishes_count == 0"
                href="#"
            >
                <div
                    class="flex justify-between py-1 px-4 bg-indigo-500 rounded-full text-white"
                >
                    <span>
                        {{ item.wishes_count }}
                    </span>
                    <component
                        class="h-6 w-6 fill-white"
                        aria-hidden="true"
                        :is="
                            dir == 'rtl'
                                ? ArrowLongLeftIcon
                                : ArrowLongRightIcon
                        "
                    />
                </div>
            </Link>
            <span v-else>---</span>
        </template>
        <template #item-occasions_count="item">
            <Link
                v-if="item.occasions_count > 0"
                as="button"
                preserve-scroll
                preserve-state
                @click="openOccasionsModal(item)"
                :disabled="item.occasions_count == 0"
                href="#"
            >
                <div
                    class="flex justify-between py-1 px-4 bg-indigo-500 rounded-full text-white"
                >
                    <span>
                        {{ item.occasions_count }}
                    </span>
                    <component
                        class="h-6 w-6 fill-white"
                        aria-hidden="true"
                        :is="
                            dir == 'rtl'
                                ? ArrowLongLeftIcon
                                : ArrowLongRightIcon
                        "
                    />
                </div>
            </Link>
            <span v-else>---</span>
        </template>
        <template #item-role="item">
            {{ __("forms")[item.role] }}
        </template>

        <template #item-causer_name="item">
            <Link
                v-if="item.causer_name"
                as="button"
                preserve-scroll
                preserve-state
                @click="showCauser(item.causer_type, item.causer_id)"
                href="#"
            >
                <div
                    class="flex justify-between py-1 px-4 bg-indigo-500 rounded-full text-white"
                >
                    <span>
                        {{ item.causer_name }}
                    </span>
                    <component
                        class="h-6 w-6 fill-white"
                        aria-hidden="true"
                        :is="
                            dir == 'rtl'
                                ? ArrowLongLeftIcon
                                : ArrowLongRightIcon
                        "
                    />
                </div>
            </Link>
            <span v-else>---</span>
        </template>
        <template #item-subject_name="item">
            <Link
                v-if="item.subject_name"
                as="button"
                preserve-scroll
                preserve-state
                @click="showSubject(item.subject_type, item.subject_id)"
                href="#"
            >
                <div
                    class="flex justify-between py-1 px-4 bg-indigo-500 rounded-full text-white"
                >
                    <span>
                        {{ item.subject_name }}
                    </span>
                    <component
                        class="h-6 w-6 fill-white"
                        aria-hidden="true"
                        :is="
                            dir == 'rtl'
                                ? ArrowLongLeftIcon
                                : ArrowLongRightIcon
                        "
                    />
                </div>
            </Link>
            <span v-else>---</span>
        </template>
        <template #item-changes="item">
            <span v-if="item.changes">
                <div v-for="change in item.changes">
                    {{ change }}
                </div>
            </span>
            <span v-else> ---- </span>
        </template>
        <template #item-actions="item">
            <div class="flex">
                <PencilSquareIcon
                    v-if="editButton"
                    class="h-6 w-6 text-blue-500 hover:scale-125 dark:text-gray-400 dark:hover:text-gray-500 cursor-pointer"
                    @click="editItem(item)"
                />
                <TrashIcon
                    class="h-6 w-6 text-blue-500 hover:scale-125 dark:text-gray-400 dark:hover:text-gray-500 cursor-pointer"
                    @click="deleteItem(item)"
                />
            </div>
        </template>
    </EasyDataTable>
    <Paginate
        v-if="paginate.data.length > 0"
        :show_per_page="show_per_page"
        :dir="dir"
        :links="paginate.links"
        :next="paginate.next_page_url"
        :prev="paginate.prev_page_url"
        :from="paginate.from"
        :to="paginate.to"
        :total="paginate.total"
        :last_page="paginate.last_page"
        :per_page="paginate.per_page"
        @perPageChanged="perPageChanged"
    />
</template>
