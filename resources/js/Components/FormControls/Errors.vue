<template>
  <transition name="error">
    <p v-if="error" class="mt-1 text-xs text-red-600">
      {{ error }}
    </p>
  </transition>
</template>

<script>
export default {
  name: 'AppErrors',
  props: {
    error: {
      type: String
    }
  }
}
</script>

<style scoped>
.error-enter-active, .error-leave-active {
  transition: all 0.5s ease-in-out;
}

.error-enter-from, .error-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
