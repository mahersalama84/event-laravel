import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import mitt from "mitt";
import VueEasyLightbox from "vue-easy-lightbox";
import "vue3-easy-data-table/dist/style.css";
import Vue3EasyDataTable from "vue3-easy-data-table";
import ToastPlugin from "vue-toast-notification";
import "vue-toast-notification/dist/theme-bootstrap.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

const emitter = mitt();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob(["./Pages/**/*.vue", "./Pages/**/**/*.vue"])
        ),
    setup({ el, App, props, plugin }) {
        const VueApp = createApp({ render: () => h(App, props) });
        // VueApp.config.globalProperties.emitter = emitter;

        VueApp.provide("emitter", emitter);
        VueApp.use(plugin)
            .mixin({
                methods: {
                    __(key, replace = {}) {
                        var translation = this.$page.props.language[key]
                            ? this.$page.props.language[key]
                            : key;

                        Object.keys(replace).forEach(function (key) {
                            translation = translation.replace(
                                ":" + key,
                                replace[key]
                            );
                        });

                        return translation;
                    },
                },
            })
            .use(ZiggyVue)
            .use(VueEasyLightbox)
            .use(ToastPlugin, {
                position: "bottom-right",
                dismissable: true,
                duration: 2000,
                queue: false,
            })
            .component("EasyDataTable", Vue3EasyDataTable)
            .mount(el);
    },
    progress: {
        // The delay after which the progress bar will appear, in milliseconds...
        delay: 0,

        // The color of the progress bar...
        color: "#6574cd",

        // Whether to include the default NProgress styles...
        includeCSS: true,

        // Whether the NProgress spinner will be shown...
        showSpinner: true,
    },
});
