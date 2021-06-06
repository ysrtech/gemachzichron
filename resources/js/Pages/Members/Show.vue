<template>
  <div>
    <member-base :member="member">
      <div class="max-w-5xl mx-auto sm:grid grid-cols-3 gap-6">
        <div class="col-span-2">
          <app-panel title="Personal details">
            <template #actions>
              <div class="space-x-1">
                <inertia-link
                  :href="$route('members.edit', member.id)"
                  v-tippy="{ content: 'Edit Member details' }"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
                  edit
                </inertia-link>
                <button
                  v-if="!member.deleted_at"
                  v-tippy="{ content: 'Archive Member' }"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
                  @click="$inertia.delete($route('members.destroy', member.id))">
                  delete
                </button>
                <button
                  v-else
                  v-tippy="{ content: 'Restore Member' }"
                  class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
                  @click="$inertia.put($route('members.restore', member.id))">
                  unarchive
                </button>
              </div>
            </template>
            <template #content>
              <div class="px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
                  <key-value label="First Name" :value="member.first_name"/>
                  <key-value label="Last Name" :value="member.last_name"/>
                  <key-value label="Hebrew First Name" :value="member.hebrew_first_name"/>
                  <key-value label="Hebrew Last Name" :value="member.hebrew_last_name"/>
                  <key-value label="Home Phone" :value="member.home_phone"/>
                  <key-value label="Cellphone" :value="member.cell_phone"/>
                  <key-value label="Wife's Name" :value="member.wife_name"/>
                  <key-value label="Wife's Cellphone" :value="member.wife_cell_phone"/>
                  <key-value label="Email" :value="member.email"/>
                  <key-value label="Address">
                    {{member.address}}<br>{{member.city}} {{member.postal_code}}
                  </key-value>
                  <key-value label="Father" :value="member.father"/>
                  <key-value label="Father In Law" :value="member.father_in_law"/>
                  <key-value label="Shtibel" :value="member.shtibel"/>
                </dl>
              </div>
            </template>
          </app-panel>
        </div>
        <div class="col-span-1 space-y-6">
          <membership-section :member="member"/>
        </div>
      </div>
    </member-base>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/PersistentAppLayout";
import MemberBase from "@/Pages/Members/MemberBase";
import MembershipSection from "@/Pages/Members/MembershipSection";
import KeyValue from "@/Components/UI/KeyValue";
import AppPanel from "@/Components/UI/Panel";

export default {
  components: {
    AppPanel,
    KeyValue,
    MembershipSection,
    MemberBase
  },

  layout: AppLayout,

  props: {
    member: Object,
  },
}
</script>
