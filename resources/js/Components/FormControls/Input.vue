<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <input
      v-bind="$attrs"
      :type="type"
      :value="modelValue"
      ref="input"
      class="mt-1 form-control form-input"
      :class="{'border-red-600': error}"
      @input="$emit('update:modelValue', $event.target.value)"
    >

    <app-errors :error="error"/>
  </div>
</template>

<script>
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppInput",

  inheritAttrs: false,

  emits: ['update:modelValue'],

  components: {
    AppErrors,
    AppLabel,
  },

  props: {
    modelValue: {
      default: null
    },
    label: {
      type: String
    },
    error: {
      type: String,
    },
    type: {
      type: String,
      default: 'text'
    },
  },

  methods: {
    focusInput() {
      this.$refs.input.focus()
    }
  },
}
</script>
