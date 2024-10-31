import { defineConfig, splitVendorChunkPlugin } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
const path = require("path");

export default defineConfig({
  plugins: [
    splitVendorChunkPlugin(),
    laravel({
      input: [
        "resources/js/craftable-pro/index.ts",
        "resources/css/craftable-pro.css",
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  css: {
    postcss: {
      plugins: [
        require("tailwindcss")({
          config: "./admin.tailwind.config.js",
        }),
      ],
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./resources/js"),
      "craftable-pro": path.resolve(
        __dirname,
        "./vendor/brackets/craftable-pro/resources/js"
      ),
      ziggy: path.resolve(__dirname, "./vendor/tightenco/ziggy"),
    },
  },
});
