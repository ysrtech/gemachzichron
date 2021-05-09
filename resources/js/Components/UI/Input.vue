<template>
  <div>

    <div class="flex justify-between items-baseline">
      <label v-if="label" :for="$attrs.id" class="block font-medium text-sm text-gray-600">
        {{ label }}
      </label>
      <slot name="top-right"/>
    </div>

    <select
      v-if="type === 'select'"
      v-bind="$attrs"
      :class="{'border-red-600': !!error}"
      :value="modelValue"
      class="select-spinner shadow-sm mt-1 block w-full appearance-none text-base leading-normal bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 ring-primary-600 rounded-md placeholder-gray-400 py-2 px-3"
      @input="$emit('update:modelValue', $event.target.value)">
      <slot name="options"/>
    </select>

    <input
      v-else
      ref="input"
      v-bind="$attrs"
      :class="{'border-red-600': !!error}"
      :type="type"
      :value="modelValue"
      class="shadow-sm mt-1 block w-full appearance-none text-base leading-normal bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 ring-primary-600 rounded-md placeholder-gray-400 py-2 px-3"
      @input="$emit('update:modelValue', $event.target.value)"
    >

    <transition name="error">
      <p v-if="error" class="mt-1 text-xs text-red-600">
        {{ error }}
      </p>
    </transition>

  </div>
</template>

<script>
export default {
  name: "AppInput",

  inheritAttrs: false,

  emits: ['update:modelValue'],

  props: {
    label: {
      type: String
    },
    modelValue: {
      default: null
    },
    error: {
      type: String,
    },
    type: {
      default: 'text'
    }
  },

  methods: {
    focus() {
      this.$refs.input.focus()
    },
  }
}
</script>

<style scoped>
.select-spinner {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3e%3cpath d='M7 7l3-3 3 3m0 6l-3 3-3-3' stroke='%239fa6b2' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1.5em 1.5em;
}

.error-enter-active, .error-leave-active {
  transition: all 0.5s ease-in-out;
}

.error-enter-from, .error-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
