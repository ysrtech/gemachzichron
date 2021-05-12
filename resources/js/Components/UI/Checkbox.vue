<template>
  <input
    type="checkbox"
    v-model="checked"
    :style="['background-origin: border-box', backgroundImage]"
    class="h-4 w-4 p-0 rounded border flex-shrink-0 appearance-none inline-block select-none align-middle text-primary-600 shadow-sm focus:ring-2 focus:ring-offset-1 focus:ring-primary-600 focus:outline-none"
    :class="modelValue ? 'border-transparent focus:border-gray-300 bg-current bg-cover bg-center bg-no-repeat' : 'bg-white border-gray-300'"
  >
</template>

<script>
export default {

  name: 'AppCheckbox',

  emits: ['update:modelValue'],

  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    checked: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      }
    },
    backgroundImage() {
      return this.modelValue
        ? "background-image: url(\"data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e\")"
        : ''
    }
  },
}
</script>
