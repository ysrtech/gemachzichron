<template>
  <div>
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
      <tr v-for="transaction in transactions" :key="transaction.id"
          class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
        <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ transaction.id }}</td>
        <td v-if="showMember" class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
          <inertia-link
            class="font-medium hover:text-gray-900 hover:underline"
            :href="$route('members.show', transaction.member.id)">
            {{ transaction.member.first_name + ' ' + transaction.member.last_name }}
          </inertia-link>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <inertia-link
            class="hover:text-gray-900 hover:underline"
            :href="$route('subscriptions.show', transaction.subscription_id)">
            {{ transaction.subscription_id }}
          </inertia-link>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap font-medium">
          <money :amount="transaction.amount"/>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">{{ date(transaction.process_date) }}</td>

        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge
            :color="TRANSACTION_STATUS_COLORS[transaction.status]"
            v-tippy="{ content: transaction.status_message }">
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
            v-tippy="{content: formattedGatewayData(transaction.gateway_data), allowHTML: true}"
            :color="GATEWAY_BADGE_COLORS[transaction.gateway]">
            {{ transaction.gateway }}
          </app-badge>
        </td>
        <td class="px-3 py-3.5 whitespace-nowrap flex items-center space-x-2" @click.stop>
        <span
          v-show="transaction.comment"
          v-tippy="{ content: transaction.comment }"
          class="material-icons-outlined text-gray-400 cursor-default">
          mode_comment
        </span>
        <button
          v-show="transaction.status === TRANSACTION_STATUSES.Fail"
          v-tippy="{content: 'Resolve [new single-installment subscription]'}"
          @click="resolveFailedTransaction = transaction"
          class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
          add_task
        </button>

         <button
          v-show="transaction.gateway === AVAILABLE_GATEWAYS.Manual"
          v-tippy="{content: 'Delete Transaction'}"
          @click="$inertia.delete($route('transaction.destroy', transaction.id), {preserveScroll: true})"
          class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
        delete
        </button>
<!--        <button-->
<!--          v-show="transaction.status === TRANSACTION_STATUSES.Pending-->
<!--                && transaction.gateway === AVAILABLE_GATEWAYS.Manual"-->
<!--          v-tippy="{content: 'Finish Transaction'}"-->
<!--          class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">-->
<!--          price_check-->
<!--        </button>-->
        </td>
      </tr>

      </tbody>
    </table>

    <subscription-form-modal
      :show="!!resolveFailedTransaction"
      :resolves-failed-transaction="resolveFailedTransaction"
      @close="resolveFailedTransaction = null"
    />

  </div>
</template>

<script>
import Money from "@/Components/Money";
import {date} from "@/helpers/dates";
import {TRANSACTION_STATUS_COLORS, TRANSACTION_STATUSES} from "@/config/transactions";
import {AVAILABLE_GATEWAYS, GATEWAY_BADGE_COLORS} from "@/config/gateways";
import SubscriptionFormModal from "@/Components/App/Subscriptions/FormModal";
import TableHeader from "@/Components/TableHeader.vue";

export default {
  components: {
    TableHeader,
    SubscriptionFormModal,
    Money
  },

  props: {
    member: Object,
    transactions: Array,
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

  watch: {
    resolveFailedTransaction(val) {
      if (!val) return;
      if (!this.member) return;
      this.resolveFailedTransaction.member = this.member // for creating new subscription in form modal
    }
  },

  data() {
    return {
      headers: [
        {value: 'ID', name: 'id'},
        {value: 'Member', name: 'member_last_name,member_first_name', hidden: !this.showMember},
        {value: 'Subscription ID', name: 'subscription_id'},
        {value: 'Amount', name: 'amount'},
        {value: 'Process Date', name: 'process_date'},
        {value: 'Status', name: 'status'},
        {value: 'Type', name: 'type'},
        {value: 'Gateway', name: 'gateway'},
        {},
      ],
      resolveFailedTransaction: null,
      TRANSACTION_STATUSES,
      TRANSACTION_STATUS_COLORS,
      AVAILABLE_GATEWAYS,
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
