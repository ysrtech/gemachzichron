<template>
  <div>
    <label class="block text-gray-700 text-xs">{{ label }}</label>
    <select
      class="mt-1 w-full text-sm border focus:outline-none rounded p-1"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)">
      <option v-for="(value, key) in options" :value="value">{{ key }}</option>
    </select>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    options: Object,
    modelValue: {
      default: null
    },
  },

  emits: ['update:modelValue'],
}
</script>
