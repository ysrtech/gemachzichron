<template>
  <button :type="type"
          class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-900 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:shadow-outline-primary transition ease-in-out duration-150"
          :class="{'opacity-25': processing}"
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
      default: 'submit',
    },
    processing: {
      type: Boolean,
      default: false
    }
  }
}
</script>
