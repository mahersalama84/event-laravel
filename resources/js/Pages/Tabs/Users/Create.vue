<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileInput from "@/Shared/FileInput.vue";
import TextInput from "@/Shared/TextInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
</script>
<template>
    <Head :title="__('users')['add_user']" />
    <AuthenticatedLayout>
        <h1 class="mb-8 text-3xl font-bold">
            <Link
                class="text-indigo-400 hover:text-indigo-600"
                :href="route('users.index')"
                >{{ __("users")["users"] }}</Link
            >
            <span class="text-indigo-400 font-medium">/</span>
            {{ __("forms")["add"] }}
        </h1>
        <div
            class="max-w-3xl bg-white rounded-md shadow overflow-hidden dark:bg-gray-400 dark:text-white"
        >
            <form @submit.prevent="store">
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
                        :label="__('forms')['password_confirm']"
                    />
                </div>
                <div
                    class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100"
                >
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("users")["add_user"] }}</loading-button
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
            form: useForm({
                first_name: "",
                last_name: "",
                email: "",
                password: "",
                password_confirmation: "",
                image: null,
            }),
        };
    },
    methods: {
        store() {
            this.form.post("/users");
        },
    },
};
</script>
