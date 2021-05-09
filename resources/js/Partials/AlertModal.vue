<template>
  <app-modal max-width="md" @close="close">
    <div class="p-6">
      <div v-if="icon" :class="iconBg" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full">
        <svg :class="iconColor" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path :d="iconData" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
        </svg>
      </div>

      <div class="mt-3 text-center sm:mt-5">
        <h3 v-if="title" id="modal-headline" class="text-lg leading-6 font-medium text-gray-900 my-4">
          {{ title }}
        </h3>

        <slot>
          <div v-if="message" class="mt-2 text-sm text-gray-500" v-html="message"></div>
        </slot>
      </div>
    </div>

    <div class="bg-gray-100 flex justify-end mt-2 px-6 py-3 space-x-3" v-if="actionButton || !closeButton === false">
      <app-button
        v-if="!closeButton === false"
        :color="closeButton.color"
        @click="close">
        {{ closeButton.text }}
      </app-button>
      <app-button
        v-if="actionButton"
        :color="actionButton.color || 'primary'"
        @click="$inertia.visit(actionButton.route, {method: actionButton.method || 'GET'})">
        {{ actionButton.text }}
      </app-button>
    </div>
  </app-modal>
</template>

<script>
import AppModal from "@/Components/UI/Modal";

export default {
  components: {
    AppModal,
  },

  props: {
    icon: String, /* 'success' | 'error' */
    title: String,
    message: String,
    actionButton: Object, /* { text: string, color: 'primary' | 'secondary' | 'danger', route: string, method: string } */
    closeButton: {
      type: [Boolean, Object], /* 'boolean' | { text: string, color: 'primary' | 'secondary' | 'danger' } */
      default: {text: 'Close', color: 'secondary'}
    }
  },

  emits: ['close'],

  computed: {
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
