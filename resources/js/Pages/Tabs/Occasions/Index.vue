<script setup>
import { inject } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import SearchFilter from "@/Shared/SearchFilter.vue";
import DatePicker from "@/Shared/DatePicker.vue";
import { formatDate } from "@/services/Functions.ts";
import OccasionsApi from "@/services/OccasionsApi.ts";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";

const props = defineProps(["paginate", "locale", "filters", "sorts", "host"]);

const emitter = inject("emitter");
const editOccasion = (occasion) => {
    OccasionsApi.editOccasion(occasion.id);
};
const deleteOccasion = (occasion) => {
    emitter.emit(
        "open-alert-modal",
        OccasionsApi.getDeleteObject(occasion, "index")
    );
};
emitter.off("ok-delete-occasions-index");
emitter.on("ok-delete-occasions-index", (occasion) => {
    console.log("ok-delete-occasions-index");
    router.delete(`/occasions/${occasion.id}`);
});
</script>

<template>
    <Head :title="__('occasions')['occasions']" />

    <AuthenticatedLayout>
        <template #header>
            {{ __("occasions")["occasions"] }}
        </template>
        <div class="flex items-center justify-between mb-6">
            <search-filter
                v-model="form.search"
                class="mr-4 w-full max-w-md"
                @reset="reset"
            >
                <label class="block text-gray-700">{{
                    __("forms")["start_date"]
                }}</label>
                <DatePicker
                    class="mt-2 w-full"
                    @startSelected="startSelected"
                    :date="form.start_date"
                    type="start"
                />
            </search-filter>
            <Link class="btn-indigo" :href="route('occasions.create')">
                <span>{{ __("forms")["add"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("occasions")["occasion"] }}</span
                >
            </Link>
        </div>
        <DataTable
            :dir="locale == 'ar' ? 'rtl' : 'ltr'"
            :headers="headers"
            :paginate="paginate"
            :border-cell="true"
            :expand="false"
            sort-by="created_at"
            sort-type="desc"
            :loading-index="loadingIndex"
            @editItem="editOccasion"
            @deleteItem="deleteOccasion"
            @updateSort="updateSort"
            @perPageChanged="perPageChanged"
        />
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            loadingIndex: false,
            form: {
                search: this.filters?.search,
                start_date: this.filters?.start_date,
            },
            sorting: {
                sortBy: this.sorts?.sortBy,
                sortType: this.sorts?.sortType,
            },
            pagination: {
                page: this.paginate?.current_page,
                per_page: this.paginate?.per_page,
            },
            headers: [
                // { text: "#", value: "id", sortable: false },
                { text: "image", value: "image", sortable: false },
                { text: "name", value: "full_name", sortable: false },
                { text: "title", value: "title", sortable: true },
                {
                    text: "wishes_count",
                    value: "wishes_count",
                    sortable: false,
                },
                { text: "description", value: "description", sortable: true },
                { text: "start_date", value: "start_date", sortable: true },
                { text: "start_time", value: "start_time", sortable: false },
                { text: "actions", value: "actions", sortable: false },
            ],
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                OccasionsApi.getOccasions(
                    this.form,
                    this.sorting,
                    this.pagination,
                    true,
                    true
                );
            }, 150),
        },
        sorting: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                OccasionsApi.getOccasions(
                    this.form,
                    this.sorting,
                    this.pagination,
                    true,
                    true
                );
            }, 150),
        },
        pagination: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                OccasionsApi.getOccasions(
                    this.form,
                    this.sorting,
                    this.pagination,
                    false,
                    true
                );
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
            this.sorting = mapValues(this.sorting, () => null);
            this.pagination = mapValues(this.pagination, () => null);
        },
        perPageChanged(value) {
            localStorage.per_page = value;
            this.pagination = { ...this.pagination, per_page: value, page: 1 };
        },
        updateSort(sortOptions) {
            this.sorting = {
                ...this.sorting,
                sortBy: sortOptions.sortBy,
                sortType: sortOptions.sortType,
            };
        },
        startSelected(d) {
            let formattedDate = formatDate(d);
            this.form = { ...this.form, start_date: formattedDate };
        },
    },
};
</script>
