<template>
  <button :type="type"
          class="inline-flex items-center px-4 py-2 bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-900 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo transition ease-in-out duration-150"
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
