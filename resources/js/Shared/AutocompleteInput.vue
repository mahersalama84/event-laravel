<template>
    <div :class="$attrs.class">
        <label v-if="label" class="form-label" :for="id">{{ label }}:</label>

        <input
            :id="id"
            ref="input"
            v-bind="{ ...$attrs, class: null }"
            class="form-input"
            :class="{ error: error }"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <ul
            v-if="searchItems.length && modelValue"
            class="w-1/2 rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10"
        >
            <li class="px-1 pt-1 pb-2 font-bold border-b border-gray-200">
                Showing {{ searchItems.length }} of
                {{ searchItems.length }} results
            </li>
            <li
                v-for="item in searchItems"
                @click="$emit('itemSelected', item)"
                :key="item.full_name"
                class="cursor-pointer hover:bg-gray-100 p-1"
            >
                {{ item.full_name }}
            </li>
        </ul>

        <!-- <p v-if="selectedCountry" class="text-lg pt-2 absolute">
            You have selected:
            <span class="font-semibold">{{ selectedCountry }}</span>
        </p> -->
        <div v-if="error" class="form-error">{{ error }}</div>
    </div>
</template>
<script>
import { v4 as uuid } from "uuid";

export default {
    inheritAttrs: false,
    props: {
        id: {
            type: String,
            default() {
                return `text-input-${uuid()}`;
            },
        },
        type: {
            type: String,
            default: "text",
        },
        searchItems: {
            type: Array,
            default: [],
        },
        error: String,
        label: String,
        modelValue: String,
    },
    emits: ["update:modelValue", "itemSelected"],
    methods: {
        focus() {
            this.$refs.input.focus();
        },
        select() {
            this.$refs.input.select();
        },
        setSelectionRange(start, end) {
            this.$refs.input.setSelectionRange(start, end);
        },
    },
};
</script>
