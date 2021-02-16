<template>
  <div class="w-full">

    <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Given Endorsments</h3>

    <div class="bg-white rounded-lg shadow w-full mt-2 mx-1 p-4">

          <app-badge color="gray" v-for="endorsement in endorsements" class="m-1.5">
            <inertia-link
              class="hover:text-black"
              :href="$route('members.show', endorsement.membership.member.id)">
              {{ endorsement.membership.member.first_name + ' ' + endorsement.membership.member.last_name }}
            </inertia-link>
          </app-badge>

          <div v-if="endorsements.length === 0" class="py-10 text-center text-gray-500">
            No Given Endorsements
          </div>

    </div>
  </div>

</template>

<script>
import AppBadge from "@/Components/UI/Badge";
export default {
  components: {AppBadge},
  props: {
    endorsements: Array,
  },
}
</script>
