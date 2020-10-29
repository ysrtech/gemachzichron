<template>
  <app-layout>
    <template #header>{{ member.last_name + ' ' + member.first_name }}</template>

    <div class="bg-orange-100 border-t border-b border-orange-500 w-full p-6 mb-3" v-if="member.deleted_at">
      <div class="flex justify-between text-orange-600">
        <div class="flex">
          <div class="material-icons-outlined">report_problem</div>
          <div class="ml-2">This member has been archived</div>
        </div>

        <inertia-link method="put" :href="route('members.restore', member.id)" class="font-medium hover:text-orange-700 hover:underline">
          Restore
        </inertia-link>
      </div>
    </div>

    <div class="pb-3 w-full text-right" v-else>
      <button type="button" class="text-red-600 hover:text-red-700 focus:outline-none" @click="confirmArchive = member">
        Archive Member
      </button>
    </div>

    <div class="grid grid-cols-6 gap-6">

      <div class="col-span-6 sm:col-span-4">
        <member-info-card :member="member"></member-info-card>
      </div>

      <div class="col-span-6 sm:col-span-2">
        <dependents-card :dependents="member.dependents" :member="member"></dependents-card>
      </div>

      <div class="col-span-6">
        <membership-card :membership="member.membership"></membership-card>
      </div>

    </div>

    <archive-confirmation :member="confirmArchive" @close="confirmArchive = null"></archive-confirmation>

  </app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import MemberInfoCard from "./Components/MemberInfoCard";
import DependentsCard from "./Components/DependentsCard";
import ArchiveConfirmation from "./Components/ArchiveConfirmation";
import MembershipCard from "./Components/MembershipCard";

export default {
  name: "Show",

  data() {
    return {
      confirmArchive: null,
    }
  },

  components: {
    MembershipCard,
    ArchiveConfirmation,
    DependentsCard,
    MemberInfoCard,
    AppLayout,
  },

  props: {
    member: Object
  }
}
</script>
