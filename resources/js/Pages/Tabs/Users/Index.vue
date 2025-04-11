<script setup>
import { inject } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import SearchFilter from "@/Shared/SearchFilter.vue";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";
import usersApi from "@/services/UsersApi.ts";

const props = defineProps(["paginate", "locale", "filters", "sorts", "host"]);

const emitter = inject("emitter");
const editUser = (user) => {
    usersApi.editUser(user.id);
};
const deleteUser = (user) => {
    emitter.emit("open-alert-modal", usersApi.getDeleteObject(user, "index"));
};
emitter.off("ok-delete-users-index");
emitter.on("ok-delete-users-index", (user) => {
    console.log("ok-delete-users-index");
    router.delete(`/users/${user.id}`);
});
</script>

<template>
    <Head :title="__('users')['users']" />

    <AuthenticatedLayout>
        <template #header>
            {{ __("users")["users"] }}
        </template>
        <div class="filter-container">
            <search-filter
                v-model="form.search"
                class="filter-main"
                @reset="reset"
            >
                <label class="block text-gray-700 dark:text-white">{{
                    __("forms")["role"]
                }}</label>
                <select
                    v-model="form.role"
                    class="form-select mt-1 w-full dark:bg-gray-400"
                >
                    <option :value="null" />
                    <option value="admin" class="dark:text-white">
                        {{ __("forms")["admin"] }}
                    </option>
                    <option value="customer" class="dark:text-white">
                        {{ __("forms")["customer"] }}
                    </option>
                </select>
            </search-filter>
            <Link class="btn-indigo" :href="route('users.create')">
                <span>{{ __("forms")["add"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("users")["user"] }}</span
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
            @editItem="editUser"
            @deleteItem="deleteUser"
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
                role: this.filters?.role,
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
                { text: "name", value: "full_name", sortable: true },
                { text: "email", value: "email", sortable: true },
                { text: "role", value: "role", sortable: false },
                { text: "actions", value: "actions", sortable: false },
            ],
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                usersApi.getUsers(
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
                usersApi.getUsers(
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
                usersApi.getUsers(
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
