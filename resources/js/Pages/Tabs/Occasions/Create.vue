<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Shared/TextInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import AutocompleteInput from "@/Shared/AutocompleteInput.vue";
import throttle from "lodash/throttle";
import OccasionsApi from "@/services/OccasionsApi.ts";
</script>
<template>
    <Head :title="__('occasions')['add_occasion']" />
    <AuthenticatedLayout>
        <h1 class="mb-8 text-3xl font-bold">
            <Link
                class="text-indigo-400 hover:text-indigo-600"
                :href="route('occasions.index')"
                >{{ __("occasions")["occasions"] }}</Link
            >
            <span class="text-indigo-400 font-medium">/</span>
            {{ __("forms")["add"] }}
        </h1>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <form @submit.prevent="store">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <AutocompleteInput
                        :searchItems="customers"
                        v-model="filters.search"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['customer']"
                        @itemSelected="customerSelected"
                    />

                    <input
                        v-model="form.customer_id"
                        :error="form.errors.customer_id"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['customer_id']"
                        type="hidden"
                    />
                    <text-input
                        v-model="form.title"
                        :error="form.errors.title"
                        class="pb-8 pr-6 w-full lg:w-full"
                        :label="__('forms')['title']"
                    />
                    <text-input
                        v-model="form.start_date"
                        :error="form.errors.start_date"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['start_date']"
                        type="date"
                    />
                    <text-input
                        v-model="form.start_time"
                        :error="form.errors.start_time"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['start_time']"
                        type="time"
                    />
                    <text-input
                        v-model="form.description"
                        :error="form.errors.description"
                        class="pb-8 pr-6 w-full lg:w-full"
                        :label="__('forms')['description']"
                        type="textarea"
                    />
                </div>
                <div
                    class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100"
                >
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("occasions")["add_occasion"] }}</loading-button
                    >
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            customers: [],
            selectedCustomer: null,
            filters: {
                search: this.filters?.search,
            },
            form: useForm({
                customer_id: "",
                title: "",
                description: "",
                start_date: "",
                start_time: "",
            }),
        };
    },
    watch: {
        filters: {
            deep: true,
            handler: throttle(function () {
                if (this.filters.search === "" || this.filters.search == null) {
                    return [];
                }
                OccasionsApi.searchCustomers(this.filters)
                    .then((customers) => {
                        this.customers = customers;
                    })
                    .catch((error) => {
                        this.$toast.error(error.message);
                    });
            }, 150),
        },
    },
    methods: {
        store() {
            this.form.post("/occasions");
        },
        customerSelected(customer) {
            this.selectedCustomer = customer;
            this.form.customer_id = customer.id;
            this.filters.search = customer.full_name;
        },
    },
};
</script>
