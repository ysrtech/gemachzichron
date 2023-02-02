<template>
  <div class="max-w-2xl mx-auto">
    <app-panel :title="`Subscription #${subscription.id}`" :badge="{
      color: subscription.active ? 'green' : 'red',
      text: subscription.active ? 'Active' : 'Inactive',
    }">
      <template #actions>
        <refresh-button :subscription-id="subscription.id" class="p-1.5" v-show="!subscription.gateway_data?.deleted && subscription.gateway != 'Manual'"/>
        <button
          v-show="!(subscription.gateway == AVAILABLE_GATEWAYS.Rotessa && !subscription.active)"
          v-tippy="{ content: 'Edit Subscription' }"
          @click="openFormModal = true"
          class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
          edit
        </button>
        <button v-show="subscription.has_transactions < 1"
                    @click="$inertia.post($route('subscription.destroy', subscription.id), {preserveScroll: false})"
                    v-tippy="{ content: 'Delete Subscription' }"
                    class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                    delete
                  </button>
      <button v-show="subscription.active"
                    @click="$inertia.post($route('subscription.cancel', subscription.id), {preserveScroll: false})" 
                    v-tippy="{ content: 'Cancel Subscription' }"
                    class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                    cancel
                  </button>
      </template>
      <template #content>
        <div class="px-4 py-5 sm:px-6">
          <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
            <key-value label="Member">
              <inertia-link :href="$route('members.show', subscription.member.id)" class="font-medium hover:underline">
                {{ subscription.member.first_name + ' ' + subscription.member.last_name }}
              </inertia-link>
            </key-value>
            <key-value label="Type" :value="subscription.type"/>
            <key-value label="Gateway">
              <app-badge
                :color="GATEWAY_BADGE_COLORS[subscription.gateway]"
                v-tippy="{content: formattedGatewayData(subscription.gateway_data), allowHTML: true}">
                {{subscription.gateway}}
              </app-badge>
            </key-value>
            <key-value label="Start Date">{{date(subscription.start_date)}}</key-value>
            <key-value label="Frequency">
              <app-badge color="gray">{{subscription.frequency}}</app-badge>
            </key-value>
            <key-value label="Installments" :value="subscription.installments"/>
            <key-value label="Comments"><div class="font-sans whitespace-pre-wrap">{{ subscription.comment }}</div></key-value>
            <inertia-link :href="$route('transactions.index', {subscription_id: subscription.id})" class="flex space-x-1">
              <span class="font-medium hover:underline text-sm">View Transactions 
                <span v-show="subscription.transactions_sum > 0">({{subscription.transactions_count}}) <money :amount="subscription.transactions_sum"/></span>
                </span>
              <i class="material-icons-outlined text-base">launch</i>
            </inertia-link>
            <div class="flex justify-around col-span-2" v-show="subscription.gateway === AVAILABLE_GATEWAYS.Manual">
              <button
                @click="openTransactionFormModal = true"
                class="px-6 py-2 uppercase text-xs rounded bg-primary-50 hover:bg-primary-100 font-medium">
                Make Transaction
              </button>
            </div>
            <div class="col-span-2">
              <dd class="mt-1 text-sm text-gray-900">
                <ul class="border border-gray-300 rounded-md divide-y divide-gray-200">
                  <li class="px-4 py-2 font-medium border-b border0gray-300">Transaction Charges</li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1">Base Amount</span>
                    <money class="font-medium" :amount="subscription.amount"/>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1">Membership Fee</span>
                    <money class="font-medium" :amount="subscription.membership_fee"/>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1">Processing Fee</span>
                    <money class="font-medium" :amount="subscription.processing_fee"/>
                  </li>
                  <li v-show="subscription.decline_fee" class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1" title="Previous declined transactions">Decline Fees</span>
                    <money class="font-medium" :amount="subscription.decline_fee"/>
                  </li>
                  <hr>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1">Total</span>
                    <money class="font-medium" :amount="subscription.transaction_total"/>
                  </li>
                </ul>
              </dd>
            </div>
          </dl>
        </div>
      </template>
    </app-panel>

    <subscription-form-modal
      :show="openFormModal"
      @close="openFormModal = false"
      :subscription="subscription"
    />

    <transaction-form-modal
      v-if="subscription.gateway === AVAILABLE_GATEWAYS.Manual"
      :show="openTransactionFormModal"
      @close="openTransactionFormModal = false"
      :subscription="subscription"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AppPanel from "@/Components/Panel";
import KeyValue from "@/Components/KeyValue";
import {AVAILABLE_GATEWAYS, GATEWAY_BADGE_COLORS} from "@/config/gateways";
import {date} from "@/helpers/dates";
import Money from "@/Components/Money";
import RefreshButton from "@/Components/App/Subscriptions/RefreshButton";
import SubscriptionFormModal from "@/Components/App/Subscriptions/FormModal"
import TransactionFormModal from "@/Components/App/Transactions/FormModal";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Subscriptions'}, () => page),

  components: {
    RefreshButton,
    Money,
    KeyValue,
    AppPanel,
    SubscriptionFormModal,
    TransactionFormModal,
  },

  props: {
    subscription: Object
  },

  data() {
    return {
      openFormModal: false,
      openTransactionFormModal: false,
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
    }
  },
}
</script>
