import colors from "tailwindcss/colors";
import defaultTheme from "tailwindcss/defaultTheme";

import typography from "@tailwindcss/typography";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'selector',
    content: [
        "./resources/views/craftable-pro.blade.php",
        "./resources/js/craftable-pro/**/*.vue",
        "./brackets/craftable-pro/resources/js/**/*.vue"
    ],

    theme: {
        extend: {
            colors: {
                primary: colors.indigo,
                // primary: '#42c8bc',
                secondary: colors.fuchsia,
                gray: colors.slate,
                warning: colors.amber,
                amber: colors.amber,
                danger: colors.red,
                success: colors.lime,
                info: colors.sky,
            },
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
            screens: {
                '3xl': '1800px',
            },
        },
    },

    plugins: [
        typography,
        forms,
    ],
};
