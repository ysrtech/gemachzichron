<template>
  <app-modal v-if="show" max-width="md" @close="cancel">
    <div class="p-6">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 9v2m0 4v2m-1.71-7.71a1 1 0 111.42 1.42 1 1 0 01-1.42-1.42zM6.29 15.71a1 1 0 111.42 1.42 1 1 0 01-1.42-1.42zM17.71 6.29a1 1 0 111.42 1.42 1 1 0 01-1.42-1.42zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
        </svg>
      </div>

      <div class="mt-3 text-center sm:mt-5">
        <h3 class="text-lg leading-6 font-medium text-gray-900 my-4">
          {{ title }}
        </h3>
        <div class="mt-2 text-sm text-gray-500">
          {{ message }}
        </div>
      </div>
    </div>

    <div class="bg-gray-100 flex justify-end mt-2 px-6 py-3 space-x-3">
      <app-button color="gray" @click="cancel">
        Cancel
      </app-button>
      <app-button :color="confirmColor" @click="confirm">
        {{ confirmText }}
      </app-button>
    </div>
  </app-modal>
</template>

<script>
import AppModal from "@/Components/Modal";
import AppButton from "@/Components/Button";

export default {
  components: {
    AppModal,
    AppButton,
  },

  props: {
    show: Boolean,
    title: String,
    message: String,
    confirmText: {
      type: String,
      default: 'Confirm'
    },
    confirmColor: {
      type: String,
      default: 'primary'
    }
  },

  emits: ['confirm', 'cancel'],

  methods: {
    confirm() {
      this.$emit('confirm');
    },
    cancel() {
      this.$emit('cancel');
    }
  }
}
</script>
