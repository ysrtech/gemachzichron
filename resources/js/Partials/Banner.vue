<template>
  <teleport to="body">
    <transition
      leave-active-class="transition ease-in duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="show" :class="bgColor" class="fixed w-screen top-0">
        <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8 relative">

          <div class="pr-16 sm:text-center sm:px-16">
            <p class="font-medium text-white">
              <span>{{ message }}</span>
              <span v-if="action" class="block sm:ml-2 sm:inline-block">
                <button @click="doAction" class="text-white font-bold underline">
                  {{ action.text }} &rarr;
                </button>
              </span>
            </p>
          </div>

          <div v-if="closable" class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pr-2">
            <button
              aria-label="Dismiss"
              class="flex p-2 rounded-full focus:outline-none transition ease-in-out duration-150 hover:bg-gray-600 focus:bg-gray-900 hover:bg-opacity-40"
              type="button"
              @click.prevent="close">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
import {ref} from "vue";

export default {

  name: 'AppBanner',

  setup(props, {emit}) {

    const show = ref(true) // for transition purposes

    const close = () => {
      show.value = false
      setTimeout(() => emit('close'), 300)
    }

    return {
      show,
      close,
    }
  },

  props: {
    message: String,
    level: {
      type: String, // 'primary' | 'success' | 'danger' | 'warning'
      default: 'primary',
    },
    closable: {
      type: Boolean,
      default: true,
    },
    action: Object, // {text: String, route: String, method: String, url: String, target: String}
  },

  emits: ['close'],

  computed: {
    bgColor() {
      return {
        'bg-primary-600': this.level === 'primary',
        'bg-green-600': this.level === 'success',
        'bg-red-700': this.level === 'danger',
        'bg-yellow-600': this.level === 'warning'
      }
    }
  },

  methods: {
    doAction() {
      if (this.action?.route) {
        this.$inertia.visit(this.action.route, {
          method: this.action.method || 'get'
        })
      } else {
        window.open(this.action.url)
      }
    }
  }
}
</script>
