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
              <div v-if="member.plan_type" class="p-3 bg-blue-50 border border-blue-200 rounded">
                <p class="text-sm text-gray-600 mb-2">Membership Type</p>
                <p class="text-base text-blue-600 mb-3">{{ member.membership_type }} ({{ member.plan_type.name }})</p>
                <div v-if="currentRate">
                  <p class="text-lg font-semibold text-blue-600">${{ currentRate }}/month</p>
                </div>
              </div>
              <div v-else>
                <key-value label="Membership Type">
                  {{ member.membership_type }}
                </key-value>
              </div>
              <key-value label="Created on" :value="date(member.created_at)"/>
              <key-value label="Updated on" :value="date(member.updated_at)"/>
            </dl>
          </div>
          <div class="px-4 py-5 sm:px-6 flex items-center">
            <div class="font-medium text-gray-400 flex-1" title="Excluding Fees">Total Paid</div>
            <div class="text-2xl">
              <money
                :amount="member.membership_payments_total || 0"
                class="font-medium text-gray-900 text-right"
                currency-sign-class="font-normal text-gray-600 mr-1"/>
            </div>
          </div>
          <div class="px-4 py-5 sm:px-6 flex items-center">
            <div class="font-medium text-gray-400 flex-1" title="Excluding Fees">Total Due</div>
            <div class="text-2xl">
              <money
                :amount="member.membership_due || 0"
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
import KeyValue from "@/Components/KeyValue";
import Money from "@/Components/Money";
import AppPanel from "@/Components/Panel";

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
  },

  computed: {
    currentRate() {
      if (!this.member.plan_type || !this.member.plan_type.rates) {
        return null
      }

      const rates = this.member.plan_type.rates
      
      // Handle both object and array formats
      if (Array.isArray(rates)) {
        return null
      }

      const today = new Date().toISOString().split('T')[0]
      const ratesObj = typeof rates === 'object' ? rates : JSON.parse(rates)
      
      // Get all dates and sort them
      const allDates = Object.keys(ratesObj).sort()
      
      // Find the most recent rate that is effective on or before today
      let applicableRate = null
      for (const date of allDates) {
        if (date <= today) {
          applicableRate = ratesObj[date]
        } else {
          break
        }
      }

      return applicableRate
    }
  }
}
</script>
