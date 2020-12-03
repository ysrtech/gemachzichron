<template>
  <div>
    <div class="bg-white shadow rounded-md px-6 pb-4">

      <div class="py-4 flex justify-between">
        <div class="text-xl font-medium">Membership</div>
        <button
          v-if="membership"
          class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
          @click="editMembership = true">
          edit
        </button>
      </div>


      <div v-if="membership">
        <div class="grid grid-cols-5 gap-6">

          <div class="col-span-3">
            <div class="py-1">
              <span class="font-bold">Membership Since:</span>
              <span>{{ formatDate(membership.created_at) }}</span>
            </div>
            <div class="py-1">
              <span class="font-bold">Membership Type:</span>
              <span>{{ membership.type }}</span>
            </div>
            <div class="py-1">
              <span class="font-bold">Plan Type:</span>
              <span v-if="membership.plan_type">{{ membership.plan_type.name }}</span>
            </div>
          </div>

          <div class="col-span-2">
            <div class="py-1 text-xl">
              <span class="font-bold">Total Paid:</span>
              <span class="font-medium">${{ membership.total_paid || 0 }}</span>
            </div>

            <inertia-link :href="$route('invoices.index', {'membership_id': membership.id})" class="py-1 flex items-center text-sm font-medium text-gray-500">
              <span class="mr-1 text-sm hover:underline">View Invoices</span>
              <span class="material-icons text-lg">launch</span>
            </inertia-link>
          </div>

        </div>
      </div>

      <div v-else>
        <span>This person does not not have a membership</span>
        <button
          class="text-primary-400 hover:text-primary-600 underline ml-2 focus:outline-none"
          @click="$emit('create-membership')">
          Create one now
        </button>
      </div>

    </div>

        <membership-form-modal
          :membership="membership"
          :show="editMembership"
          @close="editMembership = false"
        />

  </div>
</template>

<script>
import MembershipFormModal from "./MembershipFormModal";
export default {

  components: {
    MembershipFormModal
  },

  data() {
    return {
      addMembership: false,
      editMembership: false,
    }
  },

  props: {
    membership: Object
  }
}
</script>
