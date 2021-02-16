<template>
  <app-modal v-if="options" :show="true" @close="close" max-width="md">
    <div class="p-6">
      <div>
        <div v-if="icon" :class="iconBg" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full">
          <svg :class="iconColor" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path :d="iconData" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
        </div>

        <div class="mt-3 text-center sm:mt-5">
          <h3 v-if="title" id="modal-headline" class="text-lg leading-6 font-medium text-gray-900 my-4">
            {{ title }}
          </h3>

          <slot>
            <div v-if="message" class="mt-2">
              <p class="text-sm text-gray-500" v-html="message"></p>
            </div>
          </slot>
        </div>
      </div>

      <div class="mt-5 sm:mt-6 flex space-x-3 justify-center">
        <app-button color="secondary" @click="close">
          {{ closeText }}
        </app-button>
        <app-button v-if="actionRoute" :color="actionColor" @click="$inertia.visit(actionRoute, {method: actionMethod})">
          {{ actionText }}
        </app-button>
      </div>

    </div>
  </app-modal>
</template>

<script>
import AppModal from "@/Components/UI/Modals/Modal";

export default {
  components: {
    AppModal,
  },

  props: {
    options: {
      type: Object
    },
  },

  emits: ['close'],

  computed: {
    icon() {
      return this.options?.icon
    },
    title() {
      return this.options?.title
    },
    message() {
      return this.options?.message
    },
    actionText() {
      return this.options?.action_text
    },
    actionColor() {
      return this.options?.action_color
    },
    actionRoute() {
      if (this.options?.action_route) {
        return this.$route(this.options?.action_route, this.options?.action_route_params || {})
      }
    },
    actionMethod() {
      return this.options?.action_method || 'GET'
    },
    closeText() {
      return this.options?.close_text
    },
    iconColor() {
      return {
        success: 'text-green-600',
        error: 'text-red-600',
      }[this.icon]
    },
    iconBg() {
      return {
        success: 'bg-green-100',
        error: 'bg-red-100',
      }[this.icon]
    },
    iconData() {
      return {
        success: 'M5 13l4 4L19 7',
        error: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
      }[this.icon]
    },
  },

  methods: {
    close() {
      this.$emit('close')
    }
  }
}
</script>
