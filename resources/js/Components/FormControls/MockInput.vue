<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <div
      v-bind="$attrs"
      class="mt-1 form-control form-input"
      :class="{'border-red-600': error}">
      <slot/>
    </div>

    <app-errors :error="error"/>
  </div>
</template>

<script>
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppMockInput",

  inheritAttrs: false,

  components: {
    AppErrors,
    AppLabel,
  },

  props: {
    label: {
      type: String
    },
    error: {
      type: String,
    },
  },
}
</script>
