<script setup>
import { ref, inject } from "vue";
import Modal from "@/Components/Modal.vue";
import ModalTitle from "@/Components/ModalTitle.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import Spacer from "@/Components/Spacer.vue";
import { Link, useForm } from "@inertiajs/vue3";
import TextInput from "@/Shared/TextInput.vue";
import FileInput from "@/Shared/FileInput.vue";

const show = ref(false);
const wish_id = ref(null);
const form = useForm({
    occasion_id: null,
    title: null,
    description: null,
    image: null,
});
const customer = ref(null);
const occasion = ref(null);
const edit = ref(false);

const close = () => {
    form.reset();
    customer.value = null;
    occasion.value = null;
    edit.value = false;
    wish_id.value = null;
    show.value = false;
};
const addWish = () => {
    form.post(route("wishes.create"), {
        onSuccess: () => close(),
    });
};
const saveWish = () => {
    form.post(`/wishes/${wish_id.value}`, {
        onSuccess: (response) => {
            let data = response.props.wishes;
            emitter.emit("wish-updated", data);
            close();
        },
    });
};
const emitter = inject("emitter");
emitter.off("open-wish-modal");
emitter.on("open-wish-modal", (data) => {
    if (data.wish) {
        edit.value = true;
        wish_id.value = data.wish.id;
        form.title = data.wish.title;
        form.description = data.wish.description;
    }
    customer.value = data.customer;
    occasion.value = data.occasion;
    form.occasion_id = data.occasion.id;

    show.value = true;
});
</script>

<template>
    <div v-show="show">
        <Modal max-width="2xl" :show="show" @close="close" :closeable="true">
            <modal-title>
                <h3>{{ customer?.full_name }} / {{ occasion?.title }}</h3>
                <Spacer :w-full="true" />
                <XMarkIcon
                    class="h-6 w-6 text-black cursor-pointer hover:text-indigo-600 dark:hover:text-gray-400"
                    @click="close"
                />
            </modal-title>
            <div class="flex flex-wrap overflow-y-auto px-4 pt-4 pb-12">
                <div
                    class="max-w-3xl bg-white dark:bg-gray-400 dark:text-white rounded-md shadow overflow-hidden"
                >
                    <form>
                        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                            <input
                                v-if="edit"
                                v-model="wish_id"
                                :error="form.errors.id"
                                class="pb-8 pr-6 w-full"
                                :label="__('forms')['id']"
                                type="hidden"
                            />
                            <input
                                v-model="form.occasion_id"
                                :error="form.errors.occasion_id"
                                class="pb-8 pr-6 w-full"
                                :label="__('forms')['occasion_id']"
                                type="hidden"
                            />
                            <text-input
                                v-model="form.title"
                                :error="form.errors.title"
                                class="pb-8 pr-6 w-full"
                                :label="__('forms')['title']"
                                autofocus
                            />
                            <file-input
                                v-model="form.image"
                                :error="form.errors.image"
                                class="pb-8 pr-6 w-full"
                                type="file"
                                accept="image/*"
                                :label="__('forms')['image']"
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
                            class="flex items-center justify-end py-4 bg-gray-50 dark:bg-gray-300 border-t border-gray-100 dark:border-gray-600"
                        >
                            <Link
                                href="#"
                                v-if="!edit"
                                as="button"
                                class="btn-indigo"
                                @click="addWish"
                            >
                                {{ __("wishes")["add_wish"] }}
                            </Link>
                            <Link
                                href="#"
                                v-if="edit"
                                as="button"
                                class="btn-indigo"
                                @click="saveWish"
                            >
                                {{ __("wishes")["update_wish"] }}
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </div>
</template>
