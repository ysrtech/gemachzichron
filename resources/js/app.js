import {createApp, h} from 'vue';
import {App, plugin} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import AppInput from '@/Components/UI/Input';
import AppButton from '@/Components/UI/Button';

InertiaProgress.init({color: '#075985'}) // should match a primary color

const el = document.getElementById('app')

const app = createApp({
  render: () => h(App, {
    initialPage: JSON.parse(el.dataset.page),
    resolveComponent: name => require(`./Pages/${name}`).default,
  })
})

app
  .use(plugin)
  .component('AppInput', AppInput)
  .component('AppButton', AppButton)
  .mount(el)

app.config.globalProperties.$route = route
