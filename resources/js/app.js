import Vue from 'vue';
import { InertiaApp, plugin } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import { InertiaProgress } from '@inertiajs/progress'
import vSelect from "vue-select";

export const EventBus = new Vue();

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.prototype.$route = (...args) => route(...args).url()

Vue.component("v-select", vSelect);
Vue.mixin({
  methods: {
    formatDate(date) {
      if (!date) {
        return '';
      }
      return new Date(date).toDateString().substr(4);
    }
  },

  mounted() {
    if(this.$options.header) {
      EventBus.$emit('page-changed', this.$options.header)
    }
  }
})

Vue.use(plugin);
Vue.use(InertiaForm);
Vue.use(PortalVue);

InertiaProgress.init({
  color: '#2d3748',
})

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
