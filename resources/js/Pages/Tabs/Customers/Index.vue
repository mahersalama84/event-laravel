<script setup>
import { inject } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import SearchFilter from "@/Shared/SearchFilter.vue";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";
import customersApi from "@/services/CustomersApi.ts";

const props = defineProps(["paginate", "locale", "filters", "sorts", "host"]);
const emitter = inject("emitter");
const editCustomer = (customer) => {
    customersApi.editCustomer(customer.id);
};
const deleteCustomer = (customer) => {
    emitter.emit(
        "open-alert-modal",
        customersApi.getDeleteObject(customer, "index")
    );
};
emitter.off("ok-delete-customers-index");
emitter.on("ok-delete-customers-index", (customer) => {
    console.log("ok-delete-customers-index");
    router.delete(`/customers/${customer.id}`);
});
</script>

<template>
    <Head :title="__('customers')['customers']" />

    <AuthenticatedLayout>
        <template #header>
            {{ __("customers")["customers"] }}
        </template>
        <div class="flex items-center justify-between mb-6">
            <search-filter
                v-model="form.search"
                class="mr-4 w-full max-w-md"
                @reset="reset"
            >
                <label class="block text-gray-700">{{
                    __("forms")["role"]
                }}</label>
                <select v-model="form.role" class="form-select mt-1 w-full">
                    <option :value="null" />
                    <option value="admin">{{ __("forms")["admin"] }}</option>
                    <option value="customer">
                        {{ __("forms")["customer"] }}
                    </option>
                </select>
                <label class="block mt-5 text-gray-700">{{
                    __("forms")["is_active"]
                }}</label>
                <select
                    v-model="form.is_active"
                    class="form-select mt-1 w-full"
                >
                    <option :value="null" />
                    <option value="active">
                        {{ __("customers")["active"] }}
                    </option>
                    <option value="inactive">
                        {{ __("customers")["inactive"] }}
                    </option>
                </select>
                <label class="block mt-5 text-gray-700">{{
                    __("forms")["prefix"]
                }}</label>
                <select v-model="form.prefix" class="form-select mt-1 w-full">
                    <option :value="null" />
                    <option value="971">
                        {{ __("forms")["uae"] }}
                    </option>
                    <option value="46">
                        {{ __("forms")["sweden"] }}
                    </option>
                </select>
            </search-filter>
            <Link class="btn-indigo" :href="route('customers.create')">
                <span>{{ __("forms")["add"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("customers")["customer"] }}</span
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
            @editItem="editCustomer"
            @deleteItem="deleteCustomer"
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
                is_active: this.filters?.is_active,
                prefix: this.filters?.prefix,
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
                { text: "is_active", value: "is_active", sortable: false },
                { text: "name", value: "full_name", sortable: true },
                { text: "mobile", value: "mobile_no", sortable: true },
                { text: "email", value: "email", sortable: true },
                {
                    text: "occasions_count",
                    value: "occasions_count",
                    sortable: false,
                },
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
                customersApi.getCustomers(
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
                customersApi.getCustomers(
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
                customersApi.getCustomers(
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
