<template>
  <app-layout>
    <template #header>
      <h2 class="font-medium text-2xl text-gray-800 leading-tight">
        {{ member.last_name + ' ' + member.first_name }}
      </h2>
    </template>

    <div class="grid grid-cols-6 gap-6">
      <div class="col-span-6 sm:col-span-4">
        <member-info-card :member="member"></member-info-card>
      </div>
      <div class="col-span-6 sm:col-span-2">
        <dependents-card :dependents="member.dependents"></dependents-card>
      </div>
    </div>


  </app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import MemberInfoCard from "./Components/MemberInfoCard";
import DependentsCard from "./Components/DependentsCard";


export default {
  name: "Show",

  components: {
    DependentsCard,
    MemberInfoCard,
    AppLayout,
  },

  props: {
    member: Object
  }
}
</script>

<style scoped>

</style>
