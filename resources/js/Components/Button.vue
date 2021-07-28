<template>
  <button
    :class="[colorClasses, {'opacity-25': processing}]"
    :disabled="processing || disabled"
    :type="type"
    class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-offset-1 focus:outline-none font-semibold text-xs uppercase transition ease-in-out duration-150">

    <div
      v-if="processing"
      class="rounded-full border-2 h-4 w-4 animate-spin relative mr-2"
      :class="{'border-primary-700': color === 'secondary'}"
      style="font-size: 10px; border-left-color: transparent"
    />

    <slot/>
  </button>
</template>

<script>
export default {
  name: 'AppButton',

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
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    colorClasses() {
      return {
        'primary': 'bg-primary-700 border-transparent text-white hover:bg-primary-800 focus:bg-primary-900 focus:ring-primary-700',
        'secondary': 'bg-white border-gray-200 text-gray-500 hover:text-gray-700 focus:text-gray-700 focus:ring-primary-700',
        'danger': 'bg-red-600 border-transparent text-white hover:bg-red-700 focus:bg-red-800 focus:ring-red-700',
      }[this.color]
    },
  }
}
</script>
