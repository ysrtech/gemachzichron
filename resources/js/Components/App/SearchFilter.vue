<template>
  <div class="flex items-center">
    <div class="flex w-full">
      <app-dropdown
        :close-on-click="false"
        align="left" width="56">
        <template #trigger>
          <button class="flex text-gray-400 px-4 md:pl-5 rounded-l-md border-r bg-white py-2 shadow-sm focus:outline-none focus:ring-2 ring-primary-700">
            <i class="md:hidden material-icons-outlined">filter_alt</i>
            <span class="hidden md:inline mr-2">Filter</span>
            <i class="hidden md:inline material-icons-outlined">expand_more</i>
          </button>
        </template>
        <template #content>
          <slot />
        </template>
      </app-dropdown>
      <input
        :value="modelValue"
        autocomplete="off"
        class="rounded-r-md shadow-sm w-full relative px-6 focus:outline-none focus:ring-2 ring-primary-700 bg-white"
        name="search"
        placeholder="Search…"
        type="text"
        @input="$emit('update:modelValue', $event.target.value)"
      >
    </div>
    <button v-show="modelValue" class="-ml-10 z-10 focus:outline-none flex items-center" type="button">
      <i class="material-icons-outlined p-1 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 focus:bg-gray-200"
         @click="$emit('reset')">clear</i>
    </button>
  </div>
</template>

<script>
import AppDropdown from "@/Components/UI/Dropdown"

export default {
  components: {
    AppDropdown
  },

  emits: ['update:modelValue', 'reset'],

  props: {
    modelValue: String,
  },
}
</script>
