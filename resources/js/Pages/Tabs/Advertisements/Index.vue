<script setup>
import DataTable from "@/Components/DataTable.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import advertisementsApi from "@/services/AdvertisementsApi.ts";
import SearchFilter from "@/Shared/SearchFilter.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import mapValues from "lodash/mapValues";
import throttle from "lodash/throttle";
import { inject } from "vue";

const props = defineProps(["paginate", "locale"]);
const emitter = inject("emitter");

const deleteAdvertisement = (advertisement) => {
    emitter.emit(
        "open-alert-modal",
        advertisementsApi.getDeleteObject(advertisement, "index")
    );
};

emitter.off("ok-delete-advertisements-index");
emitter.on("ok-delete-advertisements-index", (advertisement) => {
    console.log("ok-delete-advertisements-index");
    router.delete(`/advertisements/${advertisement.id}`);
});
</script>

<template>
    <Head :title="__('advertisements')['advertisements']" />

    <AuthenticatedLayout>
        <template #header>
            {{ __("advertisements")["advertisements"] }}
        </template>
        <div class="flex items-center justify-between mb-6">
            <search-filter class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block mt-5 text-gray-700">{{
                    __("forms")["published"]
                }}</label>
                <select
                    v-model="form.published"
                    class="form-select mt-1 w-full"
                >
                    <option :value="null" />
                    <option value="published">
                        {{ __("advertisements")["published"] }}
                    </option>
                    <option value="hidden">
                        {{ __("advertisements")["hidden"] }}
                    </option>
                </select>
            </search-filter>
            <Link class="btn-indigo" :href="route('advertisements.create')">
                <span>{{ __("forms")["add"] }}</span>
                <span class="hidden md:inline"
                    >&nbsp;{{ __("advertisements")["advertisement"] }}</span
                >
            </Link>
        </div>
        <DataTable
            :dir="locale == 'ar' ? 'rtl' : 'ltr'"
            :headers="headers"
            :paginate="paginate"
            :border-cell="true"
            :expand="false"
            sort-by="created_at"
            sort-type="desc"
            :loading-index="loadingIndex"
            :edit-button="false"
            @deleteItem="deleteAdvertisement"
            @perPageChanged="perPageChanged"
        />
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            loadingIndex: false,
            form: {
                published: this.filters?.published,
            },
            pagination: {
                page: this.paginate?.current_page,
                per_page: this.paginate?.per_page,
            },
            headers: [
                { text: "image", value: "image", sortable: false },
                { text: "published", value: "published", sortable: false },
                { text: "actions", value: "actions", sortable: false },
            ],
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                advertisementsApi.getAdvertisements(
                    this.form,
                    this.pagination,
                    true,
                    true
                );
            }, 150),
        },
        pagination: {
            deep: true,
            handler: throttle(function () {
                this.loadingIndex = true;
                advertisementsApi.getAdvertisements(
                    this.form,
                    this.pagination,
                    false,
                    true
                );
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
            this.pagination = mapValues(this.pagination, () => null);
        },
        perPageChanged(value) {
            localStorage.per_page = value;
            this.pagination = { ...this.pagination, per_page: value, page: 1 };
        },
    },
};
</script>
