import { router } from "@inertiajs/vue3";
import pickBy from "lodash/pickBy";
import type { Form, Sorting, Pagination } from "@/Types/FormTypes.ts";
const indexRoute = "users.index";

export default {
    getUsers(
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
    getDeleteObject(user: any, page: string) {
        let delete_object = {
            useMode: "users",
            page: page,
            title: "delete_user",
            button: "delete",
            description: "delete_caution",
            data: user,
        };
        return delete_object;
    },
    editUser(user_id: string) {
        return router.get(`/users/${user_id}/edit`);
    },
};
