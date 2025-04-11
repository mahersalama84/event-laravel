<script setup>
import { inject } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Shared/TextInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { formatDate, formatTimetoLacle } from "@/services/Functions.ts";
import OccasionsApi from "@/services/OccasionsApi.ts";
import WishesApi from "@/services/WishesApi.ts";

const props = defineProps(["occasion", "customer", "wishes", "locale", "host"]);

const emitter = inject("emitter");
const deleteOccasion = () => {
    emitter.emit(
        "open-alert-modal",
        OccasionsApi.getDeleteObject(props.occasion, "edit")
    );
};
const deleteWish = (wish) => {
    emitter.emit(
        "open-alert-modal",
        WishesApi.getDeleteObject(wish, "edit-occasion")
    );
};
const openWishModal = () => {
    emitter.emit("open-wish-modal", {
        customer: props.customer,
        occasion: props.occasion,
    });
};
const editWish = (wish) => {
    emitter.emit("open-wish-modal", {
        customer: props.customer,
        occasion: props.occasion,
        wish: wish,
    });
};

emitter.off("ok-delete-occasions-edit");
emitter.on("ok-delete-occasions-edit", (occasion) => {
    console.log("ok-delete-occasions-edit");
    router.delete(`/occasions/${occasion.id}`);
});
emitter.off("ok-delete-wishes-edit-occasion");
emitter.on("ok-delete-wishes-edit-occasion", (wish) => {
    console.log("ok-delete-wishes-edit-occasion");
    OccasionsApi.deleteWish(wish.id).then(() => {
        router.reload();
    });
});
</script>
<template>
    <Head :title="`${form.title}`" />
    <AuthenticatedLayout>
        <div class="flex justify-start mb-8 max-w-3xl">
            <h1 class="text-3xl font-bold">
                <Link
                    class="text-indigo-400 hover:text-indigo-600"
                    :href="route('occasions.index')"
                    >{{ __("occasions")["occasions"] }}</Link
                >
                <span class="text-indigo-400 font-medium"> / </span>
                <Link
                    class="text-indigo-400 hover:text-indigo-600"
                    :href="route('customers.edit', customer)"
                    >{{ customer.full_name }}</Link
                >

                <span class="text-indigo-400 font-medium"> / </span>
                {{ form.title }}
            </h1>
        </div>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <form @submit.prevent="updateOccasion">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input
                        v-model="customer.full_name"
                        class="pb-8 pr-6 w-full lg:w-full"
                        :label="__('forms')['customer']"
                        :disabled="true"
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
                        v-model="form.description"
                        :error="form.errors.description"
                        class="pb-8 pr-6 w-full lg:w-full"
                        :label="__('forms')['description']"
                        type="textarea"
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
                </div>
                <div
                    class="flex items-center justify-between px-8 py-4 bg-gray-50 dark:bg-gray-300 border-t border-gray-100 dark:border-gray-600"
                >
                    <button
                        class="text-red-600 dark:text-red-400 hover:underline"
                        tabindex="-1"
                        type="button"
                        @click="deleteOccasion"
                    >
                        {{ __("occasions")["delete_occasion"] }}
                    </button>
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("occasions")["save_occasion"] }}</loading-button
                    >
                </div>
            </form>
        </div>
        <div class="max-w-3xl flex items-center justify-between mb-6">
            <h1 class="mt-10">
                {{ __("wishes")["wishes"] }}
            </h1>
            <Link
                class="btn-indigo"
                as="button"
                preserve-scroll
                preserve-state
                href="#"
                @click.prevent="openWishModal"
            >
                <span>{{ __("forms")["add"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("wishes")["wish"] }}</span
                >
            </Link>
        </div>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <DataTable
                :show_per_page="false"
                :dir="locale == 'ar' ? 'rtl' : 'ltr'"
                :headers="headers"
                :paginate="wishes"
                :border-cell="true"
                :expand="false"
                sort-by="created_at"
                sort-type="desc"
                @editItem="editWish"
                @deleteItem="deleteWish"
            />
        </div>
    </AuthenticatedLayout>
</template>
<script>
export default {
    remember: "form",
    data() {
        return {
            loadingIndex: false,
            form: useForm({
                _method: "put",
                customer_id: this.occasion.customer_id,
                title: this.occasion.title,
                description: this.occasion.description,
                start_date: formatDate(this.occasion.start_date),
                start_time: formatTimetoLacle(this.occasion.start_date),
            }),
            headers: [
                { text: "image", value: "image", sortable: false },
                { text: "title", value: "title", sortable: false },
                { text: "description", value: "description", sortable: false },
                { text: "actions", value: "actions", sortable: false },
            ],
        };
    },
    methods: {
        updateOccasion() {
            this.form.post(`/occasions/${this.occasion.id}`, {
                onSuccess: () => this.form.reset(),
            });
        },
    },
};
</script>
