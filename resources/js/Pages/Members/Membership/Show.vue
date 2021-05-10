<template>
  <div>
    <member-base :member="member">
      <div class="max-w-2xl mx-auto bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <div class="p-4 sm:px-6 flex items-center justify-between">
          <h2 class="text-lg leading-6 font-medium text-gray-900">
            Membership
          </h2>
          <button
            v-if="member.membership"
            @click="openFormModal = true"
            title="Edit Membership"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
        </div>
        <div class="px-4 py-5 sm:px-6 flex items-start justify-between" v-if="member.membership">
          <dl class="space-y-8">
            <div>
              <dt class="text-xs font-medium text-gray-400">Membership Since</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ member.membership ? date(member.membership.created_at) : null }}</dd>
            </div>
            <div>
              <dt class="text-xs font-medium text-gray-400">Membership Type</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ member.membership?.type }}</dd>
            </div>
            <div>
              <dt class="text-xs font-medium text-gray-400">Plan Type</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ member.membership?.plan_type?.name || 'N/A' }}</dd>
            </div>
          </dl>
          <div class="border rounded">
            <div class="-translate-y-4 bg-white font-medium inline-flex px-1 text-gray-400 text-xs transform translate-x-1">Total Paid</div>
            <div class="p-4 pt-0 -mt-2 text-3xl">
              <span class="text-gray-600 mr-1">$</span>
              <span class="font-medium text-gray-900">{{ member.membership?.total_paid || '0.00' }}</span>
            </div>
          </div>
        </div>

        <div v-else class="py-10 text-center text-gray-500">
          <div class="mb-4">Member does not have a membership</div>
          <app-button @click="openFormModal = true">Create membership</app-button>
        </div>
      </div>
      <membership-form-modal
        :show="openFormModal"
        :member-id="member.id"
        :membership="member.membership"
        @close="openFormModal = false"
      />
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import MembershipFormModal from "./FormModal";

export default {
  layout: AppLayout,

  components: {
    MembershipFormModal,
    MemberBase
  },

  data() {
    return {
      openFormModal: false
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
