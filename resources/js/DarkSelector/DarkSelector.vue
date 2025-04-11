<script setup>
import { useDark, useToggle } from "@vueuse/core";
import { SunIcon, MoonIcon } from "@heroicons/vue/24/solid";

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <div class="mb-4">
        <a class="group icon-wrapper" @click="toggleDark()" href="#">
            <SunIcon
                v-if="isDark"
                class="icon-inactive group-hover:fill-white"
            />
            <MoonIcon v-else class="icon-inactive group-hover:fill-white" />

            <div v-if="isDark" class="text-inactive group-hover:text-white">
                {{ __("dashboard")["dark"] }}
            </div>
            <div v-else class="text-inactive group-hover:text-white">
                {{ __("dashboard")["light"] }}
            </div>
        </a>
    </div>
</template>
