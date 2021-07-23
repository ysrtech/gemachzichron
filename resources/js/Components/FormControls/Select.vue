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
      v-model="modelValue"
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

/* VUE NEXT SELECT */

/*.icon.delete {*/
/*  @apply flex justify-center items-center p-0 m-0 border-0 bg-none h-2 w-2 cursor-pointer*/
/*}*/

/*.icon.arrow-downward {*/
/*  @apply cursor-pointer border-4 border-b-0*/
/*}*/

/*.icon.arrow-downward {*/
/*  border-color: #999 transparent;*/
/*  transition: transform 0.2s linear;*/
/*}*/

/*.icon.arrow-downward.active {*/
/*  @apply transform rotate-180*/
/*}*/

/*.vue-select {*/
/*  @apply relative py-1.5 px-3 shadow-sm mt-1 block w-full appearance-none text-sm leading-normal*/
/*  bg-white border rounded-md border-gray-300*/
/*}*/

/*.vue-select[aria-disabled='true'] {*/
/*  @apply bg-gray-100*/
/*}*/

/*.vue-select[aria-disabled='true'] * {*/
/*  @apply cursor-not-allowed*/
/*}*/

/*.vue-select[aria-disabled='true'] input {*/
/*  @apply cursor-not-allowed*/
/*}*/

/*.vue-select-header {*/
/*  @apply flex w-full items-center justify-between*/
/*}*/

/*.vue-tags {*/
/*  @apply flex flex-wrap m-0 pl-0.5 select-none*/
/*}*/

/*.vue-tags {*/
/*  min-height: calc(1rem + 4px);*/
/*}*/

/*.vue-tags.collapsed {*/
/*  @apply flex-nowrap overflow-auto*/
/*}*/

/*.vue-tag {*/
/*  @apply hidden items-center justify-center list-none rounded px-1 m-0.5 text-sm bg-primary-600*/
/*}*/

/*.vue-tag {*/
/*  min-height: 1rem;*/
/*}*/

/*.vue-tag span {*/
/*  @apply mr-1*/
/*}*/

/*.vue-tag.selected {*/
/*  @apply flex items-center content-center bg-primary-700 rounded px-1 text-sm*/
/*}*/

/*.vue-tags[data-removable='false'] .vue-tag.selected img:hover {*/
/*  @apply cursor-not-allowed*/
/*}*/

/*.vue-select-input-wrapper {*/
/*  @apply relative flex w-full items-center justify-between*/
/*}*/

/*.vue-input {*/
/*  @apply inline-flex items-center rounded border-0 focus:outline-none*/
/*  max-w-full min-w-0 w-full box-border text-gray-400*/
/*}*/

/*.vue-select[aria-expanded='true'] {*/
/*  @apply focus-within:ring-2 focus-within:ring-offset-1 ring-primary-600*/
/*}*/

/*.vue-select[aria-expanded='false'][aria-disabled='false'] .vue-input input, input[readonly] {*/
/*  @apply cursor-default*/
/*}*/

/*.vue-input input {*/
/*  @apply border-0 outline-none w-full min-w-0 text-sm p-0 placeholder-black*/
/*}*/

/*.vue-input input[disabled] {*/
/*  @apply bg-gray-100*/
/*}*/

/*.vue-input input[readonly] {*/
/*  background-color: unset;*/
/*}*/

/*.vue-select-header .vue-input input[disabled] {*/
/*  background-color: unset;*/
/*}*/

/*.vue-dropdown {*/
/*  @apply hidden absolute bg-white z-10 overflow-y-auto w-full min-w-0 m-0 p-0*/
/*  box-content border border-gray-300 list-none shadow-sm rounded-md*/
/*}*/

/*.vue-dropdown {*/
/*  left: -1px;*/
/*}*/

/*.vue-select[aria-expanded='true'] .vue-dropdown {*/
/*  display: unset;*/
/*}*/

/*.vue-select .vue-dropdown[data-visible-length='0'] {*/
/*  @apply hidden*/
/*}*/

/*.vue-dropdown-item {*/
/*  @apply list-none p-1 cursor-pointer*/
/*}*/

/*.vue-dropdown-item.highlighted {*/
/*  @apply bg-primary-100 text-primary-700*/
/*}*/

/*.vue-dropdown-item.disabled {*/
/*  @apply bg-gray-100 cursor-not-allowed*/
/*}*/

/*.vue-dropdown-item.selected {*/
/*  @apply bg-primary-100 text-primary-700*/
/*}*/

/*.vue-dropdown-item.selected.highlighted {*/
/*  @apply bg-primary-200 text-primary-900*/
/*}*/

/*.vue-dropdown[data-removable='false'] .vue-dropdown-item.selected:hover {*/
/*  @apply cursor-not-allowed*/
/*}*/

/*.vue-dropdown[data-addable='false'][data-multiple='true'] .vue-dropdown-item:not(.selected):hover {*/
/*  @apply cursor-not-allowed*/
/*}*/

/*.icon.loading {*/
/*  @apply inline-block relative w-4 h-4*/
/*}*/

/*.icon.loading div {*/
/*  @apply box-border block absolute border w-4 h-4 rounded-full animate-spin*/
/*}*/

/*.icon.loading div {*/
/*  border-left-color: transparent;*/
/*}*/

/*.icon.loading div:nth-child(1) {*/
/*  animation-delay: -0.08s;*/
/*}*/
/*.icon.loading div:nth-child(2) {*/
/*  animation-delay: -0.16s;*/
/*}*/

/*.vue-select.direction-top .vue-dropdown {*/
/*  bottom: calc(100% + 2px);*/
/*}*/

/*.vue-select.direction-bottom .vue-dropdown {*/
/*  top: calc(100% + 2px);;*/
/*}*/

</style>
