<template>
  <table class="min-w-full divide-y divide-gray-200">
    <thead>
    <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
      <th
        v-for="title in ['ID', 'Member', 'Subscription ID', 'Amount', 'Procees Date', 'Status', 'Type', 'Gateway', '']"
        class="px-6 py-3 font-medium">{{ title }}
      </th>
    </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-200">
    <inertia-link
      as="tr"
      :href="$route('transactions.show', transaction.id)"
      v-for="transaction in transactions"
      :key="transaction.id"
      class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
      <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ transaction.id }}</td>
      <td class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
        <inertia-link
          @click.stop
          class="font-medium hover:text-gray-900 hover:underline"
          :href="$route('members.show', transaction.member.id)">
          {{ transaction.member.first_name + ' ' + transaction.member.last_name }}
        </inertia-link>
      </td>
      <td class="px-6 py-3.5 whitespace-nowrap">{{ transaction.subscription_id }}</td>
      <td class="px-6 py-3.5 whitespace-nowrap font-medium">
        <money :amount="transaction.amount"/>
      </td>
      <td class="px-6 py-3.5 whitespace-nowrap">{{ date(transaction.process_date) }}</td>

      <td class="px-6 py-3.5 whitespace-nowrap">
        <app-badge
          :color="TRANSACTION_STATUS_COLORS[transaction.status]"
          v-tippy="{ content: transaction.error_message }">
          {{ Object.keys(TRANSACTION_STATUSES)[Object.values(TRANSACTION_STATUSES).indexOf(transaction.status)] }}
        </app-badge>
      </td>
      <td class="px-6 py-3.5 whitespace-nowrap">
        <app-badge color="gray">
          {{ transaction.type }}
        </app-badge>
      </td>
      <td class="px-6 py-3.5 whitespace-nowrap">
        <app-badge
          v-tippy="{content: formattedGatewayData(transaction.data, transaction.gateway_identifier), allowHTML: true}"
          :color="GATEWAY_BADGE_COLORS[transaction.gateway]">
          {{ transaction.gateway }}
        </app-badge>
      </td>
      <td class="px-3 py-3.5 whitespace-nowrap flex items-center space-x-2" @click.stop>
        <span
          v-show="transaction.comment"
          v-tippy="{ content: transaction.comment }"
          class="material-icons-outlined text-gray-400">
          mode_comment
        </span>
        <button
          v-show="transaction.status === TRANSACTION_STATUSES.Fail"
          v-tippy="{content: 'Resolve (create new subscription)'}"
          class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
          refresh
        </button>
        <button
          v-show="transaction.status === TRANSACTION_STATUSES.Pending
                  && transaction.gateway === AVAILABLE_GATEWAYS.Manual"
          v-tippy="{content: 'Finish Transaction'}"
          class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
          price_check
        </button>
      </td>
    </inertia-link>

    </tbody>
  </table>

</template>

<script>
import Money from "@/Components/UI/Money";
import AppBadge from "@/Components/UI/Badge";
import {date} from "@/helpers/dates";
import {TRANSACTION_STATUS_COLORS, TRANSACTION_STATUSES} from "@/config/transactions";
import {AVAILABLE_GATEWAYS, GATEWAY_BADGE_COLORS} from "@/config/gateways";

export default {
  components: {AppBadge, Money},
  props: {
    transactions: Array
  },

  data() {
    return {
      TRANSACTION_STATUSES,
      TRANSACTION_STATUS_COLORS,
      AVAILABLE_GATEWAYS,
      GATEWAY_BADGE_COLORS
    }
  },

  methods: {
    date,
    formattedGatewayData(data, id) {
      let formatted = `<tr><td>id:</td><td class="text-right">${id}</td></tr>`
      Object.keys(data).forEach(key => {
        formatted += `<tr><td>${key}:</td><td class="text-right">${data[key]}</td></tr>`
      })
      return `<table>${formatted}</table>`
    }
  }
}
</script>
