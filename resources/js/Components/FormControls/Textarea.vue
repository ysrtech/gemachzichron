<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <textarea
      v-bind="$attrs"
      :value="modelValue"
      class="mt-1 form-control form-input"
      :class="{'border-red-600': error}"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>

    <app-errors :error="error"/>
  </div>
</template>

<script>
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppTextarea",

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
  },
}
</script>
