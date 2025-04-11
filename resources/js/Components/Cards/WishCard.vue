<script setup>
import Dropdown from "@/Shared/Dropdown.vue";
import { EllipsisVerticalIcon, UsersIcon } from "@heroicons/vue/24/solid";
import Spacer from "@/Components/Spacer.vue";
import { Link } from "@inertiajs/vue3";
import Avatar from "@/Shared/Avatar.vue";
import CustomersApi from "@/services/CustomersApi.ts";
const props = defineProps(["item"]);
const emit = defineEmits(["deleteWish"]);
const deleteWish = (item) => {
    emit("deleteWish", item.id);
};
const showCustomer = (customer) => {
    CustomersApi.editCustomer(customer.id);
};
</script>
<template>
    <div class="max-w-64 max-h-80 overflow-hidden shadow-lg dark:bg-gray-500">
        <img
            class="object-cover h-48 w-96 rounded-t-lg"
            :src="item.image"
            :alt="item.title"
        />
        <div class="px-4">
            <div
                class="flex justify-between font-bold text-xl my-2 dark:text-white"
            >
                {{ item.title }}
                <UsersIcon
                    v-if="item.customers?.length > 0"
                    class="w-6 h-6 fill-gray-700 dark:fill-white hover:fill-indigo-600 dark:group-hover:text-gray-400 focus:fill-indigo-600 dark:focus:text-gray-400"
                />
            </div>
            <p
                v-if="item.description"
                class="ellipsis-wrapper text-gray-700 text-base dark:text-white"
            >
                {{ item.description }}
            </p>
            <p
                v-else
                class="ellipsis-wrapper text-gray-700 text-base dark:text-white"
            >
                ---
            </p>
        </div>

        <div class="mb-4 px-2">
            <dropdown class="w-full flex flex-end">
                <template #default>
                    <Spacer :w-full="true" />
                    <div class="group">
                        <EllipsisVerticalIcon
                            class="w-6 h-6 fill-gray-700 dark:fill-white group-hover:fill-indigo-600 dark:group-hover:text-gray-400 focus:fill-indigo-600 dark:focus:text-gray-400"
                        />
                    </div>
                </template>
                <template #dropdown>
                    <div
                        class="mt-2 py-2 text-sm bg-white dark:text-white dark:bg-gray-400 rounded shadow-xl"
                    >
                        <div
                            v-if="item.customers?.length > 0"
                            class="flex px-6 py-2 font-bold"
                        >
                            {{ __("customers")["customers"] }} ({{
                                item.customers?.length
                            }})
                        </div>
                        <Link
                            v-if="item.customers?.length > 0"
                            v-for="customer in item.customers"
                            class="flex px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                            href="#"
                            preserve-scroll
                            preserve-state
                            @click.prevent="showCustomer(customer)"
                        >
                            <Avatar
                                :image="customer.image"
                                :name="customer.full_name"
                                imgWidth="20px"
                                class="ltr:mr-2 rtl:ml-2"
                            />
                            {{ customer.full_name }}
                        </Link>
                        <Link
                            class="block px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                            href="#"
                            preserve-scroll
                            preserve-state
                            @click.prevent="deleteWish(item)"
                            >{{ __("wishes")["delete_wish"] }}</Link
                        >
                    </div>
                </template>
            </dropdown>
        </div>
    </div>
</template>
