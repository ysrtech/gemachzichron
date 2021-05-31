<template>
  <div class="flex items-center">
    <div class="flex w-full">
      <app-dropdown
        :close-on-click="false"
        align="left" width="56">
        <template #trigger>
          <button class="flex items-center text-gray-400 px-4 md:pl-5 rounded-l-md border-r bg-white py-2 shadow-sm focus:outline-none focus:ring-2 ring-primary-700">
            <i class="md:hidden material-icons-outlined">filter_alt</i>
            <span class="hidden md:inline mr-2">Filter</span>
            <app-badge color="primary" v-if="appliedFiltersLength" class="mr-2">{{ appliedFiltersLength }}</app-badge>
            <i class="hidden md:inline material-icons-outlined">expand_more</i>
          </button>
        </template>
        <template #content>
          <div class="p-4 relative">
            <button type="button" @click="$emit('reset')" class="absolute right-4 text-gray-500 text-xs top-0">Clear</button>
            <div class="space-y-4">
              <slot/>
            </div>
          </div>
        </template>
      </app-dropdown>
      <input
        :value="modelValue"
        autocomplete="off"
        class="rounded-r-md shadow-sm w-full relative px-6 focus:outline-none focus:ring-2 ring-primary-700 bg-white"
        name="search"
        :placeholder="placeholder"
        type="text"
        @input="$emit('update:modelValue', $event.target.value)"
      >
    </div>
    <button v-show="modelValue" class="-ml-10 z-10 focus:outline-none flex items-center" type="button">
      <i class="material-icons-outlined p-1 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 focus:bg-gray-200"
         @click="$emit('reset', 'search')">clear</i>
    </button>
  </div>
</template>

<script>
import AppDropdown from "@/Components/UI/Dropdown"
import AppBadge from "@/Components/UI/Badge";

export default {
  components: {
    AppBadge,
    AppDropdown
  },

  emits: ['update:modelValue', 'reset'],

  props: {
    modelValue: String,
    appliedFiltersLength: {
      type: Number,
      required: false
    },
    placeholder: {
      type: String,
      default: 'Search...'
    }
  },
}
</script>
