<script setup>
import { inject } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Shared/TextInput.vue";
import FileInput from "@/Shared/FileInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import ShowImages from "@/Shared/ShowImages.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import CustomersApi from "@/services/CustomersApi.ts";
import OccasionsApi from "@/services/OccasionsApi.ts";

const props = defineProps([
    "customer",
    "occasions",
    "bookedWishes",
    "locale",
    "host",
]);

const emitter = inject("emitter");
const deleteCustomer = () => {
    emitter.emit(
        "open-alert-modal",
        CustomersApi.getDeleteObject(props.customer, "edit")
    );
};
const deleteOccasion = (occasion) => {
    emitter.emit(
        "open-alert-modal",
        OccasionsApi.getDeleteObject(occasion, "edit-customer")
    );
};
const editOccasion = (occasion) => {
    OccasionsApi.editOccasion(occasion.id);
};
emitter.off("ok-delete-customers-edit");
emitter.on("ok-delete-customers-edit", (customer) => {
    console.log("ok-delete-customers-edit");
    let id = customer.id;
    router.delete(`/customers/${id}`);
});
emitter.off("ok-delete-occasions-edit-customer");
emitter.on("ok-delete-occasions-edit-customer", (occasion) => {
    console.log("ok-delete-occasions-edit-customer");
    CustomersApi.deleteOccasion(occasion.id).then(() => {
        router.reload();
    });
});
</script>
<template>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <AuthenticatedLayout>
        <ShowImages :imgs="imgs" :index="index" />
        <div class="flex justify-start mb-8 max-w-3xl">
            <h1 class="text-3xl font-bold">
                <Link
                    class="text-indigo-400 hover:text-indigo-600"
                    :href="route('customers.index')"
                    >{{ __("customers")["customers"] }}</Link
                >
                <span class="text-indigo-400 font-medium"> / </span>
                {{ form.first_name }} {{ form.last_name }}
            </h1>
        </div>
        <div class="max-w-3xl flex justify-center" v-if="customer.image">
            <img
                :src="customer.image"
                class="rounded-t-lg"
                width="64"
                :alt="customer.full_name"
            />
        </div>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <form @submit.prevent="updateCustomer">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input
                        v-model="form.first_name"
                        :error="form.errors.first_name"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['first_name']"
                    />
                    <text-input
                        v-model="form.last_name"
                        :error="form.errors.last_name"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['last_name']"
                    />
                    <text-input
                        v-model="form.prefix"
                        :error="form.errors.prefix"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['prefix']"
                    />
                    <text-input
                        v-model="form.mobile"
                        :error="form.errors.mobile"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['mobile']"
                    />
                    <text-input
                        v-model="form.email"
                        :error="form.errors.email"
                        class="pb-8 pr-6 w-full lg:w"
                        :label="__('forms')['email']"
                    />
                    <file-input
                        v-model="form.image"
                        :error="form.errors.image"
                        class="pb-8 pr-6 w-full lg:w"
                        type="file"
                        accept="image/*"
                        :label="__('forms')['image']"
                    />
                    <text-input
                        v-model="form.password"
                        :error="form.errors.password"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        type="password"
                        autocomplete="new-password"
                        :label="__('forms')['password']"
                    />
                    <text-input
                        v-model="form.password_confirmation"
                        :error="form.errors.password_confirmation"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        type="password"
                        autocomplete="new-password"
                        :label="__('forms')['password_confirm']"
                    />
                </div>
                <div
                    class="flex items-center justify-between px-8 py-4 bg-gray-50 dark:bg-gray-300 border-t border-gray-100 dark:border-gray-600"
                >
                    <button
                        class="text-red-600 dark:text-red-400 hover:underline"
                        tabindex="-1"
                        type="button"
                        @click="deleteCustomer"
                    >
                        {{ __("customers")["delete_customer"] }}
                    </button>
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("customers")["save_customer"] }}</loading-button
                    >
                </div>
            </form>
        </div>
        <h1 class="mt-10">
            {{ __("occasions")["occasions"] }}
        </h1>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <DataTable
                :show_per_page="false"
                :dir="locale == 'ar' ? 'rtl' : 'ltr'"
                :headers="occasions_headers"
                :paginate="occasions"
                :border-cell="true"
                :expand="false"
                sort-by="created_at"
                sort-type="desc"
                @editItem="editOccasion"
                @deleteItem="deleteOccasion"
            />
        </div>
        <h1 class="mt-10">
            {{ __("wishes")["booked_wishes"] }}
        </h1>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <DataTable
                :show_per_page="false"
                :dir="locale == 'ar' ? 'rtl' : 'ltr'"
                :headers="booked_wishes_headers"
                :paginate="bookedWishes"
                :border-cell="true"
                :expand="false"
                sort-by="created_at"
                sort-type="desc"
            />
        </div>
    </AuthenticatedLayout>
</template>
<script>
export default {
    data() {
        return {
            loadingIndex: false,
            index: null,
            form: useForm({
                _method: "put",
                first_name: this.customer.first_name,
                last_name: this.customer.last_name,
                prefix: this.customer.prefix,
                mobile: this.customer.mobile,
                email: this.customer.email,
                password: "",
                password_confirmation: "",
                image: null,
            }),
            occasions_headers: [
                // { text: "image", value: "image", sortable: false },
                // { text: "name", value: "full_name", sortable: false },
                { text: "title", value: "title", sortable: false },
                {
                    text: "wishes_count",
                    value: "wishes_count",
                    sortable: false,
                },
                { text: "description", value: "description", sortable: false },
                { text: "start_date", value: "start_date", sortable: false },
                { text: "start_time", value: "start_time", sortable: false },
                { text: "actions", value: "actions", sortable: false },
            ],
            booked_wishes_headers: [
                { text: "image", value: "image", sortable: false },
                { text: "name", value: "full_name", sortable: false },
                {
                    text: "occasion_title",
                    value: "occasion_title",
                    sortable: false,
                },
                {
                    text: "wish_title",
                    value: "wish_title",
                    sortable: false,
                },
                { text: "note", value: "note", sortable: false },
            ],
        };
    },
    computed: {
        imgs() {
            const arr = [];
            arr.push(this.host + this.customer.image);
            return arr;
        },
    },
    methods: {
        updateCustomer() {
            this.form.post(`/customers/${this.customer.id}`, {
                onSuccess: () => this.form.reset(),
            });
        },
    },
};
</script>
