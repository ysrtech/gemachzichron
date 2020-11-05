<template>
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full whitespace-no-wrap">
      <tr class="text-left text-xs text-gray-400">
        <th class="px-6 pt-6 pb-4 font-medium">Name</th>
        <th class="px-6 pt-6 pb-4 font-medium">Home Phone</th>
        <th class="px-6 pt-6 pb-4 font-medium">Cellphone</th>
        <th class="px-6 pt-6 pb-4 font-medium" colspan="2">Email</th>
      </tr>
      <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-for="member in members" :key="member.id">
        <td class="border-t">
          <inertia-link class="px-6 py-3 flex items-center focus:text-primary-500"
                        :href="route('members.show', member.id)">
            {{ member.last_name + ', ' + member.first_name }}
            <i v-if="member.deleted_at" class="material-icons flex-shrink-0 text-sm text-red-300 ml-2"
               title="Archived member">delete</i>
<!--            <i v-if="member.membership" class="material-icons flex-shrink-0 text-sm text-green-300 ml-2"-->
<!--               title="Active Membership">verified_user</i>-->
          </inertia-link>
        </td>
        <td class="border-t">
          <inertia-link class="px-6 py-3 flex items-center focus:outline-none"
                        :href="route('members.show', member.id)" tabindex="-1">
            {{ member.home_phone }}
          </inertia-link>
        </td>
        <td class="border-t">
          <inertia-link class="px-6 py-3 flex items-center focus:outline-none"
                        :href="route('members.show', member.id)" tabindex="-1">
            {{ member.mobile_phone }}
          </inertia-link>
        </td>
        <td class="border-t">
          <inertia-link class="px-6 py-3 flex items-center focus:outline-none"
                        :href="route('members.show', member.id)" tabindex="-1">
            {{ member.email }}
          </inertia-link>
        </td>
        <td class="border-t w-px p-0">
          <app-dropdown align="right" width="36" class="mx-2">
            <template #trigger>
              <div class="px-2 flex items-center">
                <button class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                  more_vert
                </button>
              </div>
            </template>

            <template #content>
              <app-dropdown-link @click.native="memberBeingArchived = member" as="button" v-if="!member.deleted_at">
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400">archive</i>
                  <div>Archive</div>
                </div>
              </app-dropdown-link>
              <app-dropdown-link @click.native="restoreMember(member)" as="button" v-else>
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400">unarchive</i>
                  <div>Restore</div>
                </div>
              </app-dropdown-link>
              <app-dropdown-link @click.native="duplicateMember(member)" as="button">
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400">content_copy</i>
                  <div>Duplicate</div>
                </div>
              </app-dropdown-link>
            </template>
          </app-dropdown>
        </td>
      </tr>
      <tr v-if="members.length === 0">
        <td class="border-t px-6 py-3" colspan="5">No members found.</td>
      </tr>
    </table>

    <archive-confirmation :member="memberBeingArchived" @close="memberBeingArchived = null"></archive-confirmation>

  </div>

</template>

<script>
import AppDropdown from "../../../Shared/Dropdown";
import AppDropdownLink from "../../../Shared/DropdownLink";
import ArchiveConfirmation from "./ArchiveConfirmation";

export default {
  name: "MembersTable",

  components: {
    ArchiveConfirmation,
    AppDropdown,
    AppDropdownLink,

  },

  data() {
    return {
      memberBeingArchived: null,
      form: this.$inertia.form()
    }
  },

  props: {
    members: Array
  },

  methods: {
    restoreMember(member) {
      this.form.put(route('members.restore', member.id), {
        preserveScroll: true
      })
    },

    duplicateMember(member) {
      this.$inertia.post(this.route('members.store'), member)
    }
  },
}
</script>
