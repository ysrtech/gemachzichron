import {createApp, h} from 'vue';
import {createInertiaApp} from "@inertiajs/inertia-vue3";
import {InertiaProgress} from '@inertiajs/progress';
import VueTippy from 'vue-tippy';
import 'tippy.js/dist/tippy.css';
import AppInput from '@/Components/UI/Input';
import AppButton from '@/Components/UI/Button';
import AppBadge from '@/Components/UI/Badge';
import axios from "axios";

InertiaProgress.init({color: '#075985'}) // should match a primary color

createInertiaApp({
  resolve: (name) => import(`./Pages/${name}`),
  setup({ el, app, props, plugin }) {
    const App = createApp({ render: () => h(app, props) })
    App.config.globalProperties.$route = route
    App.config.globalProperties.$axios = axios
    App
      .use(plugin)
      .use(VueTippy)
      .component('AppInput', AppInput)
      .component('AppButton', AppButton)
      .component('AppBadge', AppBadge)
      .mount(el)
  },
})
