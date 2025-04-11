<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileInput from "@/Shared/FileInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
</script>

<template>
    <Head :title="__('advertisements')['add']" />
    <AuthenticatedLayout>
        <h1 class="mb-8 text-3xl font-bold">
            <Link
                class="text-indigo-400 hover:text-indigo-600"
                :href="route('advertisements.index')"
                >{{ __("advertisements")["advertisements"] }}</Link
            >
            <span class="text-indigo-400 font-medium">/</span>
            {{ __("forms")["add"] }}
        </h1>
        <div
            class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
        >
            <form @submit.prevent="store">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <file-input
                        v-model="form.image"
                        :error="form.errors.image"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        type="file"
                        accept="image/*"
                        :label="__('forms')['image']"
                    />
                </div>
                <div
                    class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100"
                >
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >{{ __("advertisements")["add"] }}</loading-button
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
                image: null,
            }),
        };
    },
    methods: {
        store() {
            this.form.post("/advertisements");
        },
    },
};
</script>
