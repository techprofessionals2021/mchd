// import './bootstrap';
// import '../css/app.css';

// import { createApp, h } from 'vue';
// import { createInertiaApp } from '@inertiajs/inertia-vue3';
// import { InertiaProgress } from '@inertiajs/progress';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
//     setup({ el, app, props, plugin }) {
//         return createApp({ render: () => h(app, props) })
//             .use(plugin)
//             .use(ZiggyVue, Ziggy)
//             .mount(el);
//     },
// });

// InertiaProgress.init({ color: '#4B5563' });

require('./bootstrap');

import {createApp} from 'vue'

import App from './App.vue'
import AntdSidebar from './Components/AntdSidebar.vue'
import STable from '@surely-vue/table';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';

console.log(AntdSidebar,'AntdSidebar');
const app = createApp({})
app.use(Antd);
app.use(STable);
app.component('app', App)
app.component('antd-sidebar', AntdSidebar)
app.mount("#app")
// createApp(App)
