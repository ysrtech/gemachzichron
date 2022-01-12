<template>
  <div class="max-w-7xl mx-auto">

    <app-panel class="overflow-x-auto">
      <template #content>
        <table class="min-w-full divide-y divide-gray-200">
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
  layout: (h, page) => h(AppLayout, {title: 'Missing Subscriptions'}, () => page),

  components: {
    IconButton,
    Money,
    AppPanel,
    SubscriptionFormModal
  },

  data() {
    return {
      AVAILABLE_GATEWAYS,
      GATEWAY_BADGE_COLORS,
      currentSyncingSchedule: null,
    }
  },

  props: {
    conflicts: Array,
  },
}
</script>

