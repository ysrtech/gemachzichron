<template>
  <div>
    <app-panel
      title="Membership"
      :badge="member.active_membership === false ? {color: 'red', text: 'Inactive'} : null">
      <template #actions>
        <button
          v-if="member.membership_since"
          @click="openMembershipFormModal = true"
          v-tippy="{ content: 'Edit Membership' }"
          class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
          edit
        </button>
      </template>
      <template #content>
        <template v-if="member.membership_since">
          <div class="px-4 py-5 sm:px-6">
            <dl class="space-y-8">
              <key-value label="Membership Since" :value="date(member.membership_since)"/>
              <key-value label="Membership Type">
                {{ member.membership_type }} <span v-if="member.plan_type">({{ member.plan_type.name }})</span>
              </key-value>
            </dl>
          </div>
          <div class="px-4 py-5 sm:px-6 flex items-center">
            <div class="font-medium text-gray-400 flex-1" title="Excluding Fees">Total Paid <span class="text-xs font-normal">(Membership)</span></div>
            <div class="text-2xl">
              <money
                :amount="member.membership_payments_total || 0"
                class="font-medium text-gray-900 text-right"
                currency-sign-class="font-normal text-gray-600 mr-1"/>
            </div>
          </div>
        </template>

        <div v-else class="py-10 text-center text-gray-500">
          <div class="pb-4 px-6 text-sm">Member does not have a membership</div>
          <app-button @click="openMembershipFormModal = true">Create membership</app-button>
        </div>
      </template>
    </app-panel>

    <membership-form-modal
      :show="openMembershipFormModal"
      :member="member"
      @close="openMembershipFormModal = false"
    />
  </div>
</template>

<script>
import {date} from "@/helpers/dates";
import MembershipFormModal from "./MembershipFormModal";
import KeyValue from "@/Components/UI/KeyValue";
import Money from "@/Components/UI/Money";
import AppPanel from "@/Components/UI/Panel";

export default {

  components: {
    AppPanel,
    Money,
    KeyValue,
    MembershipFormModal,
  },

  data() {
    return {
      openMembershipFormModal: false,
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
