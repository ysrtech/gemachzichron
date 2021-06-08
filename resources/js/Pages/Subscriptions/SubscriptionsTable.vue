<template>
  <table class="min-w-full divide-y divide-gray-200">
      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th class="px-6 py-3 font-medium">ID</th>
        <th class="px-6 py-3 font-medium" v-show="showMember">Member</th>
        <th class="px-6 py-3 font-medium">Transaction Total</th>
        <th class="px-6 py-3 font-medium">Frequency</th>
        <th class="px-6 py-3 font-medium">Installments</th>
        <th class="px-6 py-3 font-medium">Type</th>
        <th class="px-6 py-3 font-medium">Start Date</th>
        <th class="px-6 py-3 font-medium">Gateway</th>
        <th class="px-6 py-3 font-medium">Status</th>
        <th class="px-6 py-3 font-medium"></th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <inertia-link
        as="tr"
        :href="$route('subscriptions.show', subscription.id)"
        v-for="subscription in subscriptions"
        :key="subscription.id"
        class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
        <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.id }}</td>
        <td v-if="showMember" class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
          <inertia-link
            @click.stop
            class="font-medium hover:text-gray-900 hover:underline"
            :href="$route('members.show', subscription.member.id)">
            {{ subscription.member.first_name + ' ' + subscription.member.last_name }}
          </inertia-link>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <money :amount="subscription.transaction_total"/>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge color="gray">{{ subscription.frequency }}</app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.installments || 'Unlimited' }}</td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.type }}</td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ date(subscription.start_date) }}</td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge :color="GATEWAY_BADGE_COLORS[subscription.gateway]">{{ subscription.gateway }}</app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge v-show="subscription.active" color="green">Active</app-badge>
          <app-badge v-show="!subscription.active" color="red">Inactive</app-badge>
        </td>
        <td class="px-3 py-3.5 whitespace-nowrap flex items-center space-x-2" @click.stop>
          <refresh-button :subscription-id="subscription.id"/>
        </td>
      </inertia-link>

      <tr v-if="subscriptions.length === 0">
        <td class="px-6 py-10 text-center text-gray-500" colspan="9">No Subscriptions Found.</td>
      </tr>

      </tbody>
  </table>
</template>

<script>
import AppBadge from "@/Components/UI/Badge";
import Money from "@/Components/UI/Money";
import {GATEWAY_BADGE_COLORS} from "@/config/gateways";
import {date} from "@/helpers/dates";
import RefreshButton from "@/Pages/Subscriptions/RefreshButton";

export default {
  components: {
    RefreshButton,
    Money,
    AppBadge
  },

  props: {
    subscriptions: {
      type: Array,
      required: true,
    },
    showMember: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      GATEWAY_BADGE_COLORS,
    }
  },

  methods: {
    date
  }
}
</script>
