<template>
  <div>
    <dt class="text-xs font-medium text-gray-400">{{ label }}</dt>
    <dd class="mt-1 text-sm text-gray-900">
      <template v-if="value">{{ value }}</template>
      <slot/>
    </dd>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    value: [String, Number]
  }
}
</script>
