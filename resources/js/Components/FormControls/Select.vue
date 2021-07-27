<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

    <select
      v-if="native"
      v-bind="$attrs"
      :value="modelValue"
      class="mt-1 form-control form-input select-spinner"
      :class="{'border-red-600': error}"
      @input="$emit('update:modelValue', $event.target.value)">
      <option v-for="(value, key) in options" :value="value" :key="key">
        {{ Array.isArray(options) ? value : key }}
      </option>
      <slot name="options"/>
    </select>

    <vue-next-select
      v-else
      :model-value="modelValue"
      :options="options"
      placeholder=""
      search-placeholder=""
      open-direction="bottom"
      v-bind="$attrs.multiple ? $attrs : {...$attrs, ...{searchable: true, clearOnClose: true, clearOnSelect: true, closeOnSelect: true}}"
      @update:model-value="$emit('update:modelValue', $event)"
      @search:input="$emit('search:input', $event.target.value)"
    />

    <app-errors :error="error"/>
  </div>
</template>

<script>
import VueNextSelect from 'vue-next-select';
import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppSelect",

  inheritAttrs: false,

  emits: [
    'update:modelValue',
    'search:input',
  ],

  components: {
    AppErrors,
    AppLabel,
    VueNextSelect,
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
    options: {
      type: [Object, Array],
      default: [],
    },
    native: {
      type: Boolean,
      default: false,
    },
  },

}
</script>

<style scoped>
.select-spinner {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3e%3cpath d='M7 7l3-3 3 3m0 6l-3 3-3-3' stroke='%239fa6b2' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1.5em 1.5em;
}
</style>
