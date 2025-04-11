import { router } from "@inertiajs/vue3";
import pickBy from "lodash/pickBy";
import type { Form, Sorting, Pagination } from "@/Types/FormTypes.ts";
import type { CustomerData } from "@/Types/CustomerTypes.ts";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const $toast = useToast();
const indexRoute = "customers.index";

export default {
    getCustomers(
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
    async getOccasions(id: string) {
        let data = await axios
            .get(`/customers/${id}/getoccasions`)
            .then((response) => {
                return response.data;
            })
            .catch((error) => {
                $toast.error(error.message);
                return [];
            });
        return data;
    },
    async deleteOccasion(id: string) {
        return await axios
            .delete(`/customers/deleteoccasion/${id}`)
            .then((response) => {
                $toast.success(response.data.message);
            })
            .catch((error) => {
                $toast.error(error.message);
            });
    },
    getDeleteObject(customer: CustomerData, page: string) {
        let delete_object = {
            useMode: "customers",
            page: page,
            title: "delete_customer",
            button: "delete",
            description: "delete_caution",
            data: customer,
        };
        return delete_object;
    },
    editCustomer(customer_id: string) {
        return router.get(`/customers/${customer_id}`);
    },
    toggleIsActive(id: string) {
        axios
            .patch(`/customers/${id}/toggleactive`)
            .then((response) => {
                $toast.success(response.data.message);
            })
            .catch((error) => {
                $toast.error(error.message);
            });
    },
};
