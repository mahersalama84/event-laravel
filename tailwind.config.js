const colors = require("tailwindcss/colors");
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "selector",
    corePlugins: {
        preflight: true,
    },
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        // fontSize: {},
        borderRadius: {
            none: "0",
            sm: ".125rem",
            DEFAULT: ".25rem",
            lg: ".5rem",
            xl: "1rem",
            full: "9999px",
        },
        spacing: {
            px: "1px",
            0: "0",
            0.5: "0.125rem",
            1: "0.25rem",
            1.5: "0.375rem",
            2: "0.5rem",
            2.5: "0.625rem",
            3: "0.75rem",
            3.5: "0.875rem",
            4: "1rem",
            5: "1.25rem",
            6: "1.5rem",
            7: "1.75rem",
            8: "2rem",
            9: "2.25rem",
            10: "2.5rem",
            11: "2.75rem",
            12: "5rem",
            14: "3.5rem",
            16: "4rem",
            20: "5rem",
            24: "6rem",
            28: "7rem",
            32: "8rem",
            36: "9rem",
            40: "10rem",
            44: "11rem",
            48: "12rem",
            52: "13rem",
            56: "14rem",
            60: "15rem",
            64: "16rem",
            72: "18rem",
            80: "20rem",
            96: "24rem",
        },
        colors: {
            transparent: "transparent",
            current: "currentColor",
            black: colors.black,
            white: colors.white,
            red: colors.red,
            orange: colors.orange,
            yellow: colors.yellow,
            green: colors.green,
            gray: colors.slate, //bluegray
            indigo: {
                100: "#e6e8ff",
                300: "#b2b7ff",
                400: "#7886d7",
                500: "#6574cd",
                600: "#5661b3",
                800: "#2f365f",
                900: "#191e38",
            },
        },
        fontFamily: {
            sans: ["Graphik", "sans-serif"],
            serif: ["Merriweather", "serif"],
        },
        extend: {
            borderColor: (theme) => ({
                DEFAULT: theme("colors.gray.200", "currentColor"),
            }),
            fontFamily: {
                sans: ["Cerebri Sans", ...defaultTheme.fontFamily.sans],
            },
            boxShadow: (theme) => ({
                outline: "0 0 0 2px " + theme("colors.indigo.500"),
            }),
            fill: (theme) => theme("colors"),
        },
    },
    variants: {
        extend: {
            fill: ["focus", "group-hover"],
        },
    },
    plugins: [forms],
};
