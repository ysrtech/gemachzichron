<template>
  <div class="max-w-7xl mx-auto">

    <div class="mb-4">
      <div class="sm:hidden">
        <select @change="currentTab = tabs.find(tab => tab.label === $event.target.value)" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
          <option v-for="tab in tabs" :selected="currentTab.label === tab.label">{{ tab.label }}</option>
        </select>
      </div>
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button
              @click="currentTab = tab"
              v-for="tab in tabs"
              class="focus:outline-none whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm"
              :class="[currentTab.label === tab.label ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200']">
              {{ tab.label }}
              <span class="hidden ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block" :class="[currentTab.label === tab.label ? 'bg-primary-100 text-primary-600' : 'bg-gray-200 text-gray-900']">
                {{tab.amount}}
              </span>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <app-panel class="overflow-x-auto">
      <template #content>
        <table class="min-w-full divide-y divide-gray-200" v-if="currentTab.label === 'Missing Subscriptions'">
          <thead>
          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th class="px-6 py-3 font-medium" v-for="title in [
              'Member', 'Gateway', 'Gateway schedule ID', 'Start Date',
              'Installments', 'Frequency', 'Status', 'Amount', '',
            ]">{{ title }}</th>

          </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="gatewaySchedule in conflicts.filter(c => c.type === 'Missing Subscription')"
            :key="gatewaySchedule.id"
            class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
            <td class="px-6 py-3.5 whitespace-nowrap">
              <inertia-link
                class="font-medium hover:text-gray-900 hover:underline"
                :href="$route('members.show', gatewaySchedule.member.id)">
                {{ gatewaySchedule.member?.first_name + ' ' + gatewaySchedule.member?.last_name }}
              </inertia-link>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              <app-badge :color="GATEWAY_BADGE_COLORS[gatewaySchedule.gateway]">
                {{ gatewaySchedule.gateway }}
              </app-badge>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ gatewaySchedule.gateway_identifier }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ gatewaySchedule.data.start_date }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ gatewaySchedule.data.installments || 'Unlimited' }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ gatewaySchedule.data.frequency }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              <app-badge :color="gatewaySchedule.data.active ? 'green' : 'red'">
                {{ gatewaySchedule.data.active ? 'Active' : 'Inactive' }}
              </app-badge>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">
              <money :amount="gatewaySchedule.data.amount"/>
            </td>
            <td class="px-3 py-2 whitespace-nowrap flex items-center space-x-2" v-tippy="{content: 'Associate with new subscription'}">
              <icon-button icon="add_task" @click="currentSyncingSchedule = gatewaySchedule"/>
            </td>
          </tr>

          <tr v-if="conflicts.filter(c => c.type === 'Missing Subscription').length === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="9">Everything is up to date</td>
          </tr>

          </tbody>
        </table>
      </template>
    </app-panel>

    <subscription-form-modal
      :syncGatewaySchedule="currentSyncingSchedule"
      :show="!!currentSyncingSchedule"
      @close="currentSyncingSchedule = null"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AppPanel from "@/Components/Panel";
import {AVAILABLE_GATEWAYS, GATEWAY_BADGE_COLORS} from "@/config/gateways";
import SubscriptionFormModal from "@/Components/App/Subscriptions/FormModal"
import Money from "@/Components/Money";
import IconButton from "@/Components/IconButton";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Conflicts'}, () => page),

  components: {
    IconButton,
    Money,
    AppPanel,
    SubscriptionFormModal
  },

  data() {
    return {
      tabs: [
        {
          label: 'Missing Subscriptions',
          amount: this.conflicts.filter(conflict => conflict.type === 'Missing Subscription').length
        },
        {
          label: 'Orphaned Rotessa Transactions',
          amount: this.conflicts.filter(conflict => conflict.type === 'Orphaned Rotessa Transaction').length
        }
      ],
      AVAILABLE_GATEWAYS,
      GATEWAY_BADGE_COLORS,
      currentTab: null,
      currentSyncingSchedule: null,
    }
  },

  props: {
    conflicts: Array,
  },

  created() {
    let t = new URLSearchParams(this.$page.url.split('?')[1]).get('tab')
    this.currentTab = this.tabs.find(tab => tab.label === t) || this.tabs[0]
  },
}
</script>

