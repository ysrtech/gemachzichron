<template>
  <div class="flex-1 flex items-center justify-between">
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ response.from }}</span>
        to
        <span class="font-medium">{{ response.to }}</span>
        of
        <span class="font-medium">{{ response.total }}</span>
        results
      </p>
    </div>

    <div class="flex-1 flex justify-between sm:justify-end">
      <inertia-link
        v-if="response.prev_page_url"
        :href="response.prev_page_url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
        Previous
      </inertia-link>
      <inertia-link
        v-if="response.next_page_url"
        :href="response.next_page_url"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
        Next
      </inertia-link>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    response: {
      type: Object,
      required: true
    }
  }
}
</script>
