<template>
  <div class="flex items-center">
    <div class="flex w-full">
      <jet-dropdown
        class="px-4 md:px-6 form-input rounded-r-none hover:bg-gray-100 cursor-pointer border-r-0 py-3"
        align="left" width="56" :close-on-click="false">
        <template #trigger>
          <div class="flex text-gray-500">
            <span class="hidden md:inline mr-2">Filter</span>
            <i class="hidden md:inline material-icons-outlined">expand_more</i>
            <i class="md:hidden material-icons-outlined">filter_alt</i>
          </div>
        </template>
        <template #content>
          <slot />
        </template>
      </jet-dropdown>
      <jet-input class="relative w-full px-6 py-3 focus:shadow-outline rounded-l-none"
                 autocomplete="off" type="text"
                 name="search" placeholder="Search…" :value="value" @input="$emit('input', $event)"/>
    </div>
    <button v-show="value" class="-ml-10 z-10 focus:outline-none flex items-center" type="button">
      <i class="material-icons p-1 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 focus:bg-gray-200"
         @click="$emit('reset')">clear</i>
    </button>
  </div>
</template>

<script>
import JetInput from "./Input"
import JetDropdown from "./Dropdown"

export default {
  components: {
    JetInput,
    JetDropdown
  },
  props: {
    value: String,
    maxWidth: {
      type: Number,
      default: 500,
    },
  },
}
</script>
