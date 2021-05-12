<template>
  <nav aria-label="Pagination" class="relative z-0 inline-flex shadow-sm -space-x-px">
    <template v-for="(link, key) in links" :key="key">
      <div
        v-if="link.url === null"
        :class="{'rounded-l-md': key == 0, 'rounded-r-md': key == links.length - 1}"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300">
        {{ link.label }}
      </div>
      <inertia-link
        v-else
        :class="{'bg-primary-100 text-primary-600': link.active, 'rounded-l-md': key == 0, 'rounded-r-md': key == links.length - 1}"
        :href="link.url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
        {{ link.label }}
      </inertia-link>
    </template>
  </nav>
</template>

<script>
export default {
  props: {
    links: {
      type: Array,
      required: true
    }
  }
}
</script>
