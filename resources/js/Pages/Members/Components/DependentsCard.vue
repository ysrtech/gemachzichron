<template>
  <div class="bg-white shadow rounded-md px-6">

    <div class="py-4 flex justify-between">
      <div class="text-xl font-medium">Dependents</div>
      <button class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
        add
      </button>
    </div>

    <div class="py-4">
      <div v-for="dependent in dependents" :key="dependent.id">
        dep
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DependentsCard",

  props: {
    dependents: Array
  }
}
</script>
