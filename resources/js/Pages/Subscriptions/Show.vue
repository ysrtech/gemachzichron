<template>
  <div class="max-w-2xl mx-auto">
    <app-panel title="Subscription Details" :badge="{
      color: subscription.active ? 'green' : 'red',
      text: subscription.active ? 'Active' : 'Inactive',
    }">
      <template #actions>
        <refresh-button :subscription-id="subscription.id" class="p-1.5"/>
        <button
          v-tippy="{ content: 'Edit Subscription' }"
          @click="openFormModal = true"
          class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
          edit
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
            <key-value label="Comments"><pre class="font-sans">{{ subscription.comment }}</pre></key-value>
            <inertia-link :href="$route('transactions.index', {subscription_id: subscription.id})" class="flex space-x-1">
              <span class="font-medium hover:underline text-sm">View Transactions</span>
              <i class="material-icons-outlined text-base">launch</i>
            </inertia-link>
            <div class="col-span-2">
              <dd class="mt-1 text-sm text-gray-900">
                <ul class="border border-gray-300 rounded-md divide-y divide-gray-200">
                  <li class="px-4 py-2 font-medium border-b border0gray-300">Transaction Charges</li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span class="flex-1">Amount</span>
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
<!--              <table class="w-1/2">-->
<!--                <tr><td>Amount:</td><td><money :amount="subscription.amount"/></td></tr>-->
<!--                <tr><td>Membership Fee:</td><td><money :amount="subscription.membership_fee"/></td></tr>-->
<!--                <tr v-show="subscription.processing_fee"><td>Processing Fee:</td><td><money :amount="subscription.processing_fee"/></td></tr>-->
<!--                <tr v-show="subscription.decline_fee"><td>Processing Fee:</td><td><money :amount="subscription.decline_fee"/></td></tr>-->
<!--                <tr class="border-t"><td>Total:</td><td><money :amount="subscription.transaction_total"/></td></tr>-->
<!--              </table>-->
            </div>
          </dl>
        </div>
      </template>
    </app-panel>
    <teleport v-if="isMounted" to="#header">
      Subscription <span class="text-gray-500">#</span>{{ subscription.id }}
    </teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import IsMounted from "@/Mixins/IsMounted";
import AppPanel from "@/Components/UI/Panel";
import KeyValue from "@/Components/UI/KeyValue";
import AppBadge from "@/Components/UI/Badge";
import {GATEWAY_BADGE_COLORS} from "@/config/gateways";
import {date} from "@/helpers/dates";
import Money from "@/Components/UI/Money";
import RefreshButton from "@/Pages/Subscriptions/RefreshButton";

export default {
  layout: AppLayout,

  mixins: [IsMounted],

  components: {
    RefreshButton,
    Money,
    AppBadge,
    KeyValue,
    AppPanel,
  },

  props: {
    subscription: Object
  },

  data() {
    return {
      openFormModal: false,
      GATEWAY_BADGE_COLORS,
    }
  },

  methods: {
    date,
    formattedGatewayData(data) {
      return `<table>${Object.keys(data).reduce((accumulator, key) => {
        return accumulator + `<tr><td>${key.replaceAll('_', ' ')}: </td><td class="text-right">${data[key]}</td></tr>`
      }, '')}</table>`
    }
  },
}
</script>
