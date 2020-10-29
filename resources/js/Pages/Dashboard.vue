<template>
  <div>

    <div class="grid grid-cols-3 gap-6 text-gray-400 text-center">
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">Nothing</div>
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">here</div>
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">yet</div>
    </div>

  </div>
</template>

<script>
import AppLayout from '../Layouts/AppLayout'

export default {
  layout: AppLayout,
  header: 'Dashboard',
  components: {
    AppLayout,
  },
}
</script>
