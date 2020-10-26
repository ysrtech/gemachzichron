<template>
  <app-layout>
    <template #header>
      <h2 class="font-medium text-2xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
      <welcome/>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../Layouts/AppLayout'
import Welcome from '../Shared/Welcome'

export default {
  components: {
    AppLayout,
    Welcome,
  },
}
</script>
