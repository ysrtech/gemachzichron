<template>
  <div>
    <label class="block text-gray-700 text-xs">{{ label }}</label>

    <select
      v-if="type === 'select'"
      class="mt-1 w-full text-sm border focus:outline-none rounded p-1"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)">
      <option v-for="(value, key) in options" :value="value" :key="key">
        {{ Array.isArray(options) ? value : key }}
      </option>
      <slot name="options"/>
    </select>

    <input
      v-else
      :type="type"
      class="mt-1 w-full text-sm border focus:outline-none rounded p-1"
      :value="modelValue"
      :placeholder="placeholder"
      @input="$emit('update:modelValue', $event.target.value)">

  </div>
</template>

<script>
export default {
  props: {
    label: String,
    type: {
      type: String,
      default: 'select'
    },
    options: {
      type: Object,
      default: () => []
    },
    placeholder: String,
    modelValue: {
      default: null
    },
  },

  emits: ['update:modelValue'],
}
</script>
