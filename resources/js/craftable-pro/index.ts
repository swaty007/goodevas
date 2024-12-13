import Toast, { POSITION } from '@brackets/vue-toastification';
import '@brackets/vue-toastification/dist/index.css';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
// import { AuthenticatedLayout, GuestLayout } from "craftable-pro/Layouts";
import elementWidthDraggingDirective from '@/craftable-pro/hooks/elementWidthDraggingDirective';
import { AuthenticatedLayout, GuestLayout } from '@/craftable-pro/Layouts';
import { can } from 'craftable-pro/plugins/can';
import {
    i18nVue,
    loadTranslations,
} from 'craftable-pro/plugins/laravel-vue-i18n';
import { PageProps } from 'craftable-pro/types/page';
import VueLazyLoad from 'vue3-lazyload';

const appName = 'Stock PRO';

const lang = document.documentElement.lang
    ? document.documentElement.lang.replace('-', '_')
    : 'en';

createInertiaApp({
    title: (title) => {
        const titleElement = document.querySelector('title');

        if (titleElement && !titleElement.hasAttribute('inertia')) {
            titleElement.remove();
        }

        return title ? `${title} - ${appName}` : appName;
    },
    progress: { color: '#4B5563' },
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const page = (await pages[`./Pages/${name}.vue`]()).default;

        if (page.layout === undefined) {
            if (name.startsWith('Auth/')) {
                page.layout = GuestLayout;
            } else {
                page.layout = AuthenticatedLayout;
            }
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        loadTranslations(
            `/lang/${
                (props.initialPage.props.auth as PageProps['auth'])?.user
                    ?.locale ?? lang
            }/craftable-pro.json`,
            (translations: JSON) => {
                return createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(Toast, {
                        transition: 'Vue-Toastification__fade',
                        // rootComponent: Notification,
                        position: POSITION.TOP_RIGHT,
                    })
                    .use(i18nVue, {
                        resolve: (lang: string) => {
                            return translations;
                        },
                    })
                    .use(autoAnimatePlugin)
                    .use(ZiggyVue)
                    .use(VueLazyLoad)
                    .component('Link', Link)
                    .directive('can', can)
                    .directive('width-dragging', elementWidthDraggingDirective)
                    .mount(el);
            },
        );
    },
});
