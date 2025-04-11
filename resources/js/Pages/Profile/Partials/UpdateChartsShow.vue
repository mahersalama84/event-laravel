<script setup>
import Toggle from "@/Shared/Toggle.vue";
import { usePage, router } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";
const $toast = useToast();

const user = usePage().props.auth.user;
const toggle = (api) => {
    switch (api) {
        case "customers":
            axios
                .get("/profile/setshowcustomers")
                .then((response) => {
                    $toast.success(response.data.message);
                })
                .catch((error) => {
                    $toast.error(error.message);
                });
            break;
        case "users":
            axios
                .get("/profile/setshowusers")
                .then((response) => {
                    $toast.success(response.data.message);
                })
                .catch((error) => {
                    $toast.error(error.message);
                });
            break;
        default:
            break;
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("dashboard")["profile_info"] }}
            </h2>
        </header>
        <div class="flex">
            {{ __("customers")["customers"] }}
            <Toggle
                :id="user.id"
                :value="user.show_customers_stats"
                @toggle="toggle('customers')"
                class="mx-4"
            />
            {{ __("users")["users"] }}
            <Toggle
                :id="user.id"
                :value="user.show_users_stats"
                @toggle="toggle('users')"
                class="mx-4"
            />
        </div>
    </section>
</template>
