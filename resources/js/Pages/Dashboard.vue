<template>
  <app-layout>
    <template #header>
      <h2 class="font-medium text-2xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="grid grid-cols-3 gap-6 text-gray-400 text-center">
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">Nothing</div>
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">here</div>
      <div class="rounded-md bg-white shadow-md px-5 py-12 col-span-3 md:col-span-1 mx-4 md:m-0">yet</div>
    </div>

  </app-layout>
</template>

<script>
import AppLayout from '../Layouts/AppLayout'

export default {
  components: {
    AppLayout,
  },
}
</script>
