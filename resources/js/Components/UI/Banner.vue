<template>
  <div>
    <transition
      leave-active-class="transition ease-in duration-1000"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="options" :class="bgColor" class="relative">
        <div class="max-w-screen-xl mx-auto py-3 px-3 sm:px-6 lg:px-8">

          <div class="pr-16 sm:text-center sm:px-16">
            <p class="font-medium text-white">
              <span>
                {{ message }}
              </span>
              <span v-if="actionText" class="block sm:ml-2 sm:inline-block">
                <a :href="actionUrl" class="text-white font-bold underline" target="_blank">
                  {{ actionText }} &rarr;
                </a>
              </span>
            </p>
          </div>

          <div v-if="closable" class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pr-2">
            <button
              :class="closeIconBgColor"
              aria-label="Dismiss"
              class="flex p-2 rounded-full focus:outline-none transition ease-in-out duration-150"
              type="button"
              @click.prevent="$emit('close')">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {

  name: 'AppBanner',

  props: {
    options: {
      type: Object
    }
  },

  emits: ['close'],

  computed: {
    message() {
      return this.options?.message
    },

    level() {
      return this.options?.level
    },

    closable() {
      return this.options?.closable
    },

    actionText() {
      return this.options?.action_text
    },

    actionUrl() {
      return this.options?.action_url
    },

    bgColor() {
      return {
        'bg-primary-700': this.level === 'primary',
        'bg-green-600': this.level === 'success',
        'bg-red-700': this.level === 'danger',
        'bg-yellow-600': this.level === 'warning'
      }
    },

    closeIconBgColor() {
      return {
        'hover:bg-primary-600 focus:bg-primary-600': this.level === 'primary',
        'hover:bg-green-700 focus:bg-green-700': this.level === 'success',
        'hover:bg-red-600 focus:bg-red-600': this.level === 'danger',
        'hover:bg-yellow-700 focus:bg-yellow-700': this.level === 'warning',
      }
    }
  }
}
</script>
