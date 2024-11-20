import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'
import tailwindcss from "tailwindcss";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.ts',
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
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            "craftable-pro": path.resolve(
                __dirname,
                "./vendor/brackets/craftable-pro/resources/js"
            ),
            ziggy: path.resolve(__dirname, "./vendor/tightenco/ziggy"),
        }
    },
    css: {
        postcss: {
            plugins: [
                tailwindcss({
                    config: "./admin.tailwind.config.js",
                }),
            ],
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        const commonLibs = [
                            'calendar',
                            'echo',
                            'axios',
                            'pusher-js',
                            'vuex',
                            'cropperjs',
                            'vueform',
                            'chart',
                        ];
                        if (commonLibs.some(lib => id.includes(lib))) {
                            return id.toString().split('node_modules/')[1].split('/')[0].toString();
                        }
                        // return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        },
        minify: true,
    }
});
