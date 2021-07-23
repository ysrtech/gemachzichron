<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <label :for="$attrs.id">
      <div class="mt-1 form-control form-input text-gray-500 cursor-text truncate" :class="{'border-red-600': error}">
        {{ filename }}
      </div>
    </label>
    <input
      :id="$attrs.id"
      class="hidden"
      type="file"
      @change="$emit('update:modelValue', $event.target.files[0])"
    />

    <app-errors :error="error"/>
  </div>
</template>

<script>
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppFileInput",

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
    filename: {
      type: String,
    },
  },

}
</script>
