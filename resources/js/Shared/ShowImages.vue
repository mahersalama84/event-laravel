<script setup>
import VueEasyLightbox, { useEasyLightbox } from "vue-easy-lightbox";
import { watch } from "vue";

const props = defineProps(["imgs", "index"]);
watch(
    () => props.index,
    (newIndex, oldIndex) => {
        changeIndex(newIndex);
        show();
    }
);

const {
    // methods
    show,
    onHide,
    changeIndex,
    // refs
    visibleRef,
    indexRef,
    imgsRef,
} = useEasyLightbox({
    // src / src[]
    imgs: props.imgs,
    // initial index
    initIndex: props.index,
});
</script>

<template>
    <!-- <div>
        {{ indexRef }}
        <button @click="show">show</button> -->
    <vue-easy-lightbox
        move-disabled
        :visible="visibleRef"
        :imgs="imgsRef"
        :index="indexRef"
        @hide="onHide"
    />
    <!-- </div> -->
</template>
