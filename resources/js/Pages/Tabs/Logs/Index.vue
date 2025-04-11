<script setup>
import DataTable from "@/Components/DataTable.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import logsApi from "@/services/LogsApi";
import SearchFilter from "@/Shared/SearchFilter.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import mapValues from "lodash/mapValues";
import throttle from "lodash/throttle";

const props = defineProps(["paginate", "locale", "filters", "sorts", "host"]);
</script>

<template>
    <Head :title="__('logs')['logs']" />

    <AuthenticatedLayout>
        <template #header>
            {{ __("logs")["logs"] }}
        </template>
        <div class="flex items-center justify-between mb-6">
            <search-filter
                v-model="form.search"
                class="mr-4 w-full max-w-md"
                @reset="reset"
            >
                <label class="block mt-5 text-gray-700">{{
                    __("forms")["log_name"]
                }}</label>
                <select v-model="form.log_name" class="form-select mt-1 w-full">
                    <option :value="null" />
                    <option value="user">
                        {{ __("users")["user"] }}
                    </option>
                    <option value="Customer">
                        {{ __("customers")["customer"] }}
                    </option>
                    <option value="Occasion">
                        {{ __("occasions")["occasion"] }}
                    </option>
                    <option value="Wish">
                        {{ __("wishes")["wish"] }}
                    </option>
                </select>
            </search-filter>
            <Link class="btn-indigo" :href="route('logs.clear')">
                <span>{{ __("forms")["clear"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("logs")["logs"] }}</span
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
            :edit-button="false"
            @perPageChanged="perPageChanged"
            @updateSort="updateSort"
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
                log_name: this.filters?.log_name,
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
                { text: "log_name", value: "log_name", sortable: true },
                { text: "event", value: "event", sortable: true },
                { text: "causer_name", value: "causer_name", sortable: false },
                {
                    text: "subject_name",
                    value: "subject_name",
                    sortable: false,
                },
                { text: "description", value: "description", sortable: false },
                // { text: "causer_type", value: "causer_type", sortable: false },
                // {
                //     text: "subject_type",
                //     value: "subject_type",
                //     sortable: false,
                // },

                {
                    text: "changes",
                    value: "changes",
                    sortable: false,
                },
                {
                    text: "created_at",
                    value: "created_at",
                    sortable: true,
                },
            ],
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                logsApi.getLogs(
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
                logsApi.getLogs(
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
                logsApi.getLogs(
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
    },
};
</script>
