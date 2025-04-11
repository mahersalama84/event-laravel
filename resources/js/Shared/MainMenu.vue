<script setup>
import DarkSelector from "@/DarkSelector/DarkSelector.vue";
import LanguageSelector from "@/Language/LanguageSelector.vue";
import {
    HomeIcon,
    PhotoIcon,
    UserIcon,
    UsersIcon,
} from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div>
        <div class="mb-4" v-for="tab in tabs">
            <Link class="group icon-wrapper" :href="route(tab.url)">
                <component
                    :is="tab.icon"
                    :class="
                        isUrl(tab.text)
                            ? 'icon-active'
                            : 'icon-inactive group-hover:fill-white dark:group-hover:fill-indigo-400'
                    "
                />
                <div
                    :class="
                        isUrl(tab.text)
                            ? 'text-active'
                            : 'text-inactive group-hover:text-white dark:group-hover:text-indigo-300'
                    "
                >
                    {{ __(tab.text)[tab.text] }}
                </div>
            </Link>
        </div>
        <language-selector />
        <dark-selector />
    </div>
</template>

<script>
export default {
    data() {
        return {
            tabs: [
                { icon: HomeIcon, url: "dashboard", text: "dashboard" },
                { icon: UserIcon, url: "users.index", text: "users" },
                { icon: UsersIcon, url: "customers.index", text: "customers" },
                {
                    icon: UsersIcon,
                    url: "occasions.index",
                    text: "occasions",
                },
                {
                    icon: PhotoIcon,
                    url: "advertisements.index",
                    text: "advertisements",
                },
                {
                    icon: PhotoIcon,
                    url: "logs.index",
                    text: "logs",
                },
            ],
        };
    },
    methods: {
        isUrl(...urls) {
            let currentUrl = this.$page.url.substr(1);
            if (urls[0] === "") {
                return currentUrl === "";
            }
            return urls.filter((url) => currentUrl.startsWith(url)).length;
        },
    },
};
</script>
