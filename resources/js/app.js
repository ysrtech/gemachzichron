import {createApp, h} from 'vue';
import {App, plugin} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import VueTippy from 'vue-tippy';
import 'tippy.js/dist/tippy.css';
import AppInput from '@/Components/UI/Input';
import AppButton from '@/Components/UI/Button';
import axios from "axios";

InertiaProgress.init({color: '#075985'}) // should match a primary color

const el = document.getElementById('app')

const app = createApp({
  render: () => h(App, {
    initialPage: JSON.parse(el.dataset.page),
    resolveComponent: name => require(`./Pages/${name}`).default,
  })
})

app.config.globalProperties.$route = route
app.config.globalProperties.$axios = axios

app
  .use(plugin)
  .use(VueTippy)
  .component('AppInput', AppInput)
  .component('AppButton', AppButton)
  .mount(el)
