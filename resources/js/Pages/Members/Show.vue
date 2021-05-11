<template>
  <div>
    <member-base :member="member">
      <div class="max-w-5xl mx-auto sm:grid grid-cols-3 gap-6">
        <div class="col-span-2">
          <div class="bg-white rounded-lg shadow divide-y divide-gray-200">
            <div class="p-4 sm:px-6 flex items-center justify-between">
              <h2 class="text-lg leading-6 font-medium text-gray-900">
                Personal details
              </h2>
              <div class="space-x-1">
                <inertia-link
                  :href="$route('members.edit', member.id)"
                  title="Edit Member details"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
                  edit
                </inertia-link>
                <button
                  v-if="!member.deleted_at"
                  title="Archive Member"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
                  @click="$inertia.delete($route('members.destroy', member.id))">
                  delete
                </button>
                <button
                  v-else
                  title="Restore Member"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
                  @click="$inertia.put($route('members.restore', member.id))">
                  unarchive
                </button>
              </div>
            </div>
            <div class="px-4 py-5 sm:px-6">
              <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
                <div>
                  <dt class="text-xs font-medium text-gray-400">First Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.first_name }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Last Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.last_name }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Hebrew First Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.hebrew_first_name }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Hebrew Last Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.hebrew_last_name }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Home Phone</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.home_phone }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Cellphone</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.mobile_phone }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Wife's Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.wife_name }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Wife's Cellphone</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.wife_mobile_phone }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Email</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.email }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ member.address }}<br>{{ member.city }} {{ member.postal_code }}
                  </dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Father</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.father }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Father In Law</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.father_in_law }}</dd>
                </div>
                <div>
                  <dt class="text-xs font-medium text-gray-400">Shtibel</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ member.shtibel }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <div class="col-span-1">
          <membership-section :member="member"/>
        </div>
      </div>
    </member-base>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import MemberBase from "@/Pages/Members/MemberBase";
import MembershipFormModal from "@/Pages/Members/MembershipFormModal";
import MembershipSection from "@/Pages/Members/MembershipSection";

export default {
  components: {
    MembershipSection,
    MemberBase
  },

  layout: AppLayout,

  props: {
    member: Object,
  },
}
</script>
