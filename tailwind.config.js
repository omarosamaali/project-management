import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./node_modules/flowbite/**/*.js",
    ],

    darkMode: "class",

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                    950: "#172554",
                },
            },
        },
    },

    plugins: [forms, require("flowbite/plugin")],

    safelist: [
        "bg-green-700",
        "hover:bg-green-800",
        "bg-amber-600",
        "hover:bg-amber-700",
        "bg-red-600",
        "hover:bg-red-700",
        "bg-blue-600",
        "hover:bg-blue-700",
        "bg-gray-900",
        "hover:bg-black",
        "bg-gray-600",
        "hover:bg-gray-700",
    ],
};
