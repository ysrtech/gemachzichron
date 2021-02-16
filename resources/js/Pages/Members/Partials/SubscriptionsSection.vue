<template>
  <div class="w-full">

    <div class="flex space-x-1 items-center">
      <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Membership Subscriptions</h3>
      <button
        title="Add Subscription"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="openSubscriptionModal = true">
        add
      </button>
    </div>

    <div class="bg-white rounded-lg shadow w-full mt-2 mx-1 overflow-x-auto overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead v-if="subscriptions.length > 0">

          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th class="px-6 py-3 font-medium" v-for="title in ['Type',	'Recurrences',	'Start date',	'Recurring day',	'Amount', '']">{{ title }}</th>
          </tr>

        </thead>
        <tbody class="bg-white divide-y divide-gray-200">

          <tr
            v-for="subscription in subscriptions"
            :key="subscription.id"
            @click="subscriptionToEdit = subscription; openSubscriptionModal = true"
            class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer"
          >
            <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.type }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.recurrences }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(subscription.start_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.frequency }} - {{ numberSuffix(subscription.process_day) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">${{ subscription.amount.toFixed(2) }}</td>
            <td class="pr-5 text-center w-px whitespace-nowrap text-gray-500 cursor-default" @click.stop>
              <inertia-link :href="$route('invoices.index', {'subscription_id': subscription.id})">
                <button
                  title="View Invoices"
                  class="material-icons-outlined focus:outline-none rounded-full text-lg py-1 px-2 hover:bg-gray-200 focus:bg-gray-300">
                  read_more
                </button>
              </inertia-link>
            </td>
          </tr>

          <tr v-if="subscriptions.length === 0">
            <td class="px-6 py-10 text-center text-gray-500">No subscriptions.</td>
          </tr>

        </tbody>
      </table>
    </div>

    <subscription-modal
      :show="openSubscriptionModal"
      :subscription="subscriptionToEdit"
      :membershipId="membershipId"
      @close="openSubscriptionModal = false; subscriptionToEdit = null"
    />

  </div>
</template>

<script>
import SubscriptionModal from "@/Pages/Members/Partials/SubscriptionModal";
import {date} from "@/helpers/dates";
import {numberSuffix} from "@/helpers/numbers";

export default {

  components: {
    SubscriptionModal,
  },

  data() {
    return {
      openSubscriptionModal: false,
      subscriptionToEdit: null,
    }
  },

  props: {
    subscriptions: Array,
    membershipId: Number
  },

  methods: {
    date,
    numberSuffix
  }
}
</script>
