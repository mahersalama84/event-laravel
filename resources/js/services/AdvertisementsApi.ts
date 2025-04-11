import { AdvertisementData } from "@/Types/AdvertisementData";
import type { Form, Pagination } from "@/Types/FormTypes.ts";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import pickBy from "lodash/pickBy";
import { useToast } from "vue-toast-notification";

const $toast = useToast();
const indexRoute = "advertisements.index";

export default {
    getAdvertisements(
        form: Form,
        pagination: Pagination,
        state: boolean,
        scroll: boolean
    ) {
        return router.get(
            route(indexRoute),
            pickBy({
                ...form,
                ...pagination,
            }),
            {
                preserveState: state,
                preserveScroll: scroll,
            }
        );
    },

    getDeleteObject(advertisement: AdvertisementData, page: string) {
        let delete_object = {
            useMode: "advertisements",
            page: page,
            title: "delete_advertisement",
            button: "delete",
            description: "delete_caution",
            data: advertisement,
        };
        return delete_object;
    },

    togglePublished(id: string) {
        axios
            .patch(`/advertisements/${id}/togglepublished`)
            .then((response) => {
                $toast.success(response.data.message);
            })
            .catch((error) => {
                $toast.error(error.message);
            });
    },
};
