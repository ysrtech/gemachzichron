<template>
  <div class="bg-white shadow rounded-md px-6">

    <div class="py-4 flex justify-between">
      <div class="text-xl font-medium">Member Information</div>
      <button class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
              @click="showEditModal = true">
        edit
      </button>
    </div>

    <div class="py-4">
      <div class="grid grid-cols-2 gap-6">

        <div class="col-span-1">
          <div class="py-1">
            <span class="font-bold">First Name:</span>
            <span>{{ member.first_name }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Last Name:</span>
            <span>{{ member.last_name }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Hebrew Name:</span>
            <span>{{ member.hebrew_name }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Wife's Name:</span>
            <span>{{ member.wife_name }}</span>
          </div>
        </div>
        <div class="col-span-1">
          <div class="py-1">
            <span class="font-bold">Email:</span>
            <span>{{ member.email }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Home Phone:</span>
            <span>{{ member.home_phone }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Cellphone:</span>
            <span>{{ member.mobile_phone }}</span>
          </div>
          <div class="py-1">
            <span class="font-bold">Shtibel:</span>
            <span>{{ member.shtibel }}</span>
          </div>
        </div>

      </div>
    </div>

    <member-form-modal
      :member="member"
      :show="showEditModal"
      title="Edit Member"
      button="Update"
      @close="showEditModal = false"
    />

  </div>
</template>

<script>
import MemberFormModal from "./MemberFormModal";

export default {
  name: "MemberInfoCard",
  components: {
    MemberFormModal
  },
  data() {
    return {
      showEditModal: false,
    }
  },

  props: {
    member: Object
  }
}
</script>

