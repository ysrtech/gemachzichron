<template>
  <div class="relative flex items-center justify-center min-h-screen bg-gray-100">
    <div class="p-6 flex items-center text-3xl text-gray-400 uppercase tracking-widest font-light">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    message: String
  }
}
</script>
