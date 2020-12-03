<template>
  <div class="flex items-center">
    <div class="flex w-full">

      <app-dropdown
        :close-on-click="false"
        align="left"
        class="px-4 md:px-6 form-input rounded-r-none hover:bg-gray-100 cursor-pointer py-3 w-1/3"
        width="56">

        <template #trigger>
          <div class="flex text-gray-500">
            <i class="material-icons-outlined text-gray-400 mr-2">filter_alt</i>
            <span class="hidden md:inline mr-2">Filter</span>
            <div class="flex-1"></div>
            <i class="hidden md:inline material-icons-outlined">expand_more</i>
          </div>
        </template>

        <template #content>
          <div class="p-5">
            <label class="block text-gray-700 text-sm">Status:</label>
            <select v-model="filters.status" class="mt-1 w-full form-select text-sm" @input="$emit('status-select')">
              <option :value="null">All</option>
              <option value="1">Paid</option>
              <option value="0">Pending</option>
            </select>
          </div>
        </template>

      </app-dropdown>
      <div class="relative">
        <select
          v-model="filters.sort"
          class="absolute px-4 md:px-6 w-36 form-select rounded-l-none border-l-0 hover:bg-gray-100 cursor-pointer py-3 text-gray-500"
          @input="$emit('sort-select')">
          <option :selected="filters.sort === '-created_at'" value="-created_at">Invoice Date</option>
          <option :selected="filters.sort === '-payment_date'" value="-payment_date">Paid Date</option>
        </select>

        <div
          class="absolute px-4 md:px-6 w-36 form-select bg-none rounded-l-none border-l-0 py-3 flex text-gray-500 pointer-events-none">
          <i class="material-icons-outlined text-gray-400 mr-2">sort</i>
          <span class="hidden md:inline mr-2">Sort</span>
          <div class="flex-1"></div>
          <i class="hidden md:inline material-icons-outlined">expand_more</i>
        </div>
      </div>

    </div>
  </div>


</template>

<script>
import AppDropdown from "../../../Shared/Dropdown"

export default {
  components: {
    AppDropdown
  },
  props: {
    filters: Object
  }
}
</script>
