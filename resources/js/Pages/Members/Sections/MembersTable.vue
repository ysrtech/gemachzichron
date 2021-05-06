<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">

      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th v-for="title in ['Name', 'Home Phone', 'Cellphone', 'Email', '']" class="px-6 py-3 font-medium">{{ title }}</th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <tr
        @click="$inertia.get($route('members.show', member.id))"
        v-for="member in members.data"
        :key="member.id"
        class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.last_name + ', ' + member.first_name }}
          <app-badge v-if="member.deleted_at" color="red" class="ml-1">Archived Member</app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.home_phone }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.mobile_phone }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.email }}
        </td>

        <td class="px-6 text-right w-px whitespace-nowrap text-sm text-gray-500 cursor-default" @click.stop>
          <app-dropdown align="right" width="36">
            <template #trigger>
              <button class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                more_vert
              </button>
            </template>

            <template #content>
              <app-dropdown-link as="button" @click="$emit('edit-member', member)">
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400 text-xl">edit</i>
                  <div>Edit</div>
                </div>
              </app-dropdown-link>
              <app-dropdown-link as="button" @click="duplicateMember(member)">
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400 text-xl">content_copy</i>
                  <div>Duplicate</div>
                </div>
              </app-dropdown-link>
              <app-dropdown-link as="button" @click="$inertia.delete($route('members.destroy', member.id))" v-if="!member.deleted_at">
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400 text-xl">archive</i>
                  <div>Archive</div>
                </div>
              </app-dropdown-link>
              <app-dropdown-link as="button" @click="$inertia.put($route('members.restore', member.id))" v-else>
                <div class="flex items-center">
                  <i class="material-icons-outlined mr-3 text-gray-400 text-xl">unarchive</i>
                  <div>Restore</div>
                </div>
              </app-dropdown-link>
<!--              <app-dropdown-link as="button" @click="$inertia.delete($route('members.destroy.force', member.id))">-->
<!--                <div class="flex items-center">-->
<!--                  <i class="material-icons-outlined mr-3 text-gray-400 text-xl">delete</i>-->
<!--                  <div>Delete</div>-->
<!--                </div>-->
<!--              </app-dropdown-link>-->
            </template>
          </app-dropdown>
        </td>
      </tr>

      <tr v-if="members.total === 0">
        <td class="border-t px-6 py-10 text-center text-gray-500" colspan="6">No members found.</td>
      </tr>

      </tbody>
    </table>

    <!-- Pagination -->
    <div v-if="members.total > members.per_page" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
      <app-pagination :response="members"/>
    </div>
  </div>
</template>

<script>
import AppDropdown from "@/Components/UI/Dropdown";
import AppDropdownLink from "@/Components/UI/DropdownLink";
import AppPagination from "@/Components/App/Pagination";
import AppBadge from "@/Components/UI/Badge";

export default {
  components: {
    AppBadge,
    AppPagination,
    AppDropdownLink,
    AppDropdown
  },

  emits: ['edit-member'],

  props: {
    members: Object
  },

  methods: {
    duplicateMember(member) {
      ['id', 'created_at', 'updated_at', 'deleted_at'].forEach(field => delete member[field]);

      this.$inertia.post(this.$route('members.store'), member)
    }
  }
}
</script>
