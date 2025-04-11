import { router } from "@inertiajs/vue3";
import pickBy from "lodash/pickBy";
import type { Form, Sorting, Pagination } from "@/Types/FormTypes.ts";
import type { OccasionData } from "@/Types/OccasionTypes.ts";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const $toast = useToast();
const indexRoute = "occasions.index";
const searchCustomersRoute = "occasions.searchcustomer";

export default {
    getOccasions(
        form: Form,
        sorting: Sorting,
        pagination: Pagination,
        state: boolean,
        scroll: boolean
    ) {
        return router.get(
            route(indexRoute),
            pickBy({
                ...form,
                ...sorting,
                ...pagination,
            }),
            {
                preserveState: state,
                preserveScroll: scroll,
            }
        );
    },
    async getWishes(id: string) {
        let data = await axios
            .get(`/occasions/${id}/getwishes`)
            .then((response) => {
                return response.data;
            })
            .catch((error) => {
                $toast.error(error.message);
                return [];
            });
        return data;
    },
    async deleteWish(id: string) {
        return await axios
            .delete(`/occasions/deletewish/${id}`)
            .then((response) => {
                $toast.success(response.data.message);
            })
            .catch((error) => {
                $toast.error(error.message);
            });
    },
    async searchCustomers(filters: any) {
        return await axios
            .post(
                route(searchCustomersRoute),
                pickBy({
                    ...filters,
                })
            )
            .then((response) => {
                return response.data.customers;
            })
            .catch((error) => {
                $toast.error(error.message);
                return [];
            });
    },
    getDeleteObject(occasion: OccasionData, page: string) {
        let delete_object = {
            useMode: "occasions",
            page: page,
            title: "delete_occasion",
            button: "delete",
            description: "delete_caution",
            data: occasion,
        };
        return delete_object;
    },
    editOccasion(occasion_id: string) {
        return router.get(`/occasions/${occasion_id}`);
    },
};
