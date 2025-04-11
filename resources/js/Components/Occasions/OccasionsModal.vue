<script setup>
import { inject, ref } from "vue";
import Modal from "@/Components/Modal.vue";
import ModalTitle from "@/Components/ModalTitle.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import Spacer from "@/Components/Spacer.vue";
import OccasionCard from "@/Components/Cards/OccasionCard.vue";
import CustomersApi from "@/services/CustomersApi.ts";
import OccasionsApi from "@/services/OccasionsApi.ts";

const emit = defineEmits(["decreaseOccasionsCount"]);

const show = ref(false);
const customer = ref(null);
const occasions = ref({});

const emitter = inject("emitter");
const close = () => {
    customer.value = null;
    occasions.value = {};
    show.value = false;
};
const deleteOccasion = (occasion) => {
    emitter.emit(
        "open-alert-modal",
        OccasionsApi.getDeleteObject(occasion, "modal")
    );
};
const decreaseWishesCount = (wish) => {
    occasions.value.data.filter((item) => item.id == wish.occasion_id)[0]
        .wishes_count--;
};
emitter.off("open-occasions-modal");
emitter.on("open-occasions-modal", (item) => {
    console.log("open-occasions-modal");
    show.value = true;
    customer.value = item;
    CustomersApi.getOccasions(item.id).then((data) => {
        occasions.value = data;
    });
});
emitter.off("ok-delete-occasions-modal");
emitter.on("ok-delete-occasions-modal", (occasion) => {
    console.log("ok-delete-occasions-modal");
    CustomersApi.deleteOccasion(occasion.id).then(() => {
        occasions.value.data = occasions.value.data.filter(
            (item) => item.id !== occasion.id
        );
        emit("decreaseOccasionsCount", occasion);
        if (occasions.value.data.length == 0) close();
    });
});
</script>

<template>
    <div v-show="show">
        <Modal max-width="full" :show="show" @close="close" :closeable="true">
            <modal-title>
                <h3>
                    {{ __("occasions")["occasions"] }} /
                    {{ customer.full_name }}
                </h3>
                <Spacer :w-full="true" />
                <XMarkIcon
                    class="h-6 w-6 text-black cursor-pointer hover:text-indigo-600 dark:hover:text-gray-400"
                    @click="close"
                />
            </modal-title>
            <div
                v-if="occasions?.data?.length > 0"
                class="flex flex-wrap overflow-y-auto h-screen px-4 pt-4 pb-12"
            >
                <OccasionCard
                    v-for="occasion in occasions.data"
                    :key="occasion.id"
                    :item="occasion"
                    class="mx-2 my-2"
                    @deleteOccasion="deleteOccasion(occasion)"
                    @decreaseWishesCount="decreaseWishesCount"
                />
            </div>
        </Modal>
    </div>
</template>
