<template>
  <label :for="id" class="block font-medium text-sm text-gray-600">
    {{ name }}
  </label>
</template>

<script>
export default {
  name: 'AppLabel',
  props: {
    id: {
      type: String,
    },
    name: {
      type: String,
    },
  }
}
</script>
