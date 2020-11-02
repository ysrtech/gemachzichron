<template>
  <div class="bg-white shadow rounded-md px-6 pb-4">

    <div class="py-5 flex items-center justify-between">
      <div class="text-xl font-medium">Membership Subscriptions</div>
      <button
        class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
        @click="$emit('create-subscription')">
        add
      </button>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-x-auto">
      <table class="w-full whitespace-no-wrap">

        <tr class="text-left text-xs text-gray-400 uppercase">
          <th class="px-6 py-3 font-medium">Type</th>
          <th class="px-6 py-3 font-medium">Recurrences</th>
          <th class="px-6 py-3 font-medium">Start Date</th>
          <th class="px-6 py-3 font-medium">Recurring Day</th>
          <th class="px-6 py-3 font-medium">Amount</th>
        </tr>

        <tr v-for="subscription in subscriptions"
            :key="subscription.id"
            class="hover:bg-gray-100 cursor-pointer"
            @click="viewSubscription = subscription">
          <td class="border-t px-6 py-3">{{ subscription.type }}</td>
          <td class="border-t px-6 py-3">{{ subscription.recurrences }}</td>
          <td class="border-t px-6 py-3">{{ formatDate(subscription.start_date) }}</td>
          <td class="border-t px-6 py-3">{{ subscription.frequency }} - {{ numberSuffix(subscription.process_day) }}</td>
          <td class="border-t px-6 py-3">${{ subscription.amount.toFixed(2) }}</td>

        </tr>

        <tr v-if="subscriptions.length === 0">
          <td class="border-t px-6 py-4" colspan="5">No subscriptions.</td>
        </tr>

      </table>
    </div>

    <subscription-form-modal
      :subscription="viewSubscription"
      @close="viewSubscription = null;"
    />


  </div>
</template>

<script>
import SubscriptionFormModal from "./SubscriptionFormModal";

export default {
  components: {
    SubscriptionFormModal
  },

  data() {
    return {
      viewSubscription: null,
    }
  },

  props: {
    subscriptions: Array
  },

  methods: {
    numberSuffix(n) {
      return n+=['st','nd','rd'][n%100>>3^1&&n%10]||'th';
    }
  }
}
</script>
