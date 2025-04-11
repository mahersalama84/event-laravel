<script setup>
import { defineAsyncComponent, ref, inject } from "vue";
import Modal from "@/Components/Modal.vue";
import ModalTitle from "@/Components/ModalTitle.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import Spacer from "@/Components/Spacer.vue";
import WishCard from "@/Components/Cards/WishCard.vue";
import OccasionsApi from "@/services/OccasionsApi.ts";
import WishesApi from "@/services/WishesApi.ts";

const emit = defineEmits(["decreaseWishesCount"]);
const show = ref(false);
const item = ref(null);
const wishes = ref({});

const close = () => {
    item.value = null;
    wishes.value = {};
    show.value = false;
};
const fetchWishes = (occasion) => {
    show.value = true;
    if (occasion)
        OccasionsApi.getWishes(occasion.id).then((data) => {
            wishes.value = data;
        });
};
const emitter = inject("emitter");
const deleteWish = (wish) => {
    emitter.emit("open-alert-modal", WishesApi.getDeleteObject(wish, "modal"));
};

emitter.off("open-wishes-modal");
emitter.on("open-wishes-modal", (_occasion) => {
    console.log("open-wishes-modal");
    item.value = _occasion;
    fetchWishes(_occasion);
});
emitter.off("ok-delete-wishes-modal");
emitter.on("ok-delete-wishes-modal", (wish) => {
    console.log("ok-delete-wishes-modal");
    OccasionsApi.deleteWish(wish.id).then(() => {
        wishes.value.data = wishes.value.data.filter(
            (item) => item.id !== wish.id
        );
        emit("decreaseWishesCount", wish);
        if (wishes.value.data.length == 0) close();
    });
});
emitter.off("wish-updated");
emitter.on("wish-updated", (_wishes) => {
    console.log("wish-updated");
    wishes.value = _wishes;
});
</script>

<template>
    <div v-show="show">
        <Modal max-width="full" :show="show" @close="close" :closeable="true">
            <modal-title>
                <h3>
                    {{ __("wishes")["wishes"] }} / {{ item.full_name }} /
                    {{ item.title }}
                </h3>
                <Spacer :w-full="true" />
                <XMarkIcon
                    class="h-6 w-6 text-black cursor-pointer hover:text-indigo-600 dark:hover:text-gray-400"
                    @click="close"
                />
            </modal-title>
            <div
                v-if="wishes?.data?.length > 0"
                class="flex flex-wrap overflow-y-auto h-screen px-4 pt-4 pb-12"
            >
                <WishCard
                    v-for="wish in wishes.data"
                    :item="wish"
                    class="mx-2 my-2"
                    @deleteWish="deleteWish(wish)"
                />
            </div>
        </Modal>
    </div>
</template>
