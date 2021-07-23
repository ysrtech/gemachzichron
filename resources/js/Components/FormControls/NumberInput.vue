<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <div class="mt-1 form-control overflow-hidden relative">
      <button
        type="button"
        class="absolute left-0 top-0 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-500 text-xl h-full w-10 border-r material-icons-outlined"
        @click="decrement">
        remove
      </button>
      <input
        v-bind="$attrs"
        type="number"
        :value="modelValue"
        class="form-input w-full px-11"
        :class="{'border-red-600': error}"
        @input="$emit('update:modelValue', $event.target.value)"
      >
      <button
        type="button"
        class="absolute right-0 top-0 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-500 text-xl h-full w-10 border-l material-icons-outlined"
        @click="increment">
        add
      </button>
    </div>

    <app-errors :error="error"/>
  </div>
</template>

<script>
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppNumberInput",

  inheritAttrs: false,

  emits: ['update:modelValue'],

  components: {
    AppErrors,
    AppLabel,
  },

  props: {
    modelValue: {
      type: Number,
      default: null
    },
    label: {
      type: String
    },
    error: {
      type: String,
    },
  },

  methods: {
    increment() {
      this.$emit('update:modelValue', Number(this.modelValue) + Number(this.$attrs.step || 1))
    },
    decrement() {
      this.$emit('update:modelValue', Number(this.modelValue) + Number(this.$attrs.step || 1))
    }
  }
}
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}
</style>
