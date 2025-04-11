import { router } from "@inertiajs/vue3";
import pickBy from "lodash/pickBy";
import type { Form, Sorting, Pagination } from "@/Types/FormTypes.ts";
const indexRoute = "logs.index";

export default {
    getLogs(
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
};
