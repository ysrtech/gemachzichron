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
            <div class="col-span-1 border rounded-md">
              <div class="font-medium uppercase text-gray-400 border-b px-5 py-2">Total Paid:</div>
              <div class="px-5 py-3 font-medium text-lg">${{ '' || 0 }}</div>
            </div>
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
