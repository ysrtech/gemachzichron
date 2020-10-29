<template>
  <div class="bg-white shadow rounded-md px-6">

    <div class="py-5 flex items-center justify-between">
      <div class="text-xl font-medium">Membership</div>

      <div class="flex items-center w-full justify-end leading-4" v-if="membership">
        <div class="mr-16">
          <span class="text-gray-400 text-xs">Since:</span><br>
          <span class="text-gray-600 text-sm">{{ formatDate(membership.created_at) }}</span>
        </div>
        <div class="mr-16">
          <span class="text-gray-400 text-xs">Membership Type:</span><br>
          <span class="text-gray-600 text-sm">{{ membership.type }}</span>
        </div>
        <div class="mr-16" v-if="membership.plan_type">
          <span class="text-gray-400 text-xs">Plan Type:</span><br>
          <span class="text-gray-600 text-sm">{{ membership.plan_type.name }}</span>
        </div>
        <button
          class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
          @click="editMembership = true">
          edit
        </button>
      </div>

      <div  v-else>
        <span>This person does not not have a membership</span>
        <a href="#" class="text-primary-400 hover:text-primary-600 underline ml-2">Create one now</a>
      </div>

    </div>

    <div class="pb-4" v-if="membership">
      <div class="bg-white rounded border border-gray-200 overflow-x-auto">
        <table class="w-full whitespace-no-wrap">

          <tr class="text-left text-xs text-gray-400 uppercase">
            <th class="px-6 py-3 font-medium">Type</th>
            <th class="px-6 py-3 font-medium">Recurrences</th>
            <th class="px-6 py-3 font-medium">Recurring Day</th>
            <th class="px-6 py-3 font-medium">Recurring Amount</th>
          </tr>

          <tr v-for="subscription in membership.subscriptions"
              :key="subscription.id"
              class="hover:bg-gray-100 cursor-pointer"
              @click="viewSubscription = subscription">
            <td class="border-t px-6 py-3">{{ subscription.type }}</td>
            <td class="border-t px-6 py-3">{{ subscription.recurrences }}</td>
            <td class="border-t px-6 py-3">{{ subscription.frequency }}</td>
            <td class="border-t px-6 py-3">${{ subscription.amount.toFixed(2) }}</td>

          </tr>

          <tr v-if="membership.subscriptions.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No subscription.</td>
          </tr>

        </table>
      </div>
    </div>

<!--    <membership-form-modal-->
<!--      :membership="membership"-->
<!--      :show="addMembership || editMembership"-->
<!--      @close="addMembership = false; editMembership = false;"-->
<!--    />-->

<!--    <subscription-modal-->
<!--      :subscription="viewSubscription"-->
<!--      :show="addSubscription"-->
<!--      @close="addSubscription = false; viewSubscription = null;"-->
<!--    />-->


  </div>
</template>

<script>
export default {
  name: "MembershipSection",

  data() {
    return {
      addMembership: false,
      editMembership: false,
      viewSubscription: null,
      addSubscription: false,
    }
  },

  props: {
    membership: Object
  }
}
</script>
