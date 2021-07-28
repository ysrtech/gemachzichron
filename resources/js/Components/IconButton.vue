<template>
  <button type="button" class="material-icons-outlined focus:outline-none rounded-full hover:bg-gray-200 active:bg-gray-300 text-gray-600 hover:text-gray-800" :class="paddingClass">
    {{ icon }}
  </button>
</template>

<script>
export default {
  props: {
    icon: {
      type: String,
      required: true
    },
    padding: {
      type: [String, Number],
      default: '2'
    }
  },

  computed: {
    paddingClass() {
      return {
        '1': 'p-1',
        '1.5': 'p-1.5',
        '2': 'p-2'
      }[this.padding] || this.padding
    }
  }
}
</script>
