<template>
  <div class="bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden overflow-x-auto">
    <header class="p-4 sm:px-6 flex items-center justify-between" v-if="title || description || !!$slots.actions">
      <div>
        <div class="flex space-x-2 items-baseline">
          <h2 class="text-lg leading-6 font-medium text-gray-900" v-html="title"></h2>
          <app-badge v-if="badge" :color="badge.color" :uppercase="badge.uppercase">{{ badge.text }}</app-badge>
        </div>
        <p v-if="description" class="mt-1 text-sm text-gray-700">{{ description }}</p>
      </div>
      <div class="-my-2">
        <slot name="actions"/>
      </div>
    </header>
    <slot name="content"/>
  </div>
</template>
<script>
export default {

  props: {
    title: String,
    badge: Object,
    description: String,
  }
}
</script>
