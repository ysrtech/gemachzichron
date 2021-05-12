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
      :value="modelValue"
      :class="styleClasses"
      class="select-spinner"
      @input="$emit('update:modelValue', $event.target.value)">
      <slot name="options"/>
    </select>

    <textarea
      v-else-if="type === 'textarea'"
      v-bind="$attrs"
      :value="modelValue"
      :class="styleClasses"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>

    <div
      v-else-if="type === 'div'"
      v-bind="$attrs"
      :class="styleClasses">
      <slot/>
    </div>

    <template v-else-if="type === 'file'">
      <label :for="$attrs.id">
        <div :class="styleClasses" class="text-gray-500 cursor-text truncate">
          {{ filename }}
        </div>
      </label>
      <input
        :id="$attrs.id"
        class="hidden"
        type="file"
        @change="$emit('update:modelValue', $event.target.files[0])"
      />
    </template>

    <input
      v-else
      ref="input"
      v-bind="$attrs"
      :type="type"
      :value="modelValue"
      :class="styleClasses"
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
    },
    filename: String
  },

  data() {
    return {
      styleClasses: `shadow-sm mt-1 block w-full appearance-none text-sm leading-normal bg-white
        border focus:outline-none focus:ring-2 focus:ring-offset-1 ring-primary-600 rounded-md placeholder-gray-400
        py-2 px-3 ${this.error ? 'border-red-600' : 'border-gray-300'}`
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
