<template>
  <table class="min-w-full divide-y divide-gray-200">
      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <table-header
          v-for="header in headers"
          :name="header.name"
          :value="header.value"
          :current="sort"
          v-show="!header.hidden"
          @sort="updateSort"
        />
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
        <td class="px-6 py-3.5 whitespace-nowrap"><span v-show="subscription.transactions_count > 0">{{ subscription.transactions_count }}</span></td>
        <td class="px-6 py-3.5 whitespace-nowrap"><span v-show="subscription.transactions_sum > 0"><money :amount="subscription.transactions_sum"/></span></td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ subscription.type }}</td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ date(subscription.start_date) }}</td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge
            :color="GATEWAY_BADGE_COLORS[subscription.gateway]"
            v-tippy="{content: formattedGatewayData(subscription.gateway_data), allowHTML: true}">
            {{ subscription.gateway }}
          </app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge v-show="subscription.active" color="green">Active</app-badge>
          <app-badge v-show="!subscription.active" color="red">Inactive</app-badge>
        </td>
        <td class="px-3 py-2 whitespace-nowrap flex items-center space-x-2" @click.stop>
          <refresh-button :subscription-id="subscription.id" v-show="!subscription.gateway_data?.deleted && subscription.gateway != 'Manual'"/>
        </td>
      </inertia-link>

      <tr v-if="subscriptions.length === 0">
        <td class="px-6 py-10 text-center text-gray-500" colspan="9">No Subscriptions Found.</td>
      </tr>

      </tbody>
  </table>
</template>

<script>
import Money from "@/Components/Money";
import {GATEWAY_BADGE_COLORS} from "@/config/gateways";
import {date} from "@/helpers/dates";
import RefreshButton from "@/Components/App/Subscriptions/RefreshButton";
import TableHeader from "@/Components/TableHeader.vue";

export default {
  components: {
    TableHeader,
    RefreshButton,
    Money,
  },

  props: {
    subscriptions: {
      type: Array,
      required: true,
    },
    showMember: {
      type: Boolean,
      default: false
    },
    sort: {
      type: String,
      default: null
    }
  },

  emits: ['update:sort'],

  data() {
    return {
      headers: [
        {value: 'ID', name: 'id'},
        {value: 'Member', name: 'member_last_name,member_first_name', hidden: !this.showMember},
        {value: 'Transaction Amount'},
        {value: 'Frequency', name: 'frequency'},
        {value: 'Installments', name: 'installments'},
        {value: 'Transactions'},
        {value: 'Total Paid'},
        {value: 'Type', name: 'type'},
        {value: 'Start Date', name: 'start_date'},
        {value: 'Gateway', name: 'gateway'},
        {value: 'Status', name: 'active'},
        {},
      ],
      GATEWAY_BADGE_COLORS,
    }
  },

  methods: {
    date,
    formattedGatewayData(data) {
      if (!data) return;
      return `<table>${Object.keys(data).reduce((accumulator, key) => {
        return accumulator + `<tr><td>${key.replaceAll('_', ' ')}: </td><td class="text-right">${data[key]}</td></tr>`
      }, '')}</table>`
    },
    updateSort(sort) {
      this.$emit('update:sort', sort)
    }
  }
}
</script>
