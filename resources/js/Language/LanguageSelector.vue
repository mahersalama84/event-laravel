<template>
    <div class="mb-4">
        <a class="group icon-wrapper" :href="route('language', [selectable_locale])">
            <GlobeAltIcon class="icon-inactive group-hover:fill-white" />

            <!-- Check for selected language and display appropriate label -->
            <div v-if="selectable_locale == 'ar'" :class="classes">العربية</div>
            <div v-else-if="selectable_locale == 'sv'" :class="classes">SVENSKA</div>
            <div v-else :class="classes">ENGLISH</div>
        </a>
    </div>
</template>

<script setup>
import { GlobeAltIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import Icon from "@/Shared/Icon.vue";
</script>
<script>
export default {
    computed: {
        selectable_locale() {
            // Check the current locale and return the appropriate selectable locale
            if (this.$page.props.locale == "ar") {
                return "sv";  // If the current locale is Arabic, switch to English
            } else if (this.$page.props.locale == "sv") {
                return "en";  // If the current locale is Swedish, switch to English
            }
            return "ar";  // Default to Arabic if no other condition is met
        },
        classes() {
            return "text-inactive group-hover:text-white";
        },
    },
};

</script>
