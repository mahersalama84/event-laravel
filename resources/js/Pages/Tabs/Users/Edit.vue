<script setup>
import { inject, defineAsyncComponent } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Shared/TextInput.vue";
import FileInput from "@/Shared/FileInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import ShowImages from "@/Shared/ShowImages.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UsersApi from "@/services/UsersApi.ts";

const props = defineProps(["user", "host"]);
const AlertModal = defineAsyncComponent(() =>
    import("@/Components/Modals/AlertModal.vue")
);

const emitter = inject("emitter");
const deleteUser = () => {
    emitter.emit(
        "open-alert-modal",
        UsersApi.getDeleteObject(props.user, "edit")
    );
};

emitter.off("ok-delete-users-edit");
emitter.on("ok-delete-users-edit", (data) => {
    console.log("ok-delete-users-edit");
    let id = data.id;
    router.delete(`/users/${id}`);
});
</script>
<template>
    <AlertModal />
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <AuthenticatedLayout>
        <ShowImages :imgs="imgs" :index="index" />
        <div class="flex justify-start mb-8 max-w-3xl">
            <h1 class="text-3xl font-bold">
                <Link
                    class="text-indigo-400 hover:text-indigo-600"
                    :href="route('users.index')"
                    >{{ __("users")["users"] }}</Link
                >
                <span class="text-indigo-400 font-medium"> / </span>
                {{ form.first_name }} {{ form.last_name }}
            </h1>
        </div>
        <div class="max-w-3xl flex justify-center" v-if="user.image">
            <img
                :src="user.image"
                class="rounded-t-lg"
                width="64"
                :alt="user.full_name"
            />
        </div>
        <div
            class="max-w-3xl bg-white rounded-md shadow overflow-hidden dark:bg-gray-400 dark:text-white"
        >
            <form @submit.prevent="updateUser">
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
                        v-model="form.email"
                        :error="form.errors.email"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        :label="__('forms')['email']"
                    />
                    <file-input
                        v-model="form.image"
                        :error="form.errors.image"
                        class="pb-8 pr-6 w-full lg:w-1/2"
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
                    class="flex items-center justify-between px-8 py-4 bg-gray-50 dark:bg-gray-300 border-t border-gray-100"
                >
                    <button
                        class="text-red-600 dark:text-red-400 hover:underline"
                        tabindex="-1"
                        type="button"
                        @click="deleteUser"
                    >
                        {{ __("users")["delete_user"] }}
                    </button>
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("users")["save_user"] }}</loading-button
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
            index: null,
            form: useForm({
                _method: "put",
                first_name: this.user.first_name,
                last_name: this.user.last_name,
                email: this.user.email,
                password: "",
                password_confirmation: "",
                image: null,
            }),
        };
    },
    computed: {
        imgs() {
            const arr = [];
            arr.push(this.host + this.user.image);
            return arr;
        },
    },
    methods: {
        updateUser() {
            this.form.post(`/users/${this.user.id}`, {
                onSuccess: () => this.form.reset(),
            });
        },
    },
};
</script>
