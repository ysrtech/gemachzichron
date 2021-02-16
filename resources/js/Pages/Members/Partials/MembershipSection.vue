<template>
  <div class="w-full">

    <div class="flex space-x-1 items-center">
      <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Membership</h3>
      <button
        :title="member.membership ? 'Edit Membership' : 'Create Membership'"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="openMembershipModal = true">
        {{ member.membership ? 'edit' : 'add' }}
      </button>
    </div>

    <div class="bg-white rounded-lg shadow divide-y divide-gray-200 w-full mt-2 mx-1">
      <div class="px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-2 gap-x-4 gap-y-8" v-if="member.membership">
          <div>
            <dt class="text-xs font-medium text-gray-400">Membership Type</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.membership?.type }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Plan Type</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.membership?.plan_type?.name || 'N/A' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Membership Since</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.membership ? date(member.membership.created_at) : null }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Total Paid</dt>
            <dd class="mt-1 text-sm text-gray-900">${{ member.membership?.total_paid || 0 }}</dd>
          </div>
        </dl>
        <div v-else class="py-10 text-center text-gray-500">
          <div class="mb-4">Member does not have a membership</div>
          <app-button @click="openMembershipModal = true">Create membership</app-button>
        </div>
      </div>
    </div>

    <membership-modal
      :member="member"
      :show="openMembershipModal"
      @close="openMembershipModal = false"
    />

  </div>
</template>

<script>
import MembershipModal from "@/Pages/Members/Partials/MembershipModal";
import {date} from "@/helpers/dates";

export default {

  components: {
    MembershipModal,
  },

  data() {
    return {
      openMembershipModal: false,
    }
  },

  props: {
    member: Object,
  },

  methods: {
    date
  }
}
</script>
