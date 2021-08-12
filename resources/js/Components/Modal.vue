<template>
  <teleport to="body">
    <transition leave-active-class="duration-200">
      <div v-show="show" class="fixed top-0 overflow-y-auto inset-x-0 h-screen items-center px-4 sm:px-0 flex items-top justify-center z-40">
        <transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
          <div v-show="show" class="fixed inset-0 transform transition-all"> <!-- @click="close" -->
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>
        </transition>

        <transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
          <div
            v-if="show"
            :class="maxWidthClass"
            v-bind="$attrs"
            class="scrollbar-w-2 scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300 scrollbar-thumb-rounded-full
             bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full max-h-screen overflow-y-auto">
            <slot/>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<script>
import {onMounted, onUnmounted, ref} from "vue";

export default {

  inheritAttrs: false,

  props: {
    maxWidth: {
      default: '2xl'
    },
    closeable: {
      default: true
    },
  },

  emits: [
    'close'
  ],

  setup(props, {emit}) {

    const show = ref(false)

    onMounted(() => show.value = true) // for transition purposes

    const close = () => {
      if (props.closeable) {
        show.value = false
        setTimeout(() => emit('close'), 200) // for transition purposes
      }
    }

    const closeOnEscape = (e) => {
      if (e.key === 'Escape' && show.value) {
        close()
      }
    }

    onMounted(() => document.addEventListener('keydown', closeOnEscape))
    onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

    return {
      show,
      close,
    }
  },

  computed: {
    maxWidthClass() {
      return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
      }[this.maxWidth]
    }
  }
}
</script>
