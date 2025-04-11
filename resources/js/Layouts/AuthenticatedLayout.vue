<script setup>
import { Link } from "@inertiajs/vue3";
import Logo from "@/Shared/Logo.vue";
import Dropdown from "@/Shared/Dropdown.vue";
import MainMenu from "@/Shared/MainMenu.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import { ChevronDownIcon, Bars3Icon } from "@heroicons/vue/24/solid";
</script>

<template>
    <div>
        <div id="dropdown" />
        <div class="lg:flex lg:flex-col">
            <div class="lg:flex lg:flex-col lg:h-screen">
                <div class="lg:flex lg:flex-shrink-0">
                    <div
                        class="flex items-center justify-between px-6 py-4 bg-indigo-900 lg:flex-shrink-0 lg:justify-center lg:w-56"
                    >
                        <Link class="mt-1" href="/">
                            <logo class="fill-white" width="120" height="28" />
                        </Link>
                        <dropdown class="lg:hidden" placement="bottom-end">
                            <template #default>
                                <!-- <svg
                                    class="w-6 h-6 fill-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"
                                    />
                                </svg> -->
                                <Bars3Icon
                                    class="w-6 h-6 fill-white dark:fill-white hover:fill-indigo-600 group-hover:text-gray-400 focus:fill-indigo-600 dark:focus:text-gray-400"
                                />
                            </template>
                            <template #dropdown>
                                <div
                                    class="mt-2 px-8 py-4 bg-indigo-800 rounded shadow-lg"
                                >
                                    <main-menu />
                                </div>
                            </template>
                        </dropdown>
                    </div>
                    <div
                        class="lg:text-md flex items-center justify-between p-4 w-full text-sm bg-white dark:bg-gray-500 lg:px-12 lg:py-0"
                    >
                        <div class="lrt:mr-4 rtl:ml-4 mt-1 dark:text-white">
                            {{ $page.props.auth.user.full_name }}
                        </div>
                        <dropdown class="mt-1" placement="bottom-end">
                            <template #default>
                                <div
                                    class="group flex items-center cursor-pointer select-none"
                                >
                                    <div
                                        class="mr-1 text-gray-700 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-gray-400 focus:text-indigo-600 dark:focus:text-gray-400 whitespace-nowrap"
                                    >
                                        <span>{{
                                            $page.props.auth.user.full_name
                                        }}</span>
                                    </div>
                                    <ChevronDownIcon
                                        class="w-4 h-4 fill-gray-700 dark:fill-white group-hover:fill-indigo-600 dark:group-hover:text-gray-400 focus:fill-indigo-600 dark:focus:text-gray-400"
                                        name="cheveron-down"
                                    />
                                </div>
                            </template>
                            <template #dropdown>
                                <div
                                    class="mt-2 py-2 text-sm bg-white dark:text-white dark:bg-gray-400 rounded shadow-xl"
                                >
                                    <Link
                                        class="block px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                                        :href="route('profile.edit')"
                                        >{{ __("dashboard")["profile"] }}</Link
                                    >
                                    <Link
                                        class="block px-6 py-2 hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                                        href="/users"
                                        >{{ __("users")["users"] }}</Link
                                    >
                                    <Link
                                        class="block px-6 py-2 text-left hover:text-white dark:hover:text-gray-200 hover:bg-indigo-500 dark:hover:bg-gray-500"
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        >{{ __("dashboard")["logout"] }}</Link
                                    >
                                </div>
                            </template>
                        </dropdown>
                    </div>
                </div>
                <div class="main-container">
                    <main-menu class="main-menu" />

                    <div class="secondery-container" scroll-region>
                        <!-- Page Heading -->
                        <header v-if="$slots.header">
                            <h1>
                                <slot name="header" />
                            </h1>
                        </header>
                        <flash-messages />
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
