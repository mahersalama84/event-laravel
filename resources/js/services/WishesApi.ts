import type { WishData } from "@/Types/WishTypes.ts";

export default {
    getDeleteObject(wish: WishData, page: string) {
        let delete_object = {
            useMode: "wishes",
            page: page,
            title: "delete_wish",
            button: "delete",
            description: "delete_caution",
            data: wish,
        };
        return delete_object;
    },
};
