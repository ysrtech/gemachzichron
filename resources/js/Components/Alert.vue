<template>
  <div class="bg-red-50 border border-red-700 w-full px-3.5 py-2.5 rounded-md text-red-700">
    <div class="flex justify-between items-center">
      <h4 class="font-medium flex space-x-3 items-center">
        <span class="material-icons-outlined">error_outline</span>
        <span>{{header}}</span>
      </h4>
      <inertia-link :href="link" v-if="link" class="font-bold hover:underline text-sm">{{ linkText }}</inertia-link>
    </div>
  </div>
</template>

<script setup>
defineProps({
  header: {
    type: String,
    required: true,
  },
  link: String,
  linkText: String
});
</script>
