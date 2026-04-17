import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import {
    HeadlessModal,
    Modal,
    ModalLink,
    renderApp,
} from '@inertiaui/modal-vue';
import { createBootstrap } from 'bootstrap-vue-next/plugins/createBootstrap';
import { createApp } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { IconHome } from '@tabler/icons-vue';

import VueEasyLightbox from 'vue-easy-lightbox';
import VueFeather from 'vue-feather';
import FlagIcon from 'vue-flag-icon';
import VueApexCharts from 'vue3-apexcharts';
import VueSelect from 'vue3-select-component';

/********* Layout component**********/
import MainLayout from '@/layouts/main-layout.vue';
import StoreLayout from '@/layouts/store-layout.vue';

/********* Custom component**********/
import AvatarUpload from '@/components/avatar-upload.vue';
import ToggleBtn from '@/components/buttons/toggle-btn.vue';
import DropdownSelect from '@/components/form/dropdown-select.vue';
import InputSlug from '@/components/form/input-slug.vue';
import InputText from '@/components/form/input-text.vue';
import InputError from '@/components/input-error.vue';
import LoadingText from '@/components/loading-text.vue';
import SubmitBtn from '@/components/submit-btn.vue';

import DtBulkDelete from '@/components/datatable/actions/dt-bulk-delete.vue';
import DtBulkDelete2 from '@/components/datatable/actions/dt-bulk-delete2.vue';
import DtBulkDelete3 from '@/components/datatable/actions/dt-bulk-delete3.vue';
import DtDelete from '@/components/datatable/actions/dt-delete.vue';
import DtDelete2 from '@/components/datatable/actions/dt-delete2.vue';

import DtSearch from '@/components/datatable/dt-search.vue';
import DtTable from '@/components/datatable/dt-table.vue';
import SelectFilter from '@/components/datatable/filter/select-filter.vue';

import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'primeicons/primeicons.css';

import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
// import 'bootstrap/dist/js/bootstrap'; --- IGNORE ---

import '@vuepic/vue-datepicker/dist/main.css';
import 'boxicons/css/boxicons.min.css';
import 'ionicons-npm/css/ionicons.css';
import 'pe7-icon/dist/dist/pe-icon-7-stroke.css';
import 'remixicon/fonts/remixicon.css';
import 'typicons.font/src/font/typicons.css';
import 'weathericons/css/weather-icons.css';
import '../css/feather.css';
import '../css/tabler-icons.css';
import '../scss/main.scss';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
        const pages = import.meta.glob('./pages/**/*.vue');
        const page = await pages[`./pages/${name}.vue`]();

        // Use StoreLayout for store pages
        if (name.startsWith('store/')) {
            page.default.layout = StoreLayout;
        } else {
            page.default.layout = page.default.layout || MainLayout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: renderApp(App, props) });

        app.component(VueFeather.name, VueFeather);
        app.component('vue-select', VueSelect);
        app.component('IconHome', IconHome);
        app.use(VueApexCharts);
        app.use(FlagIcon);
        app.use(VueEasyLightbox);

        app.component('avatar-upload', AvatarUpload);
        app.component('loading-text', LoadingText);

        app.component('input-error', InputError);
        app.component('input-text', InputText);
        app.component('input-slug', InputSlug);
        app.component('dropdown-select', DropdownSelect);

        app.component('submit-btn', SubmitBtn);
        app.component('toggle-btn', ToggleBtn);

        app.component('dt-table', DtTable);
        app.component('dt-search', DtSearch);
        app.component('select-filter', SelectFilter);

        app.component('dt-delete', DtDelete);
        app.component('dt-bulk-delete', DtBulkDelete);
        app.component('dt-delete2', DtDelete2);
        app.component('dt-bulk-delete2', DtBulkDelete2);
        app.component('dt-bulk-delete3', DtBulkDelete3);

        app.component('Modal', Modal);
        app.component('ModalLink', ModalLink);
        app.component('HeadlessModal', HeadlessModal);

        app.use(createBootstrap());

        return app.use(plugin).use(ZiggyVue).mount(el);
    },
    progress: {
        color: '#FE9F43',
    },
});
