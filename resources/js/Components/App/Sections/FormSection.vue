<template>
  <div class="md:grid md:grid-cols-3 md:gap-6">
    <app-section-title>
      <template #title>
        <slot name="title"></slot>
      </template>
      <template #description>
        <slot name="description"></slot>
      </template>
    </app-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
      <form @submit.prevent="$emit('submitted')">
        <div class="shadow overflow-hidden rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
              <slot name="form"></slot>
            </div>
          </div>

          <div v-if="hasActions" class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
            <slot name="actions"></slot>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import AppSectionTitle from './SectionTitle'

export default {
  components: {
    AppSectionTitle,
  },

  emits: [
    'submitted'
  ],

  computed: {
    hasActions() {
      return !!this.$slots.actions
    }
  }
}
</script>
