<template>
  <div class="bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
    <div class="p-4 sm:px-6 flex items-center justify-between">
      <div class="flex space-x-2 items-baseline">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Membership</h2>
        <app-badge v-if="member.membership?.is_active === false" color="red">Inactive</app-badge>
      </div>
      <button
        v-if="member.membership"
        @click="openMembershipFormModal = true"
        title="Edit Membership"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
        edit
      </button>
    </div>
    <template v-if="member.membership">
      <div class="px-4 py-5 sm:px-6">
        <dl class="space-y-8">
          <key-value label="Membership Since" :value="date(member.membership.created_at)"/>
          <key-value label="Membership Type" :value="member.membership.type"/>
          <key-value label="Plan Type" :value="member.membership.plan_type?.name || 'N/A'"/>
        </dl>
      </div>
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div class="font-medium text-gray-400">Total Paid</div>
        <div class="text-2xl">
          <span class="text-gray-600 mr-1">$</span>
          <span class="font-medium text-gray-900">{{ Number(member.membership.total_paid || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
        </div>
      </div>
    </template>

    <div v-else class="py-10 text-center text-gray-500">
      <div class="pb-4 px-6 text-sm">Member does not have a membership</div>
      <app-button @click="openMembershipFormModal = true">Create membership</app-button>
    </div>
    <membership-form-modal
      :show="openMembershipFormModal"
      :member-id="member.id"
      :membership="member.membership"
      @close="openMembershipFormModal = false"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import MembershipFormModal from "./MembershipFormModal";
import AppBadge from "@/Components/UI/Badge";
import KeyValue from "@/Components/UI/KeyValue";

export default {
  layout: AppLayout,

  components: {
    KeyValue,
    AppBadge,
    MembershipFormModal,
  },

  data() {
    return {
      openMembershipFormModal: false
    }
  },

  props: {
    member: Object,
  },

  methods: {
    date,
  }
}
</script>
