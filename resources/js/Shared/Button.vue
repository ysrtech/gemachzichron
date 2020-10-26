<template>
  <button :type="type"
          class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase focus:outline-none transition ease-in-out duration-150"
          :class="[colorClasses, {'opacity-25': processing}]"
          :disabled="processing">
    <div v-if="processing" class="btn-spinner mr-2"/>
    <slot></slot>
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'button',
    },
    color: {
      type: String,
      default: 'primary'
    },
    processing: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    colorClasses() {
      return {
        'primary': 'bg-primary-700 border-transparent text-white hover:bg-primary-900 active:bg-primary-900 focus:border-primary-900 focus:shadow-outline-primary',
        'secondary': 'bg-white border-gray-300 text-gray-700 shadow-sm hover:text-gray-500 focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50',
        'danger': 'bg-red-600 border-transparent text-white hover:bg-red-500 focus:border-red-700 focus:shadow-outline-red active:bg-red-600',
      }[this.color]
    },
  }
}
</script>
