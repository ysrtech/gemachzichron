<template>
  <teleport to="body">
    <transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-show="show" class="fixed inset-0 overflow-hidden z-50">
        <div class="absolute inset-0 overflow-hidden">
          <div aria-hidden="true" class="absolute inset-0 bg-gray-400 bg-opacity-75 transition-opacity"></div>
          <transition
            enter-active-class="transition ease-in-out duration-300 transform"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition ease-in-out duration-300 transform"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full">
            <section v-show="show" :class="side === 'left' ? 'left-0 pr-10' : 'right-0 pl-10'" aria-labelledby="slide-over-heading" class="absolute inset-y-0 max-w-full flex">
              <div class="relative w-52">
                <div :class="side === 'left' ? 'right-0 -mr-14 sm:pl-4' : 'left-0 -ml-14 sm:pr-4'" class="absolute top-0 p-2">
                  <button
                    class="bg-gray-700 rounded-full text-gray-300 hover:text-white hover:bg-gray-800 focus:outline-none"
                    @click="$emit('close')">
                    <span class="sr-only">Close panel</span>
                    <svg class="h-10 w-10 p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                  </button>
                </div>
                <div class="h-full flex flex-col bg-gray-900 shadow-xl">
                  <div class="mt-6 relative flex-1 px-4 sm:px-6">
                    <slot/>
                  </div>
                </div>
              </div>
            </section>
          </transition>

        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
export default {

  name: 'AppSideOverlay',

  props: {
    show: {
      type: Boolean,
      default: false
    },
    side: {
      type: String,
      default: 'left'
    }
  },

  emits: [
    'close'
  ],
}
</script>
